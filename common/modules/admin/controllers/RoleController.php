<?php

namespace common\modules\admin\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\rbac\DbManager;
use yii\base\InvalidConfigException;
use yii\helpers\Url;
use common\modules\admin\rbac\Permission;
use common\modules\admin\helpers\ViewHelper;
use common\modules\admin\models\RoleForm;
use common\models\Site;

class RoleController extends \yii\web\Controller
{
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => [Permission::MANAGE_ROLES],
                    ],
                ],
            ],
        ];
    }

    public function actionIndex()
    {
        $editUrlParameters = [Yii::$app->controller->id . '/edit'];
        $deleteUrl = Url::to([Yii::$app->controller->id . '/delete']);

        $authManager = Yii::$app->getAuthManager();
        if (!$authManager instanceof DbManager) {
            throw new InvalidConfigException('You should configure "authManager" component to use database.');
        }
        $roles = $authManager->getRoles();

        return $this->render('index', ['roles' => $roles, 'editUrlParameters' => $editUrlParameters, 'deleteUrl' => $deleteUrl]);
    }

    public function actionEdit($id = "")
    {
        $authManager = Yii::$app->getAuthManager();
        $model = new RoleForm();
        $returnUrl = ViewHelper::getEditReturnUrl();
        $post = Yii::$app->request->post();
        if ($model->load($post)) {
            if ($model->validate()) {
                $selectedPermissions = $model->permissions;
                $this->setRolePermissions($model->permissions, $model);
                $selectedSites = $model->sites;
                $this->setRoleSites($model->sites, $model);
                $role = null;
                if (!empty($id)) {
                    $role = $authManager->getRole($id);
                } else {
                    $role = $authManager->getRole($model->name);
                }
                if (!empty($role)) {
                    $authManager->removeChildren($role);
                }
                if (empty($role)) {
                    $role = $authManager->createRole($model->name);
                    $authManager->add($role);
                }
                foreach ($selectedPermissions as $permissionName) {
                        $permission = $authManager->getPermission($permissionName);
                        if (empty($permission)) {
                            $permission = $authManager->createPermission($permissionName);
                            $authManager->add($permission);
                        }
                        $authManager->addChild($role, $permission);
                }
                $roleName = $role->name;
                $role->name = $model->name;
                $role->data = json_encode(array("sites" => $selectedSites));
                try {
                    $authManager->update($roleName, $role);
                    return Yii::$app->response->redirect($returnUrl);
                } catch (\Exception $ex) {
                    $model->addError("*", $ex->getMessage());
                }
            }
            else
            {
                $this->setRolePermissions($model->permissions, $model);
                $this->setRoleSites($model->sites, $model);
            }
        } else {
            $selectedSites = array();
            $selectedPermissions = array();
            if (!empty($id)) {
                $role = $authManager->getRole($id);
                if (!empty($role)) {
                    $model->name = $role->name;
                    $permissionsByRole = $authManager->getPermissionsByRole($id);
                    foreach ($permissionsByRole as $permissionsByRoleItem) {
                        $selectedPermissions[] = $permissionsByRoleItem->name;
                    }
                    if ($role->data != null) {
                        $roleData = json_decode($role->data, true);
                        $selectedSites = $roleData["sites"];

                    }
                }
            }
            $this->setRolePermissions($selectedPermissions, $model);
            $this->setRoleSites($selectedSites, $model);
        }

        return $this->render('edit', ['model' => $model, 'returnUrl' => $returnUrl]);
    }

    private function setRolePermissions($selectedPermissions, $model)
    {
        $model->permissions = array();
        foreach (Permission::getConstants() as $key => $value) {
            $model->permissions[] = ['name' => $value, 'selected' => in_array($value, $selectedPermissions)];
        }
    }

    private function setRoleSites($selectedSites, $model)
    {
        $model->sites = array();
        $sites = Site::find()->orderBy('name')->all();
        foreach ($sites as $site) {
            $model->sites[] = array('id' => $site->id, 'name' => $site->name, 'selected' => in_array($site->id, $selectedSites));
        }
    }

    public function actionDelete()
    {
        $authManager = Yii::$app->getAuthManager();
        $post = Yii::$app->request->post();
        foreach ($post['idItems'] as $roleName) {
            $role = $authManager->getRole($roleName);
            if (!empty($role)) {
                $authManager->remove($role);
            }
        }
    }
}
