<?php

namespace common\models;

use yii\db\ActiveRecord;

/**
 * Option model
 *
 * @property integer $id
 * @property string $name
 * @property string $value
 * @property integer $site_id
 */
class Option extends ActiveRecord
{
    public static function tableName()
    {
        return '{{options}}';
    }
}