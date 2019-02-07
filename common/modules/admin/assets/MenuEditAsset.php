<?php
namespace common\modules\admin\assets;

use yii\web\AssetBundle;

class MenuEditAsset extends AssetBundle
{
    public $sourcePath = '@admin/assets';
    public $css = [
        'css/menu-edit.css'
    ];
    public $js = [
        'js/bootstrap3-typeahead.js',
        'js/menu-edit.js'
    ];
    public $depends = [
        'common\modules\admin\assets\AppAsset',
    ];
}
