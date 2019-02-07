<?php
namespace common\modules\admin\components;

use yii\base\BaseObject;

class FilterItem extends BaseObject
{
    public $attribute;

    public $label;

    public $operator;

    public $operators = [];

    public $value = null;

    public $valueHandler = null;

    public $readOnly = false;

    public $visible = true;

    public $errors = [];
} 