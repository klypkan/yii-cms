<?php

namespace common\models;

use Yii;
use yii\db\ActiveRecord;
use common\modules\admin\Module;
use common\components\LogBehavior;

/**
 * Site model
 *
 * @property integer $id
 * @property string $name
 * @property string $url
 * @property string $language
 * @property string $path
 */
class Site extends ActiveRecord
{
    const SCENARIO_SEARCH = 'search';

    public static function tableName()
    {
        return '{{sites}}';
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
            [['name', 'url', 'language', 'path'], 'required'],
            [['url'], 'unique'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'name' => Module::t('app', 'Name'),
            'language' => Module::t('app', 'Language'),
            'path' => Module::t('app', 'Path'),
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