<?php

namespace common\modules\admin\controllers;

use Yii;
use yii\filters\AccessControl;
use common\modules\admin\Module;
use common\modules\admin\rbac\Permission;
use common\modules\admin\helpers\ViewHelper;
use common\models\Menu;
use common\models\MenuItem;
use common\modules\admin\models\MenuItemForm;
use common\models\Post;
use common\models\PostMeta;
use common\modules\admin\models\MenuForm;


class MenuController extends \yii\web\Controller
{
    private $menuHelper;

    public function __construct($id, $module, \common\helpers\MenuHelperInterface $menuHelper, $config = [])
    {
        $this->menuHelper = $menuHelper;
        parent::__construct($id, $module, $config);
    }

    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => [Permission::MANAGE_MENUS],
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
        $model = new MenuForm();
        $menu = null;
        $returnUrl = ViewHelper::getEditReturnUrl();
        $request = Yii::$app->request;
        $post = $request->post();
        if ($model->load($post)) {
            if ($model->validate()) {
                if (!empty($id)) {
                    $menu = Menu::findOne($id);
                }

                $savedMenuItemList = array();
                if (empty($menu)) {
                    $menu = new Menu();
                } else {
                    $savedMenuItemList = MenuItem::find()->where(['menu_id' => $menu->id])->orderBy('menu_item_order')->all();
                }
                $menu->name = $model->name;
                $menu->site_id = $model->site_id;

                try {
                    $transaction = Menu::getDb()->beginTransaction();
                    try {
                        $menu->save();

                        $menuItemIdByTempId = array();
                        $modelMenuItemsIdList = array();
                        foreach ($model->menu_items as $index => $menu_item) {
                            $menuItem = null;
                            if (strpos($menu_item["id"], 'menu-item-new') === false) {
                                $menuItem = MenuItem::findOne($menu_item["id"]);
                            }
                            if (empty($menuItem)) {
                                $menuItem = new MenuItem();
                            }
                            $menuItem->name = $menu_item["name"];
                            $menuItem->type = $menu_item["type"];
                            $menuItem->value = $menu_item["value"];
                            $menuItem->menu_item_order = $index;
                            $menuItem->parent_id = $menu_item["parent_id"];
                            if (strpos($menuItem->parent_id, 'menu-item-new') !== false) {
                                $menuItem->parent_id = $menuItemIdByTempId[$menu_item["parent_id"]];
                            }
                            $menuItem->menu_id = $menu->id;
                            $menuItem->save();
                            if (strpos($menu_item["id"], 'menu-item-new') !== false) {
                                $menuItemIdByTempId[$menu_item["id"]] = $menuItem->id;
                            }
                            $modelMenuItemsIdList[] = $menuItem->id;
                        }

                        foreach ($savedMenuItemList as $savedMenuItem) {
                            if (!in_array($savedMenuItem->id, $modelMenuItemsIdList)) {
                                $savedMenuItem->delete();
                            }
                        }

                        $transaction->commit();
                        $this->menuHelper->clearCache($menu->id);
                        return Yii::$app->response->redirect($returnUrl);
                    } catch (\Exception $ex) {
                        $transaction->rollBack();
                        throw $ex;
                    } catch (\Throwable $ex) {
                        $transaction->rollBack();
                        throw $ex;
                    }
                } catch (\Exception $ex) {
                    $model->addError("*", $ex->getMessage());
                }
            }
        } else {
            if (!empty($id)) {
                $menu = Menu::findOne($id);
                if (!empty($menu)) {
                    $model->name = $menu->name;
                    $model->site_id = $menu->site_id;

                    $menuItemList = MenuItem::find()->where(['menu_id' => $menu->id])->orderBy('menu_item_order')->all();
                    $menuItemMap = array();
                    foreach ($menuItemList as $menuItem) {
                        $menuItemMap[$menuItem->id] = $menuItem;
                    }
                    foreach ($menuItemList as $menuItem) {
                        $menuItemForm = new MenuItemForm();
                        $menuItemForm->id = $menuItem->id;
                        $menuItemForm->name = $menuItem->name;
                        $menuItemForm->type = $menuItem->type;
                        $menuItemForm->type_name = $this->getTypeName($menuItem->type);
                        $menuItemForm->value = $menuItem->value;
                        $menuItemForm->url = $this->getTypeUrl($menuItem);
                        $menuItemForm->parent_id = $menuItem->parent_id;
                        $menuItemForm->depth = $this->getDepth(0, $menuItem, $menuItemMap);
                        $model->menu_items[] = $menuItemForm;
                    }
                }
            }
            if (empty($model->site_id)) {
                $queryParams = $request->getQueryParams();
                $model->site_id = $queryParams["site_id"];
            }
        }

        return $this->render('edit', ['model' => $model, 'returnUrl' => $returnUrl]);
    }

    private function getTypeName($type)
    {
        switch ($type) {
            case MenuItem::TYPE_PAGE:
                return Module::t('app', 'Page');
            case MenuItem::TYPE_POST:
                return Module::t('app', 'Post');
            case MenuItem::TYPE_CATEGORY:
                return Module::t('app', 'Category');
            case MenuItem::TYPE_TAG:
                return Module::t('app', 'Tag');
            case MenuItem::TYPE_ROUTE:
                return Module::t('app', 'Route');
            case MenuItem::TYPE_URL:
                return Module::t('app', 'Url');
            case MenuItem::TYPE_SUB_MENU:
                return Module::t('app', 'Sub menu');
        }
        return "";
    }

    private function getTypeUrl($menuItem)
    {
        return "";
    }

    private function getDepth($depth, $menuItem, $menuItemMap)
    {
        if (!empty($menuItem->parent_id)) {
            return $this->getDepth($depth + 1, $menuItemMap[$menuItem->parent_id], $menuItemMap);
        }
        return $depth;
    }

    public function actionMenuItem()
    {
        $requestPost = Yii::$app->request->post();
        $menu_items = $requestPost['menu_items'];
        $content = "";
        foreach ($menu_items as $menu_item) {
            $model = new MenuItemForm();
            $model->id = uniqid('menu-item-new-');
            $model->name = $menu_item["name"];
            $model->type = $menu_item["type"];
            $model->type_name = $menu_item["type_name"];
            $model->value = $menu_item["value"];
            $model->url = $this->getTypeUrl($model);
            $model->parent_id = "";
            $model->depth = 0;
            $content = $content . $this->renderPartial('menu-item', ['model' => $model]);
        }
        return $content;
    }

    public function actionDelete()
    {
        $post = Yii::$app->request->post();
        foreach ($post['idItems'] as $id) {
            $model = Menu::findOne($id);
            if (!empty($model)) {
                $model->delete();
                $this->menuHelper->clearCache($id);
            }
        }
    }

    public function actionGetEntityList()
    {
        $requestPost = Yii::$app->request->post();
        $type = $requestPost['type'];
        $site_id = $requestPost['site_id'];
        $responseData = array();

        switch ($type) {
            case MenuItem::TYPE_PAGE:
                $pageList = Post::find()->where(['type' => Post::TYPE_PAGE, 'site_id' => $site_id])->orderBy('id DESC')->limit(10)->all();
                foreach ($pageList as $pageItem) {
                    $responseData[] = array('id' => $pageItem->id, 'name' => $pageItem->title);
                }
                break;
            case MenuItem::TYPE_POST:
                $postList = Post::find()->where(['type' => Post::TYPE_POST, 'site_id' => $site_id])->orderBy('id DESC')->limit(10)->all();
                foreach ($postList as $postItem) {
                    $responseData[] = array('id' => $postItem->id, 'name' => $postItem->title);
                }
                break;
            case MenuItem::TYPE_CATEGORY:
                $postMetaList = PostMeta::find()->where(['type' => PostMeta::TYPE_CATEGORY, 'site_id' => $site_id])->orderBy('id DESC')->limit(10)->all();
                foreach ($postMetaList as $postMetaItem) {
                    $responseData[] = array('id' => $postMetaItem->id, 'name' => $postMetaItem->name);
                }
                break;
            case MenuItem::TYPE_TAG:
                $postMetaList = PostMeta::find()->where(['type' => PostMeta::TYPE_TAG, 'site_id' => $site_id])->orderBy('id DESC')->limit(10)->all();
                foreach ($postMetaList as $postMetaItem) {
                    $responseData[] = array('id' => $postMetaItem->id, 'name' => $postMetaItem->name);
                }
                break;
        }

        $response = Yii::$app->response;
        $response->format = \yii\web\Response::FORMAT_JSON;
        $response->data = $responseData;
    }

    public function actionGetEntityListByName()
    {
        $requestPost = Yii::$app->request->post();
        $type = $requestPost['type'];
        $name = $requestPost['name'];
        $site_id = $requestPost['site_id'];
        $responseData = array();

        switch ($type) {
            case MenuItem::TYPE_PAGE:
                $pageList = Post::find()->where(['and', ['type' => Post::TYPE_PAGE, 'site_id' => $site_id], ['like', 'title', $name]])->orderBy('id DESC')->limit(100)->all();
                foreach ($pageList as $pageItem) {
                    $responseData[] = array('id' => $pageItem->id, 'name' => $pageItem->title);
                }
                break;
            case MenuItem::TYPE_POST:
                $postList = Post::find()->where(['type' => Post::TYPE_POST, 'site_id' => $site_id])->orderBy('id DESC')->limit(100)->all();
                foreach ($postList as $postItem) {
                    $responseData[] = array('id' => $postItem->id, 'name' => $postItem->title);
                }
                break;
            case MenuItem::TYPE_CATEGORY:
                $postMetaList = PostMeta::find()->where(['type' => PostMeta::TYPE_CATEGORY, 'site_id' => $site_id])->orderBy('id DESC')->limit(100)->all();
                foreach ($postMetaList as $postMetaItem) {
                    $responseData[] = array('id' => $postMetaItem->id, 'name' => $postMetaItem->name);
                }
                break;
            case MenuItem::TYPE_TAG:
                $postMetaList = PostMeta::find()->where(['type' => PostMeta::TYPE_TAG, 'site_id' => $site_id])->orderBy('id DESC')->limit(100)->all();
                foreach ($postMetaList as $postMetaItem) {
                    $responseData[] = array('id' => $postMetaItem->id, 'name' => $postMetaItem->name);
                }
                break;
        }

        $response = Yii::$app->response;
        $response->format = \yii\web\Response::FORMAT_JSON;
        $response->data = $responseData;
    }
}
