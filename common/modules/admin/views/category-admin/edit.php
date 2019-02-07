<?php
use common\modules\admin\Module;
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use common\modules\admin\assets\CategoryEditAsset;

/* @var $this yii\web\View */
/* @var $model common\modules\admin\models\CategoryForm */
/* @var $form ActiveForm */

CategoryEditAsset::register($this);
$this->title = Module::t('app', 'Editing the Category');
?>
<h1><?= Html::encode($this->title) ?></h1>
<?php $form = ActiveForm::begin(); ?>
<?= $form->errorSummary($model, ['class' => 'alert alert-danger']); ?>
<?= $form->field($model, 'id')->hiddenInput()->label(false) ?>
<?= $form->field($model, 'name')->textInput(['id' => 'name']) ?>
<?= $form->field($model, 'value')->textInput(['id' => 'value']) ?>
<?= $form->field($model, 'description')->textarea() ?>
<?= $form->field($model, 'site_id')->hiddenInput(['id' => 'site_id'])->label(false) ?>
<div class="form-group">
    <?= Html::submitButton(Module::t('app', 'Save'), ['id' => 'save-btn', 'class' => 'btn btn-primary']) ?>
    <button id="cancel-btn" class="btn btn-default" type="button"
            data-return-url="<?= $returnUrl ?>"><?= Module::t('app', 'Cancel') ?></button>
</div>
<?php ActiveForm::end(); ?>

