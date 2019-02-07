<?php
use yii\helpers\Url;
/* @var $this yii\web\View */
$this->title = $model->title;
if($model->description) {
    $this->registerMetaTag([
        'name' => 'description',
        'content' => $model->description
    ]);
}
?>
<div class="row">
    <div class="col-md-12">
        <?=$model->content?><br>
    </div>
</div>
<div class="row">
    <?php foreach ($postList as $postItem): ?>
        <div class="col-md-12">
            <h2><a href="<?=Url::to($postItem->url)?>"><?= $postItem->title ?></a></h2>
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