<?php
/* @var $this yii\web\View */
use yii\helpers\Html;
use yii\helpers\Url;

$this->title = $title;
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="row">
    <div class="col-md-12">
        <h1><?= Html::encode($this->title) ?></h1>
    </div>
</div>
<div class="row">
    <?php foreach ($postList as $postItem): ?>
        <div class="col-md-12">
            <h2><a href="<?=Url::to($postItem->url)?>"><?= $postItem->title ?></a></h2>
            <?php if (!empty($postItem->date)): ?>
                <p><?= $postItem->date ?></p>
            <?php endif; ?>
            <?php if (!empty($postItem->thumbnail_image)): ?>
                <p>
                    <img src="<?= $postItem->thumbnail_image ?>" alt="<?= $postItem->title ?>" class="img-responsive" />
                </p>
            <?php endif; ?>
        </div>
        <div class="col-md-12">
            <?= $postItem->content ?>
        </div>
    <?php endforeach; ?>
</div>
<div class="row">
    <div class="col-md-12">
        <?php
        $pagination = new yii\data\Pagination(['totalCount' => $totalCount]);
        echo \yii\widgets\LinkPager::widget([
            'pagination' => $pagination,
        ]);
        ?>
    </div>
</div>