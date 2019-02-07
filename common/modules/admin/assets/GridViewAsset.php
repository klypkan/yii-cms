<?php
namespace common\modules\admin\assets;

use yii\web\AssetBundle;

class GridViewAsset extends AssetBundle
{
    public $sourcePath = '@admin/assets';
    public $css = [
        'css/font-awesome.css',
        'css/grid-view.css'
    ];
    public $js = [
        'js/grid-view.js',
        'js/progress-button.js'
    ];
    public $depends = [
        'yii\bootstrap\BootstrapPluginAsset',
    ];
}
