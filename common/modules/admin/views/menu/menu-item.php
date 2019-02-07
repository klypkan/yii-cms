<?php
use common\modules\admin\Module;
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\modules\admin\models\PageForm */

?>

<li data-name="MenuForm[menu_items]" data-depth="<?= $model->depth ?>" class="menu-item-depth-<?= $model->depth ?> menu-item menu-item-state-hidden">
    <input type="hidden" data-name="id" value="<?= $model->id ?>"/>
    <input type="hidden" data-name="type" value="<?= $model->type ?>"/>
    <input type="hidden" data-name="type_name" value="<?= $model->type_name ?>"/>
    <input type="hidden" data-name="value" value="<?= $model->value ?>"/>
    <input type="hidden" data-name="parent_id"  value="<?= $model->parent_id ?>"/>
    <div class="row">
        <div class="col-sm-12">
            <div class="menu-item-header">
                <div class="menu-item-header-text pull-left">
                    <?= $model->name ?>
                </div>
                <div class="pull-right">
                    <?= $model->type_name ?>
                    <button class="btn btn-link btn-down" type="button" data-toggle="collapse" data-target="#menu-item-<?=$model->id?>"><i class="fa fa-caret-down fa-lg"></i></button>
                    <button class="btn btn-link btn-up" type="button" data-toggle="collapse" data-target="#menu-item-<?=$model->id?>"><i class="fa fa-caret-up fa-lg"></i></button>
                </div>
            </div>
        </div>
    </div>
    <div id="menu-item-<?=$model->id?>" class="row collapse">
        <div class="col-sm-12">
            <div class="menu-item-content">
                <div class="form-group">
                    <input type="text" data-name="name" value="<?= $model->name ?>" class="form-control"/>
                </div>
                <div class="form-group menu-item-move-form-group menu-item-can-move hidden">
                    <?= Module::t('app', 'Move') ?>
                    <div class="btn-group" role="group">
                        <button type="button" class="btn btn-link menu-item-up-btn hidden"><?= Module::t('app', 'Up one') ?></button>
                        <button type="button" class="btn btn-link menu-item-down-btn hidden"><?= Module::t('app', 'Down one') ?></button>
                        <button type="button" class="btn btn-link menu-item-under-btn hidden"><?= Module::t('app', 'Under') ?></button>
                        <button type="button" class="btn btn-link menu-item-out-from-under-btn hidden"><?= Module::t('app', 'Out from under') ?></button>
                    </div>
                </div>
                <div class="btn-group" role="group">
                    <button type="button"
                            class="btn btn-link menu-item-remove-btn"><?= Module::t('app', 'Remove') ?></button>
                </div>
            </div>
        </div>
    </div>
</li>


