<?php
namespace common\modules\admin\models;

use Yii;
use yii\base\Model;


class MenuItemForm extends Model
{
    public $id;
    public $name;
    public $type;
    public $type_name;
    public $value;
    public $url;
    public $parent_id;
    public $depth;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'name', 'type', 'type_name', 'value', 'depth'], 'required'],
            [['parent_id'], 'safe'],
        ];
    }
} 