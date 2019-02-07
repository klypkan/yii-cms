<?php
namespace common\modules\admin\models;

use Yii;
use yii\base\Model;
use common\modules\admin\Module;
use common\modules\admin\validators\CategorySlugValidator;


class CategoryForm extends Model
{
    public $id;
    public $name;
    public $value;
    public $description;
    public $site_id;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name', 'value', 'site_id'], 'required'],
            ['value', CategorySlugValidator::className()],
            [['id', 'description'], 'safe'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'name' => Module::t('app', 'Name'),
            'value' => Module::t('app', 'Slug'),
            'description' => Module::t('app', 'Description')
        ];
    }
} 