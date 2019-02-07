<?php

namespace common\modules\admin\controllers;

use Yii;
use yii\filters\AccessControl;
use common\modules\admin\rbac\Permission;
use common\modules\admin\helpers\ViewHelper;
use common\models\User;
use common\modules\admin\models\UserForm;

class UserController extends \yii\web\Controller
{
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => [Permission::MANAGE_USERS],
                    ],
                ],
            ],
        ];
    }

    public function actionIndex()
    {
        return $this->render('index');
    }

    public function actionEdit($id = "")
    {
        $authManager = Yii::$app->getAuthManager();
        $model = new UserForm();
        $user = null;
        $returnUrl = ViewHelper::getEditReturnUrl();
        $post = Yii::$app->request->post();
        if ($model->load($post)) {
            if ($model->validate()) {
                if (!empty($id)) {
                    $user = User::findOne($id);
                }
                if (empty($user)) {
                    $user = new User();
                }
                $user->username = $model->username;
                $user->email = $model->email;
                $selectedRoles = $model->roles;
                $this->setRoles($model->roles, $model, $authManager);
                if ($user->validate()) {
                    if ($user->password_hash != $model->password) {
                        $user->setPassword($model->password);
                        $user->generateAuthKey();
                    }
                    try {
                        $user->save();
                        $authManager->revokeAll($user->getId());
                        foreach ($selectedRoles as $roleName) {
                            $role = $authManager->getRole($roleName);
                            if (!empty($role)) {
                                $authManager->assign($role, $user->getId());
                            }
                        }
                        return Yii::$app->response->redirect($returnUrl);
                    } catch (\Exception $ex) {
                        $model->addError("*", $ex->getMessage());
                    }
                } else {
                    $model->addErrors($user->errors);
                }
            } else {
                $this->setRoles($model->roles, $model, $authManager);
            }
        } else {
            $selectedRoles = array();
            if (!empty($id)) {
                $user = User::findOne($id);
                if (!empty($user)) {
                    $model->username = $user->username;
                    $model->email = $user->email;
                    $model->password = $user->password_hash;

                    $roles = $authManager->getRolesByUser($user->getId());
                    foreach ($roles as $role) {
                        $selectedRoles[] = $role->name;
                    }
                }
            }
            $this->setRoles($selectedRoles, $model, $authManager);
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
            $model = User::findOne($id);
            if (!empty($model)) {
                $model->delete();
            }
        }
    }
}
