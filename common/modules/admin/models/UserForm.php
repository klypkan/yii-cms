<?php
namespace common\modules\admin\models;

use Yii;
use yii\base\Model;
use common\modules\admin\Module;


class UserForm extends Model
{
    public $username;
    public $email;
    public $password;
    public $roles = [];

    /**
     * @return array the validation rules.
     */
    public function rules()
    {
        return [
            ['username', 'required'],
            ['password', 'required'],
            ['password', 'string', 'length' => [6]],
            ['email', 'required'],
            ['email', 'email'],
            ['roles', 'safe'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'username' => Module::t('app', 'Name'),
            'password' => Module::t('app', 'Password')
        ];
    }
} 