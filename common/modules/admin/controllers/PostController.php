<?php

namespace common\modules\admin\controllers;

use Yii;
use yii\filters\AccessControl;
use common\modules\admin\Module;
use common\modules\admin\rbac\Permission;
use common\modules\admin\helpers\ViewHelper;
use common\modules\admin\helpers\PermalinkHelper;
use common\models\Post;
use common\models\PostMeta;
use common\models\PostMetaRelationship;
use common\modules\admin\models\PostForm;
use common\models\Permalink;


class PostController extends \yii\web\Controller
{
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => [Permission::MANAGE_POSTS],
                        'roleParams' => ['site_id' => Yii::$app->request->get('site_id')],
                    ],
                ],
            ],
        ];
    }

    public function actionIndex($site_id)
    {
        return $this->render('index', ['site_id' => $site_id]);
    }

    public function actionEdit($id = 0)
    {
        $model = new PostForm();
        $post = null;
        $returnUrl = ViewHelper::getEditReturnUrl();
        $request = Yii::$app->request;
        $requestPost = $request->post();
        if ($model->load($requestPost)) {
            if (!empty($id)) {
                $post = Post::findOne($id);
            }
            if (empty($post)) {
                $post = new Post();
                $post->date = date('Y-m-d H:i:s');
            }
            $post->type = Post::TYPE_POST;
            $post->title = $model->title;
            $post->content = $model->content;
            $post->status = $model->status;
            $post->site_id = $model->site_id;

            $isNewPermalink = false;
            if (!empty($post->permalink_id)) {
                $permalink = Permalink::findOne($post->permalink_id);
                if ($permalink->name != $model->permalink_name) {
                    $isNewPermalink = true;
                }
            } else {
                $isNewPermalink = true;
            }

            try {
                $isValid = $model->validate();
                if ($isNewPermalink) {
                    $permalink = new Permalink();
                    $permalink->name = $model->permalink_name;
                    $permalink->route = $model->permalink_route;
                    $permalink->site_id = $model->site_id;
                    $isValid = $isValid & $permalink->validate();
                    if ($permalink->hasErrors()) {
                        $model->addError("permalink_name", Module::t('app', 'The slug already exists'));
                    }
                }
                if ($isValid) {
                    $transaction = Permalink::getDb()->beginTransaction();
                    try {
                        if ($isNewPermalink) {
                            $permalink->save();
                            $post->permalink_id = $permalink->id;
                        }
                        $post->save();


                        $postMetaRelList = PostMetaRelationship::find()->where(['post_id' => $post->id])->all();
                        foreach ($postMetaRelList as $postMetaRelItem) {
                            $postMeta = PostMeta::findOne($postMetaRelItem->post_meta_id);
                            if ($postMeta->type == PostMeta::TYPE_CATEGORY) {
                                $index = array_search($postMetaRelItem->post_meta_id, $model->categories);
                                if ($index !== false) {
                                    unset($model->categories[$index]);
                                } else {
                                    $postMetaRelItem->delete();
                                }
                            } elseif ($postMeta->type == PostMeta::TYPE_TAG) {
                                $index = false;
                                foreach ($model->tags as $i => $tagItem) {
                                    if ($tagItem["id"] == $postMetaRelItem->post_meta_id) {
                                        $index = $i;
                                    }
                                }
                                if ($index !== false) {
                                    unset($model->tags[$index]);
                                } else {
                                    $postMetaRelItem->delete();
                                }
                            }
                        }

                        foreach ($model->categories as $catId) {
                            $postMetaRelationship = new PostMetaRelationship();
                            $postMetaRelationship->post_id = $post->id;
                            $postMetaRelationship->post_meta_id = $catId;
                            $postMetaRelationship->save();
                        }

                        foreach ($model->tags as $tagItem) {
                            $postMeta = new PostMeta();
                            $postMeta->type = PostMeta::TYPE_TAG;
                            $postMeta->post_meta_order = PostMeta::find()->where(['type' => PostMeta::TYPE_TAG, 'site_id' => $model->site_id])->count() + 1;
                            $postMeta->name = $tagItem["name"];
                            $postMeta->value = PermalinkHelper::createSlug($tagItem["name"]);
                            $postMeta->site_id = $model->site_id;
                            $postMeta->save();

                            $postMetaRelationship = new PostMetaRelationship();
                            $postMetaRelationship->post_id = $post->id;
                            $postMetaRelationship->post_meta_id = $postMeta->id;
                            $postMetaRelationship->save();
                        }

                        $postMetaSeoTitle = null;
                        $postMetaSeoDescription = null;
                        $postMetaList = PostMeta::find()->where(['type' => PostMeta::TYPE_META, 'parent_id' => $post->id])->all();
                        foreach ($postMetaList as $postMetaItem) {
                            switch ($postMetaItem->name) {
                                case PostMeta::TITLE_TAG:
                                    $postMetaSeoTitle = $postMetaItem;
                                    break;
                                case PostMeta::DESCRIPTION_TAG:
                                    $postMetaSeoDescription = $postMetaItem;
                                    break;
                            }
                        }

                        if ($postMetaSeoTitle != null || $model->seo_title) {
                            if ($postMetaSeoTitle == null) {
                                $postMetaSeoTitle = new PostMeta();
                                $postMetaSeoTitle->type = PostMeta::TYPE_META;
                                $postMetaSeoTitle->post_meta_order = 0;
                                $postMetaSeoTitle->name = PostMeta::TITLE_TAG;
                                $postMetaSeoTitle->parent_id = $post->id;
                                $postMetaSeoTitle->site_id = $model->site_id;
                            }
                            $postMetaSeoTitle->value = $model->seo_title;
                            $postMetaSeoTitle->save();
                        }

                        if ($postMetaSeoDescription != null || $model->seo_description) {
                            if ($postMetaSeoDescription == null) {
                                $postMetaSeoDescription = new PostMeta();
                                $postMetaSeoDescription->type = PostMeta::TYPE_META;
                                $postMetaSeoDescription->post_meta_order = 1;
                                $postMetaSeoDescription->name = PostMeta::DESCRIPTION_TAG;
                                $postMetaSeoDescription->parent_id = $post->id;
                                $postMetaSeoDescription->site_id = $model->site_id;
                            }
                            $postMetaSeoDescription->value = $model->seo_description;
                            $postMetaSeoDescription->save();
                        }

                        $transaction->commit();
                    } catch (\Exception $ex) {
                        $transaction->rollBack();
                        throw $ex;
                    } catch (\Throwable $ex) {
                        $transaction->rollBack();
                        throw $ex;
                    }
                    return Yii::$app->response->redirect($returnUrl);
                } else {
                    $this->setCategories($model->categories, $model, $model->site_id);
                }
            } catch (\Exception $ex) {
                $model->addError("*", $ex->getMessage());
                $this->setCategories($model->categories, $model, $model->site_id);
            }
        } else {
            $selectedCategories = array();
            if (!empty($id)) {
                $post = Post::findOne(['id' => $id, 'type' => Post::TYPE_POST]);
                if (!empty($post)) {
                    $model->title = $post->title;
                    $model->content = $post->content;
                    $model->status = $post->status;
                    $model->site_id = $post->site_id;
                    if (!empty($post->permalink_id)) {
                        $permalink = Permalink::findOne($post->permalink_id);
                        if (!empty($permalink)) {
                            $model->permalink_name = $permalink->name;
                            $model->permalink_route = $permalink->route;
                        }
                    }

                    $postMetaIdList = PostMetaRelationship::find()->where(['post_id' => $id])->select('post_meta_id');
                    $postMetaList = PostMeta::find()->where(['in', 'id', $postMetaIdList])->all();
                    foreach ($postMetaList as $postMetaItem) {
                        switch ($postMetaItem->type) {
                            case PostMeta::TYPE_CATEGORY:
                                $selectedCategories[] = $postMetaItem->id;
                                break;
                            case PostMeta::TYPE_TAG:
                                $model->tags[] = array("id" => $postMetaItem->id, "name" => $postMetaItem->name);
                                break;
                        }
                    }

                    $postMetaList = PostMeta::find()->where(['type' => PostMeta::TYPE_META, 'parent_id' => $id])->all();
                    foreach ($postMetaList as $postMetaItem) {
                        switch ($postMetaItem->name) {
                            case PostMeta::TITLE_TAG:
                                $model->seo_title = $postMetaItem->value;
                                break;
                            case PostMeta::DESCRIPTION_TAG:
                                $model->seo_description = $postMetaItem->value;
                                break;
                        }
                    }
                }
            }
            if (empty($model->site_id)) {
                $model->status = Post::STATUS_ACTIVE;
                $queryParams = $request->getQueryParams();
                $model->permalink_route = Yii::$app->params["postRoute"];
                $model->site_id = $queryParams["site_id"];
            }
            $this->setCategories($selectedCategories, $model, $model->site_id);
        }

        return $this->render('edit', ['model' => $model, 'returnUrl' => $returnUrl]);
    }

    private function setCategories($selectedCategories, $model, $siteId)
    {
        $model->categories = array();
        $categories = PostMeta::find()->where(['type' => PostMeta::TYPE_CATEGORY, 'site_id' => $siteId])->orderBy('name')->all();
        foreach ($categories as &$categoryItem) {
            $model->categories[] = array("id" => $categoryItem->id, "name" => $categoryItem->name, "selected" => in_array($categoryItem->id, $selectedCategories));
        }
    }

    public function actionDelete()
    {
        $requestPost = Yii::$app->request->post();
        foreach ($requestPost['idItems'] as $id) {
            $model = Post::findOne(['id' => $id, 'type' => Post::TYPE_POST]);
            if (!empty($model)) {
                $model->delete();
            }
        }
    }

    public function actionGetTags()
    {
        $requestPost = Yii::$app->request->post();
        $tags = $requestPost['tags'];
        $site_id = $requestPost['site_id'];
        $responseData = array();

        foreach ($tags as $tagName) {
            $postMeta = PostMeta::find()->where(['type' => PostMeta::TYPE_TAG, 'name' => $tagName, 'site_id' => $site_id])->one();
            if ($postMeta == null) {
                $responseData[] = array('id' => '', 'name' => $tagName);
            } else {
                $responseData[] = array('id' => $postMeta->id, 'name' => $tagName);
            }
        }

        $response = Yii::$app->response;
        $response->format = \yii\web\Response::FORMAT_JSON;
        $response->data = $responseData;
    }
}
