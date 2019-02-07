<?php
use yii\data\ActiveDataProvider;
use common\modules\admin\components\GridView;
use yii\helpers\Html;
use common\modules\admin\Module;
use common\models\Menu;

/* @var $this yii\web\View */

$this->title = Module::t('app', 'Menus');
$model = new Menu();
$model->scenario = Menu::SCENARIO_SEARCH;
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
    ],
    'filter' => [
        [
            'attribute' => 'name',
            'label' => Module::t('app', 'Name'),
            'operators' => ['NoSet', 'Equal', 'Like']
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

