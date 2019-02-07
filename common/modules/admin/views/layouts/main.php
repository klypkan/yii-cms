<?php
use yii\helpers\Html;
use yii\helpers\Url;
use common\modules\admin\assets\AppAsset;
use common\modules\admin\Module;
use common\modules\admin\helpers\SiteAdminHelper;

AppAsset::register($this);
$currentController = Yii::$app->controller->id;
$queryParams = Yii::$app->request->getQueryParams();
$editSiteId = "";
if (array_key_exists("site_id",$queryParams))
{
    $editSiteId = $queryParams["site_id"];
}

/* @var $this yii\web\View */
/* @var $content string */
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
<div class="navbar navbar-inverse" role="navigation">
    <div class="container">
        <div class="navbar-header">
            <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">
                <span class="sr-only">Toggle navigation</span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
            </button>
            <a class="navbar-brand" href="/">Yii-CMS</a>
        </div>
        <div class="collapse navbar-collapse">
            <ul class="nav navbar-nav">
            </ul>
            <ul class="nav navbar-nav navbar-right">
                <?php
                if (!Yii::$app->user->isGuest) {
                    echo '<li>'
                        . Html::beginForm(['default/logout'], 'post')
                        . Html::submitButton(
                            Module::t('app', 'Logout').' (' . Yii::$app->user->identity->username . ')',
                            ['class' => 'btn btn-link logout']
                        )
                        . Html::endForm()
                        . '</li>';
                }
                ?>
            </ul>
        </div>
    </div>
</div>
<div class="container-fluid">
    <div class="row">
        <div class="col-sm-2">
            <ul class="nav nav-pills nav-stacked">
                <li<?= ($currentController == "user" ? ' class="active"' : '') ?>><?= Html::a(Module::t('app', Module::t('app', 'Users')), ['/admin/user/index']) ?></li>
                <li<?= ($currentController == "role" ? ' class="active"' : '') ?>><?= Html::a(Module::t('app', Module::t('app', 'Roles')), ['/admin/role/index']) ?></li>
                <li<?= ($currentController == "site" ? ' class="active"' : '') ?>><?= Html::a(Module::t('app', Module::t('app', 'Sites')), ['/admin/site/index']) ?></li>
                <li<?= ($currentController == "log" ? ' class="active"' : '') ?>><?= Html::a(Module::t('app', Module::t('app', 'Log')), ['/admin/log/index']) ?></li>
            </ul>
            <div id="sites" class="panel-group-sites" role="tablist" aria-multiselectable="false">
                <?php foreach (SiteAdminHelper::getAll() as $siteAr): ?>
                    <div class="panel panel-default">
                        <div class="panel-heading" role="tab" id="<?= $siteAr["id"] ?>">
                            <h4 class="panel-title">
                                <a role="button" data-toggle="collapse" data-parent="#sites"
                                   href="#collapse<?= $siteAr["id"] ?>" aria-expanded="true"
                                   aria-controls="collapse<?= $siteAr["id"] ?>">
                                    <?= $siteAr["name"] ?>
                                </a>
                            </h4>
                        </div>
                        <div id="collapse<?= $siteAr["id"] ?>" class="panel-collapse collapse<?=$siteAr["id"]==$editSiteId?" in":""?>" role="tabpanel"
                             aria-labelledby="headingOne">
                            <div class="panel-body">
                                <ul class="nav nav-pills nav-stacked">
                                    <li<?= ($currentController == "page" && $siteAr["id"] == $editSiteId ? ' class="active"' : '') ?>><?= Html::a(Module::t('app', Module::t('app', 'Pages')), ['/admin/page/index', 'site_id' => $siteAr["id"]]) ?></li>
                                    <li<?= ($currentController == "post" && $siteAr["id"] == $editSiteId ? ' class="active"' : '') ?>><?= Html::a(Module::t('app', Module::t('app', 'Posts')), ['/admin/post/index', 'site_id' => $siteAr["id"]]) ?></li>
                                    <li<?= ($currentController == "category-admin" && $siteAr["id"] == $editSiteId ? ' class="active"' : '') ?>><?= Html::a(Module::t('app', Module::t('app', 'Categories')), ['/admin/category-admin/index', 'site_id' => $siteAr["id"]]) ?></li>
                                    <li<?= ($currentController == "tag-admin" && $siteAr["id"] == $editSiteId ? ' class="active"' : '') ?>><?= Html::a(Module::t('app', Module::t('app', 'Tags')), ['/admin/tag-admin/index', 'site_id' => $siteAr["id"]]) ?></li>
                                    <li<?= ($currentController == "menu" && $siteAr["id"] == $editSiteId ? ' class="active"' : '') ?>><?= Html::a(Module::t('app', Module::t('app', 'Menus')), ['/admin/menu/index', 'site_id' => $siteAr["id"]]) ?></li>
                                    <li<?= ($currentController == "import" && $siteAr["id"] == $editSiteId ? ' class="active"' : '') ?>><?= Html::a(Module::t('app', Module::t('app', 'Import')), ['/admin/import/index', 'site_id' => $siteAr["id"]]) ?></li>
                                </ul>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
        <div id="main-content" class="col-sm-10">
            <?= $content ?>
        </div>
    </div>
</div>
<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>
