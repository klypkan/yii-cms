<?php

namespace common\models;

use yii\db\ActiveRecord;
use common\components\LogBehavior;

/**
 * Comment model
 *
 * @property integer $id
 * @property integer $status
 * @property string $content
 * @property datetime $date
 * @property integer $user_id
 * @property integer $comment_parent_id
 * @property integer $parent_id
 */
class Comment extends ActiveRecord
{
    public static function tableName()
    {
        return '{{comments}}';
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