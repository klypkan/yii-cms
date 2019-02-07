<?php

namespace common\modules\admin\controllers;

use Yii;
use yii\filters\AccessControl;
use common\modules\admin\rbac\Permission;


class LogController extends \yii\web\Controller
{
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => [Permission::READ_LOG],
                    ],
                ],
            ],
        ];
    }

    public function actionIndex()
    {
        return $this->render('index');
    }

}
