<?php

/* @var $this yii\web\View */

use yii\helpers\Html;
use yii\web\YiiAsset;
use common\modules\admin\assets\FileBrowserAsset;
use common\modules\admin\Module;

YiiAsset::register($this);
FileBrowserAsset::register($this);
$this->title = Module::t('app', 'File browser');
?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?= Html::csrfMetaTags() ?>
    <title><?= Html::encode($this->title) ?></title>
    <?php $this->head() ?>
</head>
<body>
<?php $this->beginBody() ?>
<div class="wrapper">
    <input id="upload-dir" type="hidden" value="<?=$uploadDir?>" />
    <input id="directory" type="hidden" value="<?=$uploadDir?>" />
    <div class="btn-toolbar" role="toolbar">
        <div class="btn-group">
            <span id="file-upload" class="btn btn-link"><?=Module::t('app', 'File upload')?></span>
            <input id="file" type="file" class="hidden" />
        </div>
        <div class="btn-group">
            <span class="btn btn-link" data-toggle="modal" data-target="#create-folder-form"><?=Module::t('app', 'Create folder')?></span>
        </div>
    </div>
    <ol id="breadcrumb" class="breadcrumb hidden"></ol>
    <div id="file-browser-content"><?=$this->render('browser-items',['fileBrowserList' => $fileBrowserList])?></div>
</div>
<div id="create-folder-form" class="modal fade bs-example-modal-sm" tabindex="-1" role="dialog" aria-labelledby="mySmallModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <div class="modal-body">
                <div class="form-group">
                    <label><?=Module::t('app', 'Name')?></label>
                    <input type="text" class="form-control" name="name" />
                </div>
            </div>
            <div class="modal-footer">
                <button id="create-folder" type="button" class="btn btn-primary"><?=Module::t('app', 'Create')?></button>
                <button type="button" class="btn btn-default" data-dismiss="modal"><?=Module::t('app', 'Close')?></button>
            </div>
        </div>
    </div>
</div>
<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>

