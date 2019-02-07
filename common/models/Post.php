<?php

namespace common\models;

use yii\db\ActiveRecord;
use common\components\LogBehavior;

/**
 * Post model
 *
 * @property integer $id
 * @property integer $type
 * @property string $title
 * @property string $content
 * @property string $summary_content
 * @property string $summary_image
 * @property integer $status
 * @property integer $permalink_id
 * @property integer $parent_id
 * @property integer $site_id
 */
class Post extends ActiveRecord
{
    const TYPE_POST = 0;
    const TYPE_PAGE = 1;

    const STATUS_DELETED = 0;
    const STATUS_ACTIVE = 1;
    const STATUS_DRAFT = 2;

    const SCENARIO_SEARCH = 'search';

    public static function tableName()
    {
        return '{{posts}}';
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
    public function rules()
    {
        return [
            [['type', 'title', 'content','site_id'], 'required'],
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
        return $this->title;
    }
}