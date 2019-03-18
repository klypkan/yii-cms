<?php
namespace common\modules\admin\models;

use Yii;
use yii\base\Model;
use common\modules\admin\Module;


class PostForm extends Model
{
    public $title;
    public $date;
    public $permalink_name;
    public $permalink_route;
    public $thumbnail_image;
    public $content;
    public $status;
    public $site_id;
    public $categories = [];
    public $tags = [];
    public $seo_title;
    public $seo_description;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['title', 'date', 'permalink_name', 'permalink_route', 'content', 'status', 'site_id'], 'required'],
            [['thumbnail_image', 'categories', 'tags'], 'safe'],
            ['seo_title', 'string', 'length' => [0, 160]],
            ['seo_description', 'string', 'length' => [0, 300]],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'title' => Module::t('app', 'Title'),
            'date' => Module::t('app', 'Date'),
            'permalink_name' => Module::t('app', 'Slug'),
            'content' => Module::t('app', 'Content'),
            'seo_title' => Module::t('app', 'Title'),
            'seo_description' => Module::t('app', 'Description')
        ];
    }
} 