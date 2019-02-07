<?php

namespace common\models;

use yii\db\ActiveRecord;

/**
 * Permalink model
 *
 * @property integer $id
 * @property string $name
 * @property string $route
 * @property integer $site_id
 */
class Permalink extends ActiveRecord
{
    public static function tableName()
    {
        return '{{permalinks}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            ['name', 'unique', 'targetAttribute' => ['name','site_id']],
        ];
    }
}