<?php
use common\modules\admin\Module;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\modules\admin\models\RoleForm */
/* @var $form ActiveForm */

$this->title = Module::t('app', 'Editing the Role');
?>
<h1><?= Html::encode($this->title) ?></h1>
<?php $form = ActiveForm::begin(); ?>
<?= $form->errorSummary($model,  ['class' => 'alert alert-danger']); ?>
<?= $form->field($model, 'name') ?>
<div class="form-group">
    <label><?= Module::t('app', 'Permissions') ?></label>
    <?php foreach ($model->permissions as $i => $permission): ?>
        <div class="checkbox">
            <label>
                <?= Html::checkbox('RoleForm[permissions][]', $permission["selected"], ['value' => $permission["name"]]) ?> <?= Module::t('permission', $permission["name"]) ?>
            </label>
        </div>
    <?php endforeach; ?>
</div>

<div class="form-group">
    <label><?= Module::t('app', 'Sites') ?></label>
    <?php foreach ($model->sites as $i => $site): ?>
        <div class="checkbox">
            <label>
                <?= Html::checkbox('RoleForm[sites][]', $site["selected"], ['value' => $site["id"]]) ?> <?= $site["name"] ?>
            </label>
        </div>
    <?php endforeach; ?>
</div>

<div class="form-group">
    <?= Html::submitButton(Module::t('app', 'Save'), ['id' => 'save-btn', 'class' => 'btn btn-primary']) ?>
    <button id="cancel-btn" class="btn btn-default" type="button" data-return-url="<?= $returnUrl ?>"><?= Module::t('app', 'Cancel') ?></button>
</div>
<?php ActiveForm::end(); ?>

