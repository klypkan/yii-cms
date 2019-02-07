<?php
namespace common\modules\admin\assets;

use yii\web\AssetBundle;

class FileBrowserAsset extends AssetBundle
{
    public $sourcePath = '@admin/assets';
    public $css = [
        'css/font-awesome.css',
        'css/file-browser.css'
    ];
    public $js = [
        'js/file-browser.js'
    ];
    public $depends = [
        'common\modules\admin\assets\AppAsset',
    ];
}
