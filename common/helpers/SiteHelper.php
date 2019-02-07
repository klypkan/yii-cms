<?php
namespace common\helpers;

use Yii;
use yii\base\BaseObject;
use common\models\Site;

class SiteHelper extends BaseObject implements SiteHelperInterface
{
    private $cacheKey = "site_cache";

    function getCurrentSite()
    {
        $site = null;
        $sites = $this->getSites();

        if (count($sites) == 1) {
            $site = array("id" => $sites[0]["id"], "url" => $sites[0]["url"], "language" => $sites[0]["language"]);
        } else {
            $absoluteUrl = Yii::$app->request->getAbsoluteUrl();
            foreach ($sites as $site) {
                if (strpos($absoluteUrl, $site["url"]) !== false) {
                    $site = array("id" => $site["id"], "url" => $site["url"], "language" => $site["language"]);
                    break;
                }
            }
        }
        return $site;
    }

    function clearCache()
    {
        Yii::$app->cache->delete($this->cacheKey);
    }

    private function getSites()
    {
        $sites = Yii::$app->cache->getOrSet($this->cacheKey, function () {
            $sites = Site::find()->orderBy('url DESC')->all();
            $siteAr = array();
            foreach ($sites as $site) {
                $siteAr [] = array("id" => $site->id, "url" => $site->url, "language" => $site->language);
            }
            return $siteAr;
        });
        return $sites;
    }
}