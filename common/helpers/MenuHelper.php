<?php
namespace common\helpers;

use Yii;
use yii\base\BaseObject;
use common\models\Menu;
use common\models\MenuItem;
use common\models\Permalink;
use common\models\Post;
use common\models\PostMeta;

class MenuHelper extends BaseObject implements MenuHelperInterface
{
    function get($id)
    {
        $cacheKey = $this->getCacheKey($id);
        $cashedMenu = Yii::$app->cache->getOrSet($cacheKey, function () use ($id) {
            $cashedMenu = array();
            $menu = Menu::find()->where(["id" => $id])->one();
            if ($menu != null) {
                $this->getMenuItems($cashedMenu, $menu->id, null);
            }
            return $cashedMenu;
        });
        return $cashedMenu;
    }


    function clearCache($id)
    {
        Yii::$app->cache->delete($this->getCacheKey($id));
    }

    private function getMenuItems(&$currentMenu, $menu_id, $parent_id)
    {
        $menuItemsTemp = MenuItem::find()->where(["menu_id" => $menu_id, "parent_id" => $parent_id])->orderBy("menu_item_order")->all();
        foreach ($menuItemsTemp as $menuItem) {
            $subMenuItems = array();
            $this->getMenuItems($subMenuItems, $menu_id, $menuItem->id);
            $menuItemValue = $menuItem->value;
            switch ($menuItem->type) {
                case MenuItem::TYPE_PAGE:
                    $page = Post::find()->where(["type" => Post::TYPE_PAGE, "id" => $menuItemValue])->one();
                    if ($page != null) {
                        $permalink = Permalink::find()->where(["id" => $page->permalink_id])->one();
                        $menuItemValue = $permalink->name;
                    } else {
                        $menuItemValue = null;
                    }
                    break;
                case MenuItem::TYPE_POST:
                    $post = Post::find()->where(["type" => Post::TYPE_POST, "id" => $menuItemValue])->one();
                    if ($post != null) {
                        $permalink = Permalink::find()->where(["id" => $post->permalink_id])->one();
                        $menuItemValue = $permalink->name;
                    } else {
                        $menuItemValue = null;
                    }
                    break;
                case MenuItem::TYPE_CATEGORY:
                    $postMeta = PostMeta::find()->where(["id" => $menuItemValue])->one();
                    if ($postMeta != null) {
                        $menuItemValue = $postMeta->value;
                    } else {
                        $menuItemValue = null;
                    }
                    break;
                case MenuItem::TYPE_TAG:
                    $postMeta = PostMeta::find()->where(["id" => $menuItemValue])->one();
                    if ($postMeta != null) {
                        $menuItemValue = $postMeta->value;
                    } else {
                        $menuItemValue = null;
                    }
                    break;
            }
            $currentMenu[] = array("name" => $menuItem->name, "type" => $menuItem->type, "value" => $menuItemValue, "items" => $subMenuItems);
        }
    }

    private function getCacheKey($id)
    {
        return "menu_cache_" . $id;
    }
}