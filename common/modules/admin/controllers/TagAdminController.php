<?php

namespace common\modules\admin\controllers;

use Yii;
use yii\filters\AccessControl;
use common\modules\admin\rbac\Permission;
use common\modules\admin\helpers\ViewHelper;
use common\modules\admin\helpers\PermalinkHelper;
use common\models\PostMeta;
use common\modules\admin\models\TagForm;


class TagAdminController extends \yii\web\Controller
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
        $model = new TagForm();
        $postMeta = null;
        $returnUrl = ViewHelper::getEditReturnUrl();
        $request = Yii::$app->request;
        $post = $request->post();
        if ($model->load($post)) {
            if ($model->validate()) {
                if (!empty($id)) {
                    $postMeta = PostMeta::findOne($id);
                }
                if (empty($postMeta)) {
                    $postMeta = new PostMeta();
                    $postMeta->type = PostMeta::TYPE_TAG;
                    $postMeta->post_meta_order = PostMeta::find()->where(['type' => PostMeta::TYPE_TAG])->count() + 1;
                }
                $postMeta->name = $model->name;
                $postMeta->value = $model->value;
                $postMeta->description = $model->description;
                $postMeta->site_id = $model->site_id;

                try {
                    $postMeta->save();
                    return Yii::$app->response->redirect($returnUrl);
                } catch (\Exception $ex) {
                    $model->addError("*", $ex->getMessage());
                }
            }
        } else {
            if (!empty($id)) {
                $postMeta = PostMeta::findOne($id);
                if (!empty($postMeta)) {
                    $model->id = $postMeta->id;
                    $model->name = $postMeta->name;
                    $model->value = $postMeta->value;
                    $model->description = $postMeta->description;
                    $model->site_id = $postMeta->site_id;
                }
            }
            if (empty($model->site_id)) {
                $queryParams = $request->getQueryParams();
                $model->site_id = $queryParams["site_id"];
            }
        }

        return $this->render('edit', ['model' => $model, 'returnUrl' => $returnUrl]);
    }

    public function actionDelete()
    {
        $post = Yii::$app->request->post();
        foreach ($post['idItems'] as $id) {
            $model = PostMeta::findOne($id);
            if (!empty($model)) {
                $model->delete();
            }
        }
    }

    public function actionCreateSlug()
    {
        $name = Yii::$app->request->post('name');
        $site_id = Yii::$app->request->post('site_id');
        if (!empty($name) && !empty($site_id)) {
            return PermalinkHelper::createSlug($name);
        }
        return "";
    }
}
