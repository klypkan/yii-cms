<?php
use common\modules\admin\Module;
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use common\modules\admin\assets\PostEditAsset;

/* @var $this yii\web\View */
/* @var $model common\modules\admin\models\PostForm */
/* @var $form ActiveForm */

PostEditAsset::register($this);
$this->title = Module::t('app', 'Editing the Post');
?>
<h1><?= Html::encode($this->title) ?></h1>
<?php $form = ActiveForm::begin(['enableClientScript' => false]); ?>
<div class="row">
    <div class="col-sm-10">
        <?= $form->errorSummary($model, ['class' => 'alert alert-danger']); ?>
        <?= $form->field($model, 'title')->textInput(['id' => 'title']) ?>
        <?= $form->field($model, 'permalink_name')->textInput(['id' => 'permalink_name']) ?>
        <?= $form->field($model, 'permalink_route')->hiddenInput(['id' => 'permalink_route'])->label(false) ?>
        <?= $form->field($model, 'date') ?>
        <div class="form-group field-slide-image required">
            <label class="control-label" for="slide-image"><?= Module::t('app', 'Thumbnail image') ?></label><br>
            <?= $form->field($model, 'thumbnail_image')->hiddenInput(['class' => 'img-editor-value'])->label(false) ?>
            <img src="<?=$model->thumbnail_image?>" alt="" class="img-responsive  img-editor-image" /><br>
            <input id="file" type="file" accept=".jpg,.png,.gif" class="hidden" data-directory="/uploads/images/post_thumbnails/"/>
            <div class="btn-group" role="group">
                <button id="file-upload-btn" class="btn btn-default" type="button"><?= Module::t('app', 'Upload image') ?>
                </button><button id="file-remove-btn" class="btn btn-default" type="button"><?= Module::t('app', 'Remove image') ?></button>
            </div>
        </div>
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
            <button id="cancel-btn" class="btn btn-default" type="button" data-return-url="<?= $returnUrl ?>"><?= Module::t('app', 'Cancel') ?></button>
        </div>
    </div>
    <div class="col-sm-2">
        <div class="panel panel-default post-category-panel">
            <div class="panel-heading"><?= Module::t('app', 'Categories') ?></div>
            <div class="panel-body">
                <input name="PostForm[categories2]"  type="hidden">
                <?php foreach ($model->categories as $i =>$catItem): ?>
                    <div class="checkbox">
                        <label>
                            <?= Html::checkbox('PostForm[categories][]', $catItem["selected"], ['value' => $catItem["id"]]) ?> <?= $catItem["name"] ?>
                        </label>
                    </div>
                <?php endforeach; ?>

            </div>
        </div>
        <div class="panel panel-default">
            <div class="panel-heading"><?= Module::t('app', 'Tags') ?></div>
            <div class="panel-body">
                <div class="form-group">
                    <div class="input-group">
                        <input id="add-tags-text" type="text" class="form-control" placeholder="<?= Module::t('app', 'Separate tags with commas') ?>">
                        <span class="input-group-btn">
                            <button id="add-tags-btn" class="btn btn-default" type="button"><span><?= Module::t('app', 'Add') ?></span></button>
                        </span>
                    </div>
                </div>
                <div class="form-group">
                    <ul id="post-tag-list" class="post-tag-list">
                        <?php foreach ($model->tags as $i => $tagItem): ?>
                            <li>
                                <input type="hidden" name="PostForm[tags][<?=$i?>][id]" value="<?=$tagItem["id"]?>" />
                                <input type="hidden" class="post-tag-name" name="PostForm[tags][<?=$i?>][name]" value="<?=$tagItem["name"]?>" />
                                <i class="fa fa-times-circle fa-lg post-tag-remove"></i> <?=$tagItem["name"]?>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
<?php ActiveForm::end(); ?>


