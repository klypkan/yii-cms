<?php

namespace common\models;

use yii\db\ActiveRecord;

/**
 * PostMeta model
 *
 * @property integer $id
 * @property integer $post_meta_id
 * @property integer $post_id
 */
class PostMetaRelationship extends ActiveRecord
{
    public static function tableName()
    {
        return '{{post_meta_relationships}}';
    }
}