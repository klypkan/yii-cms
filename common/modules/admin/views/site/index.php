<?php
use yii\data\ActiveDataProvider;
use common\modules\admin\components\GridView;
use yii\helpers\Html;
use common\modules\admin\Module;
use common\models\Site;

/* @var $this yii\web\View */

$this->title = Module::t('app', 'Sites');
$model = new Site();
$model->scenario = Site::SCENARIO_SEARCH;
?>
    <h1><?= Html::encode($this->title) ?></h1>
<?php
echo GridView::widget([
    'model' => $model,
    'columns' => [
        [
            'attribute' => 'name',
            'label' => Module::t('app', 'Name'),
            'format' => 'text',
        ],
        [
            'attribute' => 'url',
            'format' => 'text'
        ],
        [
            'attribute' => 'language',
            'label' => Module::t('app', 'Language'),
            'format' => 'text',
        ],
    ],
    'defaultOrder' => [
        'name' => SORT_ASC,
    ]
]);

