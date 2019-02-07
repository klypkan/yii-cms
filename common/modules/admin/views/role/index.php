<?php
use common\modules\admin\Module;
use yii\helpers\Html;
use yii\helpers\Url;
use common\modules\admin\assets\GridViewAsset;

GridViewAsset::register($this);

/* @var $this yii\web\View */

$this->title = Module::t('app', 'Roles');
?>
<h1><?= Html::encode($this->title) ?></h1>
<div id="entity-list-error" class="alert alert-danger hidden" role="alert"></div>
<div class="btn-toolbar entity-list-toolbar" role="toolbar">
    <div class="btn-group">
        <button id="add-entity" class="btn btn-default" type="button" title="<?= Module::t('app', 'Create') ?>" data-edit-url="<?= Url::to($editUrlParameters) ?>">
            <i class="fa fa-plus"></i>
        </button>
    </div>
    <div class="btn-group">
        <button id="delete-entity" class="btn btn-default" type="button" title="<?= Module::t('app', 'Delete') ?>" data-delete-url="<?= $deleteUrl ?>">
            <i class="fa fa-remove"></i>
        </button>
    </div>
</div>
<table id="entity-list-table" class="table table-bordered">
    <tr>
        <th class="entity-list-table-header entity-list-table-header-selector">
            <input id="select-entity-list" type="checkbox"/>
        </th>
        <th class="entity-list-table-header">
            <?= Module::t('app', 'Name') ?>
        </th>
    </tr>
    <?php foreach ($roles as $role) {
        $cells = [];
        $cells[] = Html::tag('td', '<input type="checkbox" class="entity-selector" />', ['class' => 'entity-list-item-selector']);
        $cells[] = Html::tag('td', $role->name);
        $options = ['class' => 'entity-list-item entity-list-item-editable'];
        $options['data-key'] = $role->name;
        $controller = Yii::$app->controller;
        $editUrlParameters = [$controller->id . '/edit'];
        $editUrlParameters['id'] = $role->name;
        $editUrl = Url::to($editUrlParameters);
        $options['data-edit-url'] = Url::to($editUrl);
        echo Html::tag('tr', implode('', $cells), $options);
    }?>
</table>

