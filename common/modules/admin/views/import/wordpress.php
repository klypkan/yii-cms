<?php

use yii\helpers\Html;
use yii\web\YiiAsset;
use common\modules\admin\assets\ImportWordpressAsset;
use common\modules\admin\Module;


/* @var $this yii\web\View */

YiiAsset::register($this);
ImportWordpressAsset::register($this);
$this->title = Module::t('app', 'Import data from WordPress');

?>
    <h1><?= Html::encode($this->title) ?></h1>
    <div id="import-wordpress-step1" class="row">
        <div class="col-sm-12">
            <button id="file-upload-btn" class="btn btn-default" type="button"><?= Module::t('app', 'File upload') ?></button>
            <input id="file" type="file" accept=".xml" class="hidden"/>
        </div>
    </div>
    <div id="import-wordpress-step2" class="row hidden">
        <div class="col-sm-12">
            <div class="checkbox">
                <label>
                    <input type="checkbox" name="item_types" value="post" checked> <?= Module::t('app', 'Posts') ?>
                </label>
            </div>
            <div class="checkbox">
                <label>
                    <input type="checkbox" name="item_types" value="page" checked> <?= Module::t('app', 'Pages') ?>
                </label>
            </div>
            <p>
                <button id="load-btn" class="btn btn-primary" type="button"><?= Module::t('app', 'Load') ?></button>
            </p>
        </div>
    </div>
    <div id="import-wordpress-step3" class="row hidden">
        <div class="col-sm-12">
            <div class="form-group">
                <strong><?= Module::t('app', 'Number of loaded') ?></strong><br>
                <?= Module::t('app', 'Posts') ?>:<span id="post-loaded-number"></span><br>
                <?= Module::t('app', 'Page') ?>:<span id="page-loaded-number"></span>
            </div>
            <div class="alert alert-info" role="alert">
                <?= Module::t('app', 'Copy images from the WordPress') ?>
            </div>
            <button id="renew-btn" class="btn btn-default" type="button"><?= Module::t('app', 'Renew') ?></button>
        </div>
    </div>
<?php

