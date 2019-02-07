<?php
namespace common\modules\admin\helpers;

use Yii;
use common\models\Site;

class SiteAdminHelper
{
    static function getAll()
    {
        $roleSites = array();
        $authManager = Yii::$app->getAuthManager();
        $roles = $authManager->getRolesByUser(Yii::$app->user->id);
        foreach ($roles as $role) {
            $roleData = json_decode($role->data, true);
            $roleSites = array_merge($roleSites, $roleData["sites"]);
        }

        $sites = array();
        $sitesDb = Site::find()->where(['id' => $roleSites])->orderBy('name')->all();
        foreach ($sitesDb as $site) {
            $sites[] = ['id' => $site->id, 'name' => $site->name];
        }
        return $sites;
    }
}