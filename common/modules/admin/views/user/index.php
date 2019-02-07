<?php
use yii\data\ActiveDataProvider;
use common\modules\admin\components\GridView;
use yii\helpers\Html;
use common\modules\admin\Module;
use common\models\User;

/* @var $this yii\web\View */

$this->title = Module::t('app', 'Users');
$model = new User();
$model->scenario = User::SCENARIO_SEARCH;
?>
    <h1><?= Html::encode($this->title) ?></h1>
<?php
echo GridView::widget([
    'model' => $model,
    'columns' => [
        [
            'attribute' => 'username',
            'label' => Module::t('app', 'Name'),
            'format' => 'text',
        ],
        [
            'attribute' => 'email',
            'format' => 'text'
        ],
    ],
    'defaultOrder' => [
        'username' => SORT_ASC,
    ]
]);

