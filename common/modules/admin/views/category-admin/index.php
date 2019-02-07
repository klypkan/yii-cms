<?php
use yii\data\ActiveDataProvider;
use common\modules\admin\components\GridView;
use yii\helpers\Html;
use common\modules\admin\Module;
use common\models\PostMeta;

/* @var $this yii\web\View */

$this->title = Module::t('app', 'Categories');
$model = new PostMeta();
$model->scenario = PostMeta::SCENARIO_SEARCH;
?>
    <h1><?= Html::encode($this->title) ?></h1>
<?php
echo GridView::widget([
    'model' => $model,
    'columns' => [
        [
            'attribute' => 'name',
            'label' => Module::t('app', 'Name'),
            'format' => 'text'
        ],
        [
            'attribute' => 'value',
            'label' => Module::t('app', 'Slug'),
            'format' => 'text'
        ],
    ],
    'filter' => [
        [
            'attribute' => 'name',
            'label' => Module::t('app', 'Name'),
            'operators' => ['NoSet', 'Equal', 'Like']
        ],
        [
            'attribute' => 'type',
            'label' => 'type',
            'operator' => 'Equal',
            'operators' => ['Equal'],
            'value' => PostMeta::TYPE_CATEGORY,
            'visible' => false
        ],
        [
            'attribute' => 'site_id',
            'label' => 'site_id',
            'operator' => 'Equal',
            'operators' => ['Equal'],
            'value' => $site_id,
            'visible' => false
        ],
    ],
    'defaultOrder' => [
        'name' => SORT_ASC,
    ]
]);

