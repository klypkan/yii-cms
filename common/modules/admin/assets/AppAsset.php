<?php
namespace common\modules\admin\assets;

use yii\web\AssetBundle;

class AppAsset extends AssetBundle
{
    public $sourcePath = '@admin/assets';
    public $css = [
        'css/font-awesome.css',
        'css/admin.css'
    ];
    public $js = [
        'js/edit.js',
        'js/progress-button.js',
        'js/ckeditor/ckeditor.js'
    ];
    public $depends = [
        'yii\web\YiiAsset',
        'yii\bootstrap\BootstrapPluginAsset',
    ];
}
