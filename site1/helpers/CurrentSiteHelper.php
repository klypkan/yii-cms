<?php
namespace site1\helpers;

use Yii;

class CurrentSiteHelper
{
    static function getRootUrl()
    {
        $rootUrl = "/";
        $siteHelper = Yii::$container->get("common\helpers\SiteHelperInterface");
        $currentSite = $siteHelper->getCurrentSite();
        if ($currentSite["language"] != Yii::$app->params["defaultLanguage"]) {
            $rootUrl .= $currentSite["language"];
        }
        return $rootUrl;
    }

}