<?php

namespace common\models;

use yii\db\ActiveRecord;
use common\components\LogBehavior;

/**
 * MenuItem model
 *
 * @property integer $id
 * @property string $name
 * @property integer $type
 * @property integer $value
 * @property integer $menu_item_order
 * @property integer $parent_id
 * @property integer $menu_id
 */
class MenuItem extends ActiveRecord
{
    const TYPE_PAGE = 0;
    const TYPE_POST = 1;
    const TYPE_CATEGORY = 2;
    const TYPE_TAG = 3;
    const TYPE_ROUTE = 4;
    const TYPE_URL = 5;
    const TYPE_SUB_MENU = 6;

    public static function tableName()
    {
        return '{{menu_items}}';
    }

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            LogBehavior::className(),
        ];
    }
}