<?php

namespace common\models;

use yii\db\ActiveRecord;
use common\components\LogBehavior;

/**
 * Menu model
 *
 * @property integer $id
 * @property string $name
 * @property integer $site_id
 */
class Menu extends ActiveRecord
{
    const SCENARIO_SEARCH = 'search';

    public static function tableName()
    {
        return '{{menus}}';
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
            [['name','site_id'], 'required'],
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