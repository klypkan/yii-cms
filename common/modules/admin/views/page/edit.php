<?php
use common\modules\admin\Module;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\modules\admin\models\PageForm */
/* @var $form ActiveForm */
$this->title = Module::t('app', 'Editing the Page');
?>
<h1><?= Html::encode($this->title) ?></h1>
<?php $form = ActiveForm::begin(['enableClientScript' => false]); ?>
<?= $form->errorSummary($model, ['class' => 'alert alert-danger']); ?>
<?= $form->field($model, 'title')->textInput(['id' => 'title']) ?>
<?= $form->field($model, 'permalink_name')->textInput(['id' => 'permalink_name']) ?>
<?= $form->field($model, 'permalink_route')->hiddenInput(['id' => 'permalink_route'])->label(false) ?>
<?= $form->field($model, 'content')->textarea(['id' => 'content']) ?>
<?= $form->field($model, 'status')->hiddenInput()->label(false) ?>
<?= $form->field($model, 'site_id')->hiddenInput(['id' => 'site_id'])->label(false) ?>
<div class="form-group">
    <div class="panel panel-default post-category-panel">
        <div class="panel-heading">SEO</div>
        <div class="panel-body">
            <?= $form->field($model, 'seo_title') ?>
            <?= $form->field($model, 'seo_description')->textarea() ?>
        </div>
    </div>
</div>
<div class="form-group">
    <?= Html::submitButton(Module::t('app', 'Save'), ['id' => 'save-btn', 'class' => 'btn btn-primary']) ?>
    <button id="cancel-btn" class="btn btn-default" type="button"
            data-return-url="<?= $returnUrl ?>"><?= Module::t('app', 'Cancel') ?></button>
</div>
<?php ActiveForm::end(); ?>

