<?php

namespace common\modules\admin\controllers;

use Yii;
use yii\filters\AccessControl;
use common\modules\admin\rbac\Permission;
use common\models\Post;
use common\models\PostMeta;
use common\models\PostMetaRelationship;
use common\models\Permalink;
use common\modules\admin\helpers\PermalinkHelper;
use common\modules\admin\Module;


class ImportController extends \yii\web\Controller
{
    private $serverUploadDir = ".";
    private $defaultUploadDir = "/uploads/files/import/";

    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => [Permission::IMPORT_DATA],
                        'roleParams' => ['site_id' => Yii::$app->request->get('site_id')],
                    ],
                ],
            ],
        ];
    }

    public function actionIndex()
    {
        //return $this->render('index');
        return $this->redirect(['import/wordpress', 'site_id' => Yii::$app->request->get('site_id')]);
    }

    public function actionWordpress()
    {
        return $this->render('wordpress');
    }

    public function actionWordpressLoad()
    {
        $post = Yii::$app->request->post();
        $doc = new \DOMDocument();
        $fileName = $post['file_name'];
        $postLoaded = 0;
        $pageLoaded = 0;
        if ($fileName) {
            try {
                $doc->load($this->serverUploadDir . $this->defaultUploadDir . $fileName);
                $categoryAr = $this->parseNodesToArray($doc, "category");
                $tagAr = $this->parseNodesToArray($doc, "tag");
                $base_site_url = $doc->getElementsByTagNameNS('http://wordpress.org/export/1.2/', "base_site_url")[0]->nodeValue;
                $itemAr = $this->parseItemsToArray($doc);
                foreach ($itemAr as $item) {
                    switch ($item["wp:post_type"]) {
                        case "post":
                            if ($this->loadPost($item, $categoryAr, $tagAr, $base_site_url)) {
                                $postLoaded++;
                            }
                            break;
                        case "page":
                            if ($this->loadPost($item, $categoryAr, $tagAr, $base_site_url)) {
                                $pageLoaded++;
                            }
                            break;
                    }
                }
            } catch (\Exception $ex) {
                Yii::$app->response->statusCode = 500;
                return $ex->getMessage();
            }
        }

        $responseData = array("postLoaded" => $postLoaded, "pageLoaded" => $pageLoaded);
        $response = Yii::$app->response;
        $response->format = \yii\web\Response::FORMAT_JSON;
        $response->data = $responseData;
    }

    public function actionUploadFile()
    {
        try {
            $fileName = basename($_FILES["upload"]["name"]);

            if ($this->getExtension($fileName) != "xml") {
                throw new \InvalidArgumentException(Module::t('error', 'The file extension does not allow'));
            } else {
                $target_file = $this->serverUploadDir . $this->defaultUploadDir . $fileName;
                if (file_exists($target_file)) {
                    return $fileName;
                }

                if (move_uploaded_file($_FILES["upload"]["tmp_name"], $target_file)) {
                    return $fileName;
                } else {
                    throw new \InvalidArgumentException('name');
                }
            }
        } catch (\Exception $ex) {
            Yii::$app->response->statusCode = 500;
            return $ex->getMessage();
        }
    }

    private function loadPost($item, $categoryAr, $tagAr, $base_site_url)
    {
        if ($item["wp:status"] == "publish") {
            $post = new Post();
            $post->type = ($item["wp:post_type"] == "page" ? Post::TYPE_PAGE : Post::TYPE_POST);
            $post->date = date($item["wp:post_date"]);
            $post->status = Post::STATUS_ACTIVE;
            $post->site_id = Yii::$app->request->get('site_id');
            $post->title = $item["title"];
            $post->content = $item["content:encoded"];

            $permalink_name = $this->convertLinkToPermalinkName($item["link"], $base_site_url);

            $transaction = Permalink::getDb()->beginTransaction();
            try {
                if ($permalink_name) {
                    $this->loadPermalink($post, $permalink_name);
                } else {
                    $this->loadPermalink($post, PermalinkHelper::createSlug($post->title));
                }

                if (Post::find()->where(['permalink_id' => $post->permalink_id, 'site_id' => $post->site_id])->one() != null) {
                    return false;
                }

                if ($post->validate()) {
                    $post->save();
                } else {
                    return false;
                }

                if (array_key_exists("category", $item)) {
                    foreach ($item["category"] as $catItem) {
                        switch ($catItem["domain"]) {
                            case "category":
                                $this->loadCategory($post, $categoryAr, $catItem["nicename"]);
                                break;
                            case "post_tag":
                                $this->loadTag($post, $tagAr, $catItem["nicename"]);
                                break;
                        }
                    }
                }

                $transaction->commit();
                return true;
            } catch (\Exception $ex) {
                $transaction->rollBack();
                throw $ex;
            } catch (\Throwable $ex) {
                $transaction->rollBack();
                throw $ex;
            }
        }
        return false;
    }


    private function loadPermalink($entity, $permalink_name)
    {
        $permalink = Permalink::find()->where(['name' => $permalink_name, 'site_id' => $entity->site_id])->one();
        if ($permalink == null) {
            $permalink = new Permalink();
            $permalink->name = $permalink_name;
            $permalink->route = Yii::$app->params["postRoute"];
            $permalink->site_id = $entity->site_id;

            $permalink->save();
        }
        $entity->permalink_id = $permalink->id;
    }

    private function loadCategory($post, $categoryAr, $slug)
    {
        $postMeta = PostMeta::find()->where(['type' => PostMeta::TYPE_CATEGORY, 'value' => $slug, 'site_id' => $post->site_id])->one();
        if ($postMeta == null) {
            foreach ($categoryAr as $catItem) {
                if ($catItem["wp:category_nicename"] == $slug) {
                    $postMeta = new PostMeta();
                    $postMeta->type = PostMeta::TYPE_CATEGORY;
                    $postMeta->post_meta_order = PostMeta::find()->where(['type' => PostMeta::TYPE_CATEGORY, 'site_id' => $post->site_id])->count() + 1;
                    $postMeta->name = $catItem["wp:cat_name"];
                    $postMeta->value = $slug;
                    if (array_key_exists("wp:category_description", $catItem)) {
                        $postMeta->description = $catItem["wp:category_description"];
                    }
                    $postMeta->site_id = $post->site_id;
                    $postMeta->save();
                    break;
                }
            }
        }

        if ($postMeta != null) {
            $postMetaRelationship = new PostMetaRelationship();
            $postMetaRelationship->post_id = $post->id;
            $postMetaRelationship->post_meta_id = $postMeta->id;
            $postMetaRelationship->save();
        }
    }

    private function loadTag($post, $tagAr, $slug)
    {
        $postMeta = PostMeta::find()->where(['type' => PostMeta::TYPE_TAG, 'value' => $slug, 'site_id' => $post->site_id])->one();
        if ($postMeta == null) {
            foreach ($tagAr as $tagItem) {
                if ($tagItem["wp:tag_slug"] == $slug) {
                    $postMeta = new PostMeta();
                    $postMeta->type = PostMeta::TYPE_TAG;
                    $postMeta->post_meta_order = PostMeta::find()->where(['type' => PostMeta::TYPE_TAG, 'site_id' => $post->site_id])->count() + 1;
                    $postMeta->name = $tagItem["wp:tag_name"];
                    $postMeta->value = $slug;
                    if (array_key_exists("wp:tag_description", $tagItem)) {
                        $postMeta->description = $tagItem["wp:tag_description"];
                    }
                    $postMeta->site_id = $post->site_id;
                    $postMeta->save();
                    break;
                }
            }
        }

        if ($postMeta != null) {
            $postMetaRelationship = new PostMetaRelationship();
            $postMetaRelationship->post_id = $post->id;
            $postMetaRelationship->post_meta_id = $postMeta->id;
            $postMetaRelationship->save();
        }
    }

    private function parseItemsToArray($doc)
    {
        $itemAr = array();
        $nodeList = $doc->getElementsByTagName("item");
        foreach ($nodeList as $nodeItem) {
            $item = array();
            foreach ($nodeItem->childNodes as $nodeChildItem) {
                if ($nodeChildItem->nodeType == 1) {
                    if ($nodeChildItem->nodeName == "category") {
                        if (!array_key_exists($nodeChildItem->nodeName, $item)) {
                            $item[$nodeChildItem->nodeName] = array();
                        }
                        $categoryAr = array();
                        foreach ($nodeChildItem->attributes as $attrNode) {
                            $categoryAr[$attrNode->nodeName] = $attrNode->nodeValue;
                        }
                        $categoryAr["value"] = $nodeChildItem->nodeValue;
                        $item[$nodeChildItem->nodeName][] = $categoryAr;
                    } else {
                        $item[$nodeChildItem->nodeName] = $nodeChildItem->nodeValue;
                    }
                }
            }
            $itemAr[] = $item;
        }
        return $itemAr;
    }

    private function parseNodesToArray($doc, $tagName)
    {
        $nodeAr = array();
        $nodeList = $doc->getElementsByTagNameNS('http://wordpress.org/export/1.2/', $tagName);
        foreach ($nodeList as $nodeItem) {
            $item = array();
            foreach ($nodeItem->childNodes as $nodeChildItem) {
                if ($nodeChildItem->nodeType == 1) {
                    $item[$nodeChildItem->nodeName] = $nodeChildItem->nodeValue;
                }
            }
            $nodeAr[] = $item;
        }
        return $nodeAr;
    }

    private function convertLinkToPermalinkName($link, $base_site_url)
    {
        $link = str_replace($base_site_url, "", $link);
        if ($link == "/") {
            return "/";
        }
        $linkAr = explode("/", $link);
        for ($i = count($linkAr) - 1; $i >= 0; $i--) {
            $slug = $linkAr[$i];
            if (!empty($slug) && strpos($slug, "?") === false) {
                return $slug;
            }
        }
        return "";
    }

    private function getExtension($fileName)
    {
        $info = new \SplFileInfo($fileName);
        return $info->getExtension();
    }
}
