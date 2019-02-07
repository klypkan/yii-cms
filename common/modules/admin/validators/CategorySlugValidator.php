<?php
namespace common\modules\admin\validators;

use yii\validators\Validator;
use common\modules\admin\Module;
use common\models\PostMeta;

class CategorySlugValidator extends Validator
{
    public function validateAttribute($model, $attribute)
    {
        $query = PostMeta::find()->where(['type' => PostMeta::TYPE_CATEGORY, 'site_id' => $model->site_id, 'value' => $model->$attribute]);
        if ($model->id) {
            $query->andWhere(['not', ['id' => $model->id]]);
        }
        if ($query->count() > 0) {
            $this->addError($model, $attribute, Module::t('app', 'The slug already exists'));
        }
    }
}