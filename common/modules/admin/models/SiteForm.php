<?php
namespace common\modules\admin\models;

use Yii;
use yii\base\Model;
use common\modules\admin\Module;


class SiteForm extends Model
{
    public $name;
    public $url;
    public $path;
    public $language;
    public $roles = [];

    /**
     * @return array the validation rules.
     */
    public function rules()
    {
        return [
            [['name', 'url', 'path', 'language'], 'required'],
            [['roles'], 'safe'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'name' => Yii::t('app', 'Name'),
            'language' => Yii::t('app', 'Language'),
        ];
    }
} 