<?php
namespace common\modules\admin\assets;

use yii\web\AssetBundle;

class CategoryEditAsset extends AssetBundle
{
    public $sourcePath = '@admin/assets';
    public $js = [
        'js/cat-edit.js'
    ];
    public $depends = [
        'common\modules\admin\assets\AppAsset',
    ];
}
