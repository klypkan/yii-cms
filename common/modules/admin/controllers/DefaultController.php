<?php

namespace common\modules\admin\controllers;

use common\modules\admin\rbac\Permission;
use Yii;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\web\Controller;
use yii\helpers\Url;
use common\models\Permalink;
use common\modules\admin\helpers\PermalinkHelper;
use common\modules\admin\models\LoginForm;
use common\modules\admin\models\PasswordResetRequestForm;
use common\modules\admin\models\ResetPasswordForm;
use common\modules\admin\Module;

class DefaultController extends Controller
{
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'only' => ['index', 'logout', 'create-slug'],
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => [Permission::ACCESS_TO_ADMIN_DASHBOARD]
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'logout' => ['post'],
                ],
            ],
        ];
    }


    public function actionError()
    {
        $renderParams = Yii::$app->session->getFlash('renderParams');
        if ($renderParams != null) {
            return $this->render('error', $renderParams);
        }
    }

    public function actionIndex()
    {
        if (Yii::$app->user->can(Permission::READ_LOG)) {
            return Yii::$app->response->redirect(Url::to(['log/index']));
        }
        return $this->render('index');
    }

    public function actionLogin()
    {
        if (!Yii::$app->user->isGuest) {
            return Yii::$app->response->redirect(Url::to(['default/index']));
        }

        $model = new LoginForm();
        if ($model->load(Yii::$app->request->post()) && $model->login()) {
            return $this->goBack();
        } else {
            return $this->renderPartial('login', ['model' => $model]);
        }
    }

    public function actionLogout()
    {
        Yii::$app->user->logout();
        return $this->goHome();
    }

    /**
     * Requests password reset.
     *
     * @return mixed
     */
    public function actionRequestPasswordReset()
    {
        $model = new PasswordResetRequestForm();
        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            if ($model->sendEmail()) {
                Yii::$app->session->setFlash('success', Module::t('app', 'Check your email for further instructions'));
                return Yii::$app->response->redirect(Url::to(['default/index']));
            } else {
                $model->addError("*", Module::t('error', 'Sorry, we are unable to reset password'));
            }
        }

        return $this->renderPartial('requestPasswordResetToken', [
            'model' => $model,
        ]);
    }

    /**
     * Resets password.
     *
     * @param string $token
     * @return mixed
     * @throws BadRequestHttpException
     */
    public function actionResetPassword($token)
    {
        try {
            $model = new ResetPasswordForm($token);
            if ($model->load(Yii::$app->request->post()) && $model->validate() && $model->resetPassword()) {
                Yii::$app->session->setFlash('success', Module::t('app', 'New password saved'));
                return Yii::$app->response->redirect(Url::to(['default/index']));
            }
        } catch (InvalidParamException $ex) {
            $model->addError("*", Module::t('error', $ex->getMessage()));
        }

        return $this->renderPartial('resetPassword', [
            'model' => $model,
        ]);
    }

    public function actionCreateSlug()
    {
        $name = Yii::$app->request->post('name');
        $site_id = Yii::$app->request->post('site_id');
        if (!empty($name) && !empty($site_id)
        ) {
            $slug = PermalinkHelper::createSlug($name);
            $tempSlug = $slug;
            $i = 1;
            while (true) {
                $permalink = Permalink::find()
                    ->where(['name' => $tempSlug, 'site_id' => $site_id])
                    ->one();
                if ($permalink == null) {
                    break;
                }
                $tempSlug = $slug . "-" . $i;
                $i++;
            }
            return $tempSlug;
        }
        return "";
    }
}
