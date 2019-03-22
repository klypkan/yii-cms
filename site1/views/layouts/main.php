<?php

/* @var $this \yii\web\View */
/* @var $content string */

use yii\helpers\Html;
use yii\bootstrap\Nav;
use yii\bootstrap\NavBar;
use yii\widgets\Breadcrumbs;
use site1\assets\AppAsset;
use site1\helpers\CurrentSiteHelper;
use site1\helpers\SiteMenuHelper;

AppAsset::register($this);
?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>">
<head>
    <meta charset="<?= Yii::$app->charset ?>">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?= Html::csrfMetaTags() ?>
    <title><?= Html::encode($this->title) ?></title>
    <?php $this->head() ?>
</head>
<body>
<?php $this->beginBody() ?>
<?php
NavBar::begin([
    'brandLabel' => Yii::t('app', 'SITE_LABEL'),
    'brandUrl' => CurrentSiteHelper::getRootUrl(),
    'options' => [
        'class' => 'navbar-inverse navbar-static-top',
    ],
]);
$menuItems = [
    ['label' => 'Home', 'url' => ['/site/index']],
    ['label' => 'About', 'url' => ['/site/about']],
    ['label' => 'Contact', 'url' => ['/site/contact']],
    [
        'label' => 'Dropdown',
        'items' => [
            ['label' => 'Level 1 - Dropdown A', 'url' => '#'],
            ['label' => 'Level 1 - Dropdown B', 'url' => '#'],
        ],
    ],
];
$menuItems = SiteMenuHelper::getTopMenu();
echo Nav::widget([
    'options' => ['class' => 'navbar-nav'],
    'items' => $menuItems,
]);
NavBar::end();
?>
<div class="container">
    <?= Breadcrumbs::widget([
        'homeLink' => ['label' => Yii::t('app', 'Home'), 'url' => CurrentSiteHelper::getRootUrl()],
        'links' => isset($this->params['breadcrumbs']) ? $this->params['breadcrumbs'] : [],
    ]) ?>
    <?= $content ?>
</div>
<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>
