<?php
namespace common\modules\admin\assets;

use yii\web\AssetBundle;

class UpdateAsset extends AssetBundle
{
    public $sourcePath = '@admin/assets';
    public $css = [
        'css/update.css'
    ];
    public $js = [
        'js/update.js'
    ];
    public $depends = [
        'common\modules\admin\assets\AppAsset',
    ];
}
