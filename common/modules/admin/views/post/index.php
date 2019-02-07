<?php
use common\modules\admin\components\GridView;
use yii\helpers\Html;
use common\modules\admin\Module;
use common\models\Post;

/* @var $this yii\web\View */

$this->title = Module::t('app', 'Posts');
$model = new Post();
$model->scenario = Post::SCENARIO_SEARCH;
?>
    <h1><?= Html::encode($this->title) ?></h1>
<?php
echo GridView::widget([
    'model' => $model,
    'columns' => [
        [
            'attribute' => 'title',
            'label' => Module::t('app', 'Title'),
            'format' => 'text'
        ],
    ],
    'filter' => [
        [
            'attribute' => 'title',
            'label' => Module::t('app', 'Title'),
            'operators' => ['NoSet', 'Equal', 'Like']
        ],
        [
            'attribute' => 'type',
            'label' => 'type',
            'operator' => 'Equal',
            'operators' => ['Equal'],
            'value' => Post::TYPE_POST,
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
        'title' => SORT_ASC,
    ]
]);

