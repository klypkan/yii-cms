<?php
namespace common\modules\admin;

use Yii;

class Module extends \yii\base\Module
{
    public function init()
    {
        parent::init();

        $this->layout = 'main';
        $this->controllerNamespace = 'common\modules\admin\controllers';
        Yii::$app->user->loginUrl= ['admin/default/login'];
        Yii::setAlias('@admin', '@common/modules/admin');
        $this->registerTranslations();
    }

    public function registerTranslations()
    {
        Yii::$app->i18n->translations['modules/admin/*'] = [
            'class' => 'yii\i18n\PhpMessageSource',
            'basePath' => '@admin/messages',
            'fileMap' => [
                'modules/admin/app' => 'app.php',
                'modules/admin/error' => 'error.php',
                'modules/admin/operator' => 'operator.php',
                'modules/admin/permission' => 'permission.php'
            ],
        ];
    }

    public static function t($category, $message, $params = [], $language = null)
    {
        return Yii::t('modules/admin/' . $category, $message, $params, $language);
    }
}
