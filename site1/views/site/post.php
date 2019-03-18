<?php
/* @var $this yii\web\View */
use yii\helpers\Html;
use yii\helpers\Url;

$this->title = $model->title;
if ($model->description) {
    $this->registerMetaTag([
        'name' => 'description',
        'content' => $model->description
    ]);
}
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="row">
    <div class="col-md-12">
        <h1><?= Html::encode($this->title) ?></h1>
        <?php if (!empty($model->date)): ?>
            <p><?= $model->date ?></p>
        <?php endif; ?>
    </div>
</div>
<div class="row">
    <div class="col-md-12">
        <?= $model->content ?>
    </div>
</div>
<div class="row">
    <div class="col-md-12">
        <?php $i = 0;
        foreach ($model->tags as $tagItem): ?><?= ($i > 0 ? " " : "") ?>
            <a href="<?= Url::to($tagItem["url"]) ?>"><?= $tagItem["name"] ?></a>
            <?php $i++; endforeach; ?>
    </div>
</div>
