<?php
namespace common\modules\admin\assets;

use yii\web\AssetBundle;

class PostEditAsset extends AssetBundle
{
    public $sourcePath = '@admin/assets';
    public $css = [
        'css/post-edit.css'
    ];
    public $js = [
        'js/post-edit.js'
    ];
    public $depends = [
        'common\modules\admin\assets\AppAsset',
    ];
}
