<?php
namespace common\modules\admin\assets;

use yii\web\AssetBundle;

class ImportWordpressAsset extends AssetBundle
{
    public $sourcePath = '@admin/assets';
    public $css = [
    ];
    public $js = [
        'js/import-wordpress.js'
    ];
    public $depends = [
        'common\modules\admin\assets\AppAsset',
    ];
}
