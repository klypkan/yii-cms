<?php
use common\modules\admin\Module;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\modules\admin\models\UserForm */
/* @var $form ActiveForm */
$this->title = Module::t('app', 'Editing the User');
?>
<h1><?= Html::encode($this->title) ?></h1>
<?php $form = ActiveForm::begin(); ?>
<?= $form->errorSummary($model, ['class' => 'alert alert-danger']); ?>
<?= $form->field($model, 'username') ?>
<?= $form->field($model, 'email') ?>
<?= $form->field($model, 'password')->passwordInput() ?>
<div class="form-group">
    <label><?= Module::t('app', 'Roles') ?></label>
    <?php foreach ($model->roles as $i => $role): ?>
        <div class="checkbox">
            <label>
                <?= Html::checkbox('UserForm[roles][]', $role["selected"], ['value' => $role["name"]]) ?> <?= Module::t('app', $role["name"]) ?>
            </label>
        </div>
    <?php endforeach; ?>
</div
<div class="form-group">
    <?= Html::submitButton(Module::t('app', 'Save'), ['id' => 'save-btn', 'class' => 'btn btn-primary']) ?>
    <button id="cancel-btn" class="btn btn-default" type="button"
            data-return-url="<?= $returnUrl ?>"><?= Module::t('app', 'Cancel') ?></button>
</div>
<?php ActiveForm::end(); ?>
