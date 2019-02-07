<?php
use yii\data\ActiveDataProvider;
use common\modules\admin\components\GridView;
use yii\helpers\Html;
use common\modules\admin\Module;
use common\models\Log;
use common\models\User;

/* @var $this yii\web\View */

$this->title = Module::t('app', 'Log');
$model = new Log();
$model->scenario = Log::SCENARIO_SEARCH;
?>
    <h1><?= Html::encode($this->title) ?></h1>
<?php
echo GridView::widget([
    'model' => $model,
    'editActionsEnabled' => false,
    'columns' => [
        [
            'attribute' => 'created_at',
            'label' => Module::t('app', 'Date'),
            'format' => function ($value) {
                return date("Y-m-d H:i:s", $value);
            },
        ],
        [
            'attribute' => 'event',
            'label' => Module::t('app', 'Event'),
            'format' => function ($value) {
                switch ($value) {
                    case Log::EVENT_INSERT:
                        return Module::t('app', 'Insert');
                    case Log::EVENT_UPDATE:
                        return Module::t('app', 'Update');
                        break;
                    case Log::EVENT_DELETE:
                        return Module::t('app', 'Delete');
                        break;
                    default:
                        return "";
                }
            },
        ],
        [
            'attribute' => 'source',
            'label' => Module::t('app', 'Source'),
            'format' => function ($value) {
                $source = json_decode($value, true);
                return 'type:' . $source['className'] . '<br>' . 'id:' . $source['id'];
            },
        ],
        [
            'attribute' => 'user_id',
            'label' => Module::t('app', 'User'),
            'format' => function ($value) {
                if ($value != null) {
                    $user = User::find()->where(['id' => $value])->one();
                    if ($user != null) {
                        return $user->username;
                    }
                }
                return $value;
            },
        ],
    ],
    'filter' => [
        [
            'attribute' => 'event',
            'label' => Yii::t('app', 'Event'),
            'operators' => ['NoSet', 'Equal', 'NotEqual'],
            'valueHandler' => function ($filterItem, $model) {
                $options = ['class' => 'form-control', 'data-name' => $filterItem->attribute];
                if ($filterItem->readOnly) {
                    $options["disabled"] = "";
                }
                if (!empty($filterItem->errors)) {
                    $options['title'] = implode(" ", $filterItem->errors);
                    $options['data-toggle'] = 'tooltip';
                }

                $selectOptions = array();
                for ($i = 0; $i <= 2; $i++) {
                    switch ($i) {
                        case Log::EVENT_INSERT:
                            $htmlOptions = array();
                            $htmlOptions['value'] = Log::EVENT_INSERT;
                            if ($filterItem->value == Log::EVENT_INSERT) {
                                $htmlOptions['selected'] = true;
                            }
                            $selectOptions[] = Html::tag('option', Module::t('app', 'Insert'), $htmlOptions);
                            break;
                        case Log::EVENT_UPDATE:
                            $htmlOptions = array();
                            $htmlOptions['value'] = Log::EVENT_UPDATE;
                            if ($filterItem->value == Log::EVENT_UPDATE) {
                                $htmlOptions['selected'] = true;
                            }
                            $selectOptions[] = Html::tag('option', Module::t('app', 'Update'), $htmlOptions);
                            break;
                        case Log::EVENT_DELETE:
                            $htmlOptions = array();
                            $htmlOptions['value'] = Log::EVENT_DELETE;
                            if ($filterItem->value == Log::EVENT_DELETE) {
                                $htmlOptions['selected'] = true;
                            }
                            $selectOptions[] = Html::tag('option', Module::t('app', 'Delete'), $htmlOptions);
                            break;
                    }
                }
                return Html::tag('select', implode('', $selectOptions), $options);
            },
        ],
    ],
    'defaultOrder' => [
        'created_at' => SORT_DESC,
    ]
]);

