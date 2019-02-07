<?php
use common\modules\admin\Module;
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use common\models\MenuItem;
use yii\helpers\Url;
use common\modules\admin\helpers\ViewHelper;
use common\modules\admin\assets\MenuEditAsset;

/* @var $this yii\web\View */
/* @var $model common\modules\admin\models\MenuForm */
/* @var $form ActiveForm */

MenuEditAsset::register($this);
$this->title = Module::t('app', 'Editing the Menu');
?>
<h1><?= Html::encode($this->title) ?></h1>
<?php $form = ActiveForm::begin(); ?>
<div id="menu-items" class="hidden">

</div>
<div class="row">
    <div class="col-sm-12">
        <?= $form->errorSummary($model, ['class' => 'alert alert-danger']); ?>
        <?= $form->field($model, 'name') ?>
        <?= $form->field($model, 'site_id')->hiddenInput(['id' => 'site_id'])->label(false) ?>
    </div>
</div>
<div class="row">
    <div class="col-sm-4">
        <div class="form-group">
            <ul id="menu-item-list">
                <?php foreach ($model->menu_items as $i => $menuItem): ?>
                    <?=$this->render('menu-item', ['model' => $menuItem])?>
                <?php endforeach; ?>
            </ul>
        </div>
        <div id="menu-item-type-selector-group" class="form-group">
            <select id="menu-item-type-selector" class="form-control">
                <option value="<?= MenuItem::TYPE_PAGE ?>"><?= Module::t('app', 'Page') ?></option>
                <option value="<?= MenuItem::TYPE_POST ?>"><?= Module::t('app', 'Post') ?></option>
                <option value="<?= MenuItem::TYPE_CATEGORY ?>"><?= Module::t('app', 'Category') ?></option>
                <option value="<?= MenuItem::TYPE_TAG ?>"><?= Module::t('app', 'Tag') ?></option>
                <option value="<?= MenuItem::TYPE_ROUTE ?>"><?= Module::t('app', 'Route') ?></option>
                <option value="<?= MenuItem::TYPE_URL ?>"><?= Module::t('app', 'Url') ?></option>
                <option value="<?= MenuItem::TYPE_SUB_MENU ?>"><?= Module::t('app', 'Sub menu') ?></option>
            </select>
        </div>
        <div id="menu-item-entity-group" class="form-group">
            <ul class="nav nav-tabs" role="tablist">
                <li role="presentation" class="active">
                    <a href="#menu-item-entity-tab-recent"
                       aria-controls="menu-item-entity-tab-recent" role="tab"
                       data-toggle="tab"><?= Module::t('app', 'Recent') ?></a>
                </li>
                <li role="presentation">
                    <a href="#menu-item-entity-tab-search"
                       aria-controls="menu-item-entity-tab-search" role="tab"
                       data-toggle="tab"><?= Module::t('app', 'Search') ?></a>
                </li>
            </ul>
            <div class="tab-content">
                <div role="tabpanel" class="tab-pane active" id="menu-item-entity-tab-recent">
                    <ul id="menu-item-entity-list" class="menu-item-entity-list">
                        <li>
                            <div class="checkbox">
                                <label>
                                    <i class="fa fa-spinner fa-spin"></i>
                                </label>
                            </div>
                        </li>
                    </ul>
                </div>
                <div role="tabpanel" class="tab-pane" id="menu-item-entity-tab-search">
                    <div class="form-group">
                        <input id="menu-item-entity-search" type="text" class="form-control">
                    </div>
                    <ul id="menu-item-entity-list-search" class="menu-item-entity-list">
                    </ul>
                </div>
            </div>
        </div>
        <div id="menu-item-name-group" class="form-group hidden">
            <input id="menu-item-name" type="text" class="form-control"
                   placeholder="<?= Module::t('app', 'Navigation Label') ?>">
        </div>
        <div id="menu-item-url-group" class="form-group hidden">
            <input id="menu-item-url" type="text" class="form-control" placeholder="">
        </div>
        <div class="form-group">
            <button id="add-menu-item-btn" class="btn btn-default"
                    type="button"><?= Module::t('app', 'Add to Menu') ?></button>
        </div>
    </div>
    <div class="col-sm-10">

    </div>
</div>
<div class="row">
    <div class="col-sm-12">
        <div class="form-group">
            <?= Html::submitButton(Module::t('app', 'Save'), ['id' => 'save-btn', 'class' => 'btn btn-primary']) ?>
            <button id="cancel-btn" class="btn btn-default" type="button"
                    data-return-url="<?= $returnUrl ?>"><?= Module::t('app', 'Cancel') ?></button>
        </div>
    </div>
</div>
<?php ActiveForm::end(); ?>


