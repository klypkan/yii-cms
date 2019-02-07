<?php
namespace site1\helpers;

use Yii;
use common\models\Menu;
use common\models\MenuItem;

class SiteMenuHelper
{
    static function getTopMenu()
    {
        $top_menu_key = "top_menu";
        $siteHelper = Yii::$container->get("common\helpers\SiteHelperInterface");
        $currentSite = $siteHelper->getCurrentSite();
        $top_menu_key .= "_" . $currentSite["id"];

        $topMenu = array();
        $menu = Menu::find()->where(["site_id" => $currentSite["id"]])->orderBy("id")->one();
        if ($menu != null) {
            $menuHelper = Yii::$container->get("common\helpers\MenuHelperInterface");
            $menuItems = $menuHelper->get($menu->id);
            $lang = $currentSite["language"];
            SiteMenuHelper::getMenuItems($topMenu, $menuItems, $lang);
        }
        return $topMenu;
    }

    private static function getMenuItems(&$currentMenu, $menuItems, $lang)
    {
        $params = Yii::$app->params;
        $rules = Yii::$app->urlManager->rules;
        foreach ($menuItems as $menuItem) {
            $subMenuItems = array();
            SiteMenuHelper::getMenuItems($subMenuItems, $menuItem["items"], $lang);
            $label = $menuItem["name"];
            $url = array();
            switch ($menuItem["type"]) {
                case MenuItem::TYPE_PAGE:
                    if ($menuItem["value"] != null) {
                        $url[] = $params["postRoute"];
                        foreach ($rules as $ruleItem) {
                            if ($ruleItem->route == $params["postRoute"]) {
                                $defaults = $ruleItem->defaults;
                                $url = array_merge($url, $defaults);
                                $url["lang"] = $lang;
                                $url["slug"] = $menuItem["value"];
                                break;
                            }
                        }
                    }
                    break;
                case MenuItem::TYPE_POST:
                    if ($menuItem["value"] != null) {
                        $url[] = $params["postRoute"];
                        foreach ($rules as $ruleItem) {
                            if ($ruleItem->route == $params["postRoute"]) {
                                $defaults = $ruleItem->defaults;
                                $url = array_merge($url, $defaults);
                                $url["lang"] = $lang;
                                $url["slug"] = $menuItem["value"];
                                break;
                            }
                        }
                    }
                    break;
                case MenuItem::TYPE_CATEGORY:
                    if ($menuItem["value"] != null) {
                        $url[] = $params["categoryRoute"];
                        foreach ($rules as $ruleItem) {
                            if ($ruleItem->route == $params["categoryRoute"]) {
                                $defaults = $ruleItem->defaults;
                                $url = array_merge($url, $defaults);
                                $url["lang"] = $lang;
                                $url["slug"] = $menuItem["value"];
                                break;
                            }
                        }
                    }
                    break;
                case MenuItem::TYPE_TAG:
                    if ($menuItem["value"] != null) {
                        $url[] = $params["tagRoute"];
                        foreach ($rules as $ruleItem) {
                            if ($ruleItem->route == $params["tagRoute"]) {
                                $defaults = $ruleItem->defaults;
                                $url = array_merge($url, $defaults);
                                $url["lang"] = $lang;
                                $url["slug"] = $menuItem["value"];
                                break;
                            }
                        }
                    }
                    break;
                case MenuItem::TYPE_ROUTE:
                    $routeList = explode(",", $menuItem["value"]);
                    $routeListCount = count($routeList);
                    if ($routeListCount > 0) {
                        $url[] = $routeList[0];
                        if ($routeListCount > 1) {
                            for ($i = 1; $i < $routeListCount; $i++) {
                                $parameterList = explode("=>", trim($routeList[$i]));
                                if (count($parameterList) == 2) {
                                    $url[trim($parameterList[0])] = trim($parameterList[1]);
                                }
                            }
                        }
                    }
                    $url["lang"] = $lang;
                    break;
                case MenuItem::TYPE_URL:
                    $url = $menuItem["value"];
                    break;
                case MenuItem::TYPE_SUB_MENU:
                    $url = $menuItem["value"];
                    break;
            }
            $currentMenu[] = array("label" => $label, "url" => $url, "items" => $subMenuItems);
        }
    }
}