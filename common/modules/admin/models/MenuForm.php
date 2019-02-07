<?php
namespace common\modules\admin\models;

use Yii;
use yii\base\Model;
use common\modules\admin\Module;


class MenuForm extends Model
{
    public $name;
    public $site_id;
    public $menu_items = [];

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name', 'site_id'], 'required'],
            [['menu_items'], 'safe'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'name' => Module::t('app', 'Name')
        ];
    }
} 