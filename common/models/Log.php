<?php

namespace common\models;

use yii\db\ActiveRecord;
use yii\behaviors\TimestampBehavior;

/**
 * Log model
 *
 * @property integer $id
 * @property integer $event
 * @property integer $created_at
 * @property string $source
 * @property string $message
 * @property integer $user_id
 */
class Log extends ActiveRecord
{
    const SCENARIO_SEARCH = 'search';

    const EVENT_INSERT = 0;
    const EVENT_UPDATE = 1;
    const EVENT_DELETE = 2;
    const EVENT_ERROR = 3;

    public static function tableName()
    {
        return '{{logs}}';
    }

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            [
                'class' => TimestampBehavior::className(),
                'attributes' => [
                    ActiveRecord::EVENT_BEFORE_INSERT => ['created_at'],
                ],
            ],
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
}