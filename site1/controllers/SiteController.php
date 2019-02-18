<?php
namespace site1\controllers;

use common\models\LoginForm;
use common\models\Permalink;
use common\models\Post;
use common\models\PostMeta;
use common\models\PostMetaRelationship;
use common\modules\admin\rbac\Permission;
use site1\helpers\PostHelper;
use site1\models\PasswordResetRequestForm;
use site1\models\PostVewModel;
use site1\models\ResetPasswordForm;
use site1\models\SignupForm;
use Yii;
use yii\base\InvalidParamException;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\helpers\Url;
use yii\web\BadRequestHttpException;
use yii\web\Controller;
use yii\web\HttpException;
use yii\web\NotFoundHttpException;

/**
 * Site controller
 */
class SiteController extends Controller
{
    private $currentSite;

    public function __construct($id, $module, \common\helpers\SiteHelperInterface $siteHelper, $config = [])
    {
        $this->currentSite = $siteHelper->getCurrentSite();
        parent::__construct($id, $module, $config);
    }

    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'only' => ['logout', 'signup'],
                'rules' => [
                    [
                        'actions' => ['signup'],
                        'allow' => true,
                        'roles' => ['?'],
                    ],
                    [
                        'actions' => ['logout'],
                        'allow' => true,
                        'roles' => ['@'],
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
        $exception = Yii::$app->errorHandler->exception;
        if ($exception !== null) {
            $name = $exception->getName();
            $code = "";
            if ($exception instanceof HttpException) {
                $code = $exception->statusCode;
            } else {
                $code = $exception->getCode();
            }
            $name .= " (#$code)";
            $renderParams = array('name' => $name, 'message' => $exception->getMessage(), 'exception' => $exception);
            $pathInfo = Yii::$app->request->getPathInfo();
            $pathAr = explode('/', $pathInfo);
            if ($pathAr[0] == 'admin' && Yii::$app->user->can(Permission::ACCESS_TO_ADMIN_DASHBOARD)) {
                Yii::$app->session->setFlash('renderParams', $renderParams);
                return Yii::$app->response->redirect(Url::to(['admin/default/error']));
            }
            return $this->render('error', $renderParams);
        }
    }

    /**
     * Displays homepage.
     *
     * @return mixed
     */
    public function actionIndex($lang, $page, $perPage)
    {
        $this->setLanguage($lang);

        $postVewModel = new PostVewModel();
        $postVewModel->title = "";
        $permalink = Permalink::find()
            ->where(['site_id' => $this->currentSite['id'], 'name' => '/'])
            ->one();
        if ($permalink != null) {
            $postModel = Post::find()
                ->where(['type' => Post::TYPE_PAGE, 'permalink_id' => $permalink->id, 'status' => Post::STATUS_ACTIVE])
                ->one();
            if ($postModel != null) {
                $postVewModel = $this->getPostViewModel($lang, $postModel);
            }
        }
        $postsAndCount = array('postList' => array(), 'totalCount' => 0);
        $category = PostMeta::find()
            ->where(['site_id' => $this->currentSite['id'], 'type' => PostMeta::TYPE_CATEGORY, 'value' => '/'])
            ->one();
        if ($category != null) {
            $postsAndCount = $this->getPostsAndCountByPostMetaId($lang, $category->id, $page, $perPage);
        }
        return $this->render('index', ['model' => $postVewModel, 'postList' => $postsAndCount['postList'], 'totalCount' => $postsAndCount['totalCount']]);
    }

    public function actionPage($lang, $slug)
    {
        $this->setLanguage($lang);

        $permalink = Permalink::find()
            ->where(['site_id' => $this->currentSite['id'], 'name' => $slug])
            ->one();
        if ($permalink != null) {
            if ($permalink->route === Yii::$app->params["postRoute"]) {
                $post = Post::find()
                    ->where(['permalink_id' => $permalink->id, 'status' => Post::STATUS_ACTIVE])
                    ->one();
                if ($post != null) {
                    return $this->handleActionPage($lang, $post);
                }
            }
        }
        throw new NotFoundHttpException(Yii::t('error', 'The requested page does not exist'));
    }

    public function actionCategory($lang, $slug, $page, $perPage)
    {
        $this->setLanguage($lang);

        $category = PostMeta::find()->where(['site_id' => $this->currentSite['id'], 'type' => PostMeta::TYPE_CATEGORY, 'value' => $slug])->one();
        if ($category == null) {
            throw new NotFoundHttpException(Yii::t('error', 'The requested page does not exist'));
        }
        $postsAndCount = $this->getPostsAndCountByPostMetaId($lang, $category->id, $page, $perPage);
        return $this->render('category', ['title' => $category->name, 'postList' => $postsAndCount['postList'], 'totalCount' => $postsAndCount['totalCount']]);
    }

    public function actionTag($lang, $slug, $page, $perPage)
    {
        $this->setLanguage($lang);

        $tag = PostMeta::find()->where(['site_id' => $this->currentSite['id'], 'type' => PostMeta::TYPE_TAG, 'value' => $slug])->one();
        if ($tag == null) {
            throw new NotFoundHttpException(Yii::t('error', 'The requested page does not exist'));
        }
        $postsAndCount = $this->getPostsAndCountByPostMetaId($lang, $tag->id, $page, $perPage);
        return $this->render('category', ['title' => $tag->name, 'postList' => $postsAndCount['postList'], 'totalCount' => $postsAndCount['totalCount']]);
    }

    private function handleActionPage($lang, $post)
    {
        return $this->render('post', ['model' => $this->getPostViewModel($lang, $post)]);
    }

    private function setLanguage($lang)
    {
        if ($this->currentSite['language'] != $lang) {
            throw new NotFoundHttpException(Yii::t('error', 'The requested page does not exist'));
        }
        Yii::$app->language = $lang;
    }

    private function getPostViewModel($lang, $post)
    {
        $postVewModel = new PostVewModel();
        $postVewModel->title = $post->title;
        if ($post->type == Post::TYPE_POST) {
            $postVewModel->date = PostHelper::convertDbDateToPostDate($post->date);
        }
        $postVewModel->content = $post->content;
        $postMetaList = PostMeta::find()
            ->where(['type' => PostMeta::TYPE_META, 'parent_id' => $post->id])
            ->all();

        foreach ($postMetaList as $postMetaItem) {
            switch ($postMetaItem->name) {
                case PostMeta::TITLE_TAG:
                    $postVewModel->title = $postMetaItem->value;
                    break;
                case PostMeta::DESCRIPTION_TAG:
                    $postVewModel->description = $postMetaItem->value;
                    break;
            }
        }

        $postMetaIdList = PostMetaRelationship::find()
            ->where(['post_id' => $post->id])
            ->select('post_meta_id');
        $postMetaList = PostMeta::find()
            ->where(['in', 'id', $postMetaIdList])
            ->all();
        $tags = array();
        $tagRoute = Yii::$app->params['tagRoute'];
        $rules = Yii::$app->urlManager->rules;
        foreach ($postMetaList as $postMetaItem) {
            if ($postMetaItem->type == PostMeta::TYPE_TAG) {
                $url = array();
                $url[] = $tagRoute;
                foreach ($rules as $ruleItem) {
                    if ($ruleItem->route == $tagRoute) {
                        $defaults = $ruleItem->defaults;
                        $url = array_merge($url, $defaults);
                        $url["lang"] = $lang;
                        $url["slug"] = $postMetaItem->value;
                        break;
                    }
                }
                $tags[] = array('name' => $postMetaItem->name, 'url' => $url);
            }
        }
        $postVewModel->tags = $tags;
        return $postVewModel;
    }


    private function getPostsAndCountByPostMetaId($lang, $postMetaId, $page, $perPage)
    {
        $date = new \DateTime('now', new \DateTimeZone('UTC'));
        $query = (new \yii\db\Query())
            ->from('post_meta_relationships')
            ->join('INNER JOIN', 'posts', 'posts.id = post_meta_relationships.post_id')
            ->where(['post_meta_relationships.post_meta_id' => $postMetaId, 'posts.status' => Post::STATUS_ACTIVE])
            ->andWhere(['<=', 'posts.date', $date->format('Y-m-d H:i:s')])
            ->select('post_meta_relationships.post_id')
            ->orderBy('posts.date DESC');
        $rows = $query
            ->limit($perPage)
            ->offset(($page - 1) * $perPage)
            ->all();
        $totalCount = $query->count();
        $postIdList = array();
        foreach ($rows as $rows) {
            $postIdList[] = $rows['post_id'];
        }
        $postVewModelList = array();
        if (count($postIdList) > 0) {
            $postList = Post::find()
                ->where(['id' => $postIdList])
                ->orderBy('id DESC')
                ->all();
            $postRoute = Yii::$app->params['postRoute'];
            foreach ($postList as $postItem) {
                $permalink = Permalink::find()->where(['id' => $postItem->permalink_id])->one();
                $postVewModel = new PostVewModel();
                $postVewModel->title = $postItem->title;
                $postVewModel->date = PostHelper::convertDbDateToPostDate($postItem->date);
                $postVewModel->content = PostHelper::trimWords($postItem->content);
                $url = array();
                $url[] = $postRoute;
                $url['lang'] = $lang;
                $url['slug'] = $permalink->name;
                $postVewModel->url = $url;
                $postVewModelList[] = $postVewModel;
            }
        }
        return array('postList' => $postVewModelList, 'totalCount' => $totalCount);
    }

    /**
     * Logs in a user.
     *
     * @return mixed
     */
    public function actionLogin($lang)
    {
        $this->setLanguage($lang);

        if (!Yii::$app->user->isGuest) {
            return $this->goHome();
        }

        $model = new LoginForm();
        if ($model->load(Yii::$app->request->post()) && $model->login()) {
            return $this->goBack();
        } else {
            $model->password = '';
            $passwordResetRoute = ($lang != Yii::$app->params['defaultLanguage'] ? $lang . '/' : '') . 'site/request-password-reset';
            return $this->render('login', [
                'model' => $model,
                'passwordResetRoute' => $passwordResetRoute
            ]);
        }
    }

    /**
     * Logs out the current user.
     *
     * @return mixed
     */
    public function actionLogout($lang)
    {
        $this->setLanguage($lang);

        Yii::$app->user->logout();
        return $this->goHome();
    }


    /**
     * Signs user up.
     *
     * @return mixed
     */
    public function actionSignup($lang)
    {
        $this->setLanguage($lang);

        $model = new SignupForm();
        if ($model->load(Yii::$app->request->post())) {
            if ($user = $model->signup()) {
                if (Yii::$app->getUser()->login($user)) {
                    return $this->goHome();
                }
            }
        }

        return $this->render('signup', [
            'model' => $model,
        ]);
    }

    /**
     * Requests password reset.
     *
     * @return mixed
     */
    public function actionRequestPasswordReset($lang)
    {
        $this->setLanguage($lang);

        $model = new PasswordResetRequestForm();
        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            if ($model->sendEmail($lang)) {
                Yii::$app->session->setFlash('success', 'Check your email for further instructions.');
                $route = ($lang != Yii::$app->params['defaultLanguage'] ? $lang . '/' : '') . 'site/login';
                return Yii::$app->response->redirect(Url::to([$route]));
            } else {
                Yii::$app->session->setFlash('error', 'Sorry, we are unable to reset password for the provided email address.');
            }
        }

        return $this->render('requestPasswordResetToken', [
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
    public function actionResetPassword($lang, $token)
    {
        $this->setLanguage($lang);

        try {
            $model = new ResetPasswordForm($token);
        } catch (InvalidParamException $e) {
            throw new BadRequestHttpException($e->getMessage());
        }

        if ($model->load(Yii::$app->request->post()) && $model->validate() && $model->resetPassword()) {
            Yii::$app->session->setFlash('success', 'New password saved.');
            $route = ($lang != Yii::$app->params['defaultLanguage'] ? $lang . '/' : '') . 'site/login';
            return Yii::$app->response->redirect(Url::to([$route]));
        }

        return $this->render('resetPassword', [
            'model' => $model,
        ]);
    }
}
