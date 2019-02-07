<?php

namespace common\modules\admin\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\helpers\Url;
use common\modules\admin\rbac\Permission;
use common\modules\admin\helpers\ViewHelper;
use common\models\Site;
use common\modules\admin\models\SiteForm;
use common\modules\admin\Module;


class SiteController extends \yii\web\Controller
{
    private $siteHelper;
    private $menuHelper;

    public function __construct($id, $module, \common\helpers\SiteHelperInterface $siteHelper, \common\helpers\MenuHelperInterface $menuHelper, $config = [])
    {
        $this->siteHelper = $siteHelper;
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
                        'roles' => [Permission::MANAGE_SITES],
                    ],
                ],
            ],
        ];
    }

    public function actionIndex()
    {
        return $this->render('index');
    }

    public function actionEdit($id = 0)
    {
        $authManager = Yii::$app->getAuthManager();
        $userCanManageRoles = Yii::$app->user->can(Permission::MANAGE_ROLES);
        $model = new SiteForm();
        $site = null;
        $returnUrl = ViewHelper::getEditReturnUrl();
        $post = Yii::$app->request->post();
        if ($model->load($post)) {
            if (!empty($id)) {
                $site = Site::findOne($id);
            }
            if (empty($site)) {
                $site = new Site();
            }
            $site->name = $model->name;
            $site->url = $model->url;
            $site->language = $model->language;
            $site->path = $model->path;
            $selectedRoles = $model->roles;
            $this->setRoles($model->roles, $model, $authManager);
            if ($userCanManageRoles && count($selectedRoles) == 0) {
                $model->addError("*", Module::t('error', 'Select at least one role'));
            }
            if (!$model->hasErrors() & $site->validate()) {
                try {
                    $site->save();
                    if ($userCanManageRoles) {
                        $roles = $authManager->getRoles();
                        foreach ($roles as $role) {
                            $roleData = json_decode($role->data, true);
                            if ($roleData == null) {
                                $roleData = array("sites" => array());
                            }
                            if (in_array($role->name, $selectedRoles)) {
                                if (!in_array($site->id, $roleData["sites"])) {
                                    $roleData["sites"][] = $site->id;
                                }
                            } else {
                                if (($key = array_search($site->id, $roleData["sites"])) !== false) {
                                    unset($roleData["sites"][$key]);
                                }
                            }
                            $role->data = json_encode($roleData);
                            $authManager->update($role->name, $role);
                        }
                    }
                    $this->siteHelper->clearCache();
                    return Yii::$app->response->redirect($returnUrl);
                } catch (\Exception $ex) {
                    $model->addError("*", $ex->getMessage());
                }
            } else {
                $model->addErrors($site->errors);
            }
        } else {
            $selectedRoles = array();
            if (!empty($id)) {
                $site = Site::findOne($id);
                if (!empty($site)) {
                    $model->name = $site->name;
                    $model->url = $site->url;
                    $model->language = $site->language;
                    $model->path = $site->path;

                    if ($userCanManageRoles) {
                        $roles = $authManager->getRoles();
                        foreach ($roles as $role) {
                            $roleData = json_decode($role->data, true);
                            if ($roleData != null && in_array($site->id, $roleData["sites"])) {
                                $selectedRoles[] = $role->name;
                            }
                        }
                    }
                }
            }
            if ($userCanManageRoles) {
                $this->setRoles($selectedRoles, $model, $authManager);
            }
        }

        return $this->render('edit', ['model' => $model, 'returnUrl' => $returnUrl]);
    }

    private function setRoles($selectedRoles, $model, $authManager)
    {
        $model->roles = array();
        $roles = $authManager->getRoles();
        foreach ($roles as $role) {
            $model->roles[] = array("name" => $role->name, "selected" => in_array($role->name, $selectedRoles));
        }
    }

    public function actionDelete()
    {
        $post = Yii::$app->request->post();
        foreach ($post['idItems'] as $id) {
            $model = Site::findOne($id);
            if (!empty($model)) {
                $model->delete();
            }
        }
        $this->siteHelper->clearCache();
    }
}
