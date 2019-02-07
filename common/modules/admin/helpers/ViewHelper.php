<?php
namespace common\modules\admin\helpers;

use Yii;
use yii\helpers\Url;

class ViewHelper
{
    static function getEditReturnUrl()
    {
        $returnUrlParameters = [Yii::$app->controller->id . '/index'];
        $queryParams = Yii::$app->request->getQueryParams();
        foreach ($queryParams as $key => $value) {
            if ($key != "id") {
                $returnUrlParameters[$key] = $value;
            }
        }
        return Url::to($returnUrlParameters);
    }
}