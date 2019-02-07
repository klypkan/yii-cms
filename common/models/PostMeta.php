<?php

namespace common\models;

use yii\db\ActiveRecord;
use common\components\LogBehavior;

/**
 * PostMeta model
 *
 * @property integer $id
 * @property integer $type
 * @property string $name
 * @property string $value
 * @property string $description
 * @property string $post_meta_order
 * @property integer $parent_id
 * @property integer $site_id
 */
class PostMeta extends ActiveRecord
{
    const TYPE_META = 0;
    const TYPE_CATEGORY = 1;
    const TYPE_TAG = 2;

    const TITLE_TAG = "title";
    const DESCRIPTION_TAG = "description";

    const SCENARIO_SEARCH = 'search';

    public static function tableName()
    {
        return '{{post_meta}}';
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

    /**
     * @inheritdoc
     */
    public function scenarios()
    {
        $scenarios = parent::scenarios();
        $scenarios[self::SCENARIO_SEARCH] = [];
        return $scenarios;
    }

    public function __toString()
    {
        return $this->name;
    }
}