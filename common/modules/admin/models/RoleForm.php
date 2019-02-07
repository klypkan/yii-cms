<?php

namespace common\modules\admin\models;

use Yii;
use yii\base\Model;

class RoleForm extends Model
{
    public $name;
    public $permissions = [];
    public $sites = [];

    /**
     * @return array the validation rules.
     */
    public function rules()
    {
        return [
            ['name', 'required'],
            ['permissions', 'safe'],
            ['sites', 'safe'],
        ];
    }
} 