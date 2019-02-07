<?php
namespace common\components;

use Yii;
use yii\base\Behavior;
use yii\db\ActiveRecord;
use common\models\Log;

class LogBehavior extends Behavior {
    public function events()
    {
        return [
            ActiveRecord::EVENT_AFTER_INSERT => 'afterChange',
            ActiveRecord::EVENT_AFTER_UPDATE => 'afterChange',
            ActiveRecord::EVENT_AFTER_DELETE => 'afterChange',
        ];
    }

    public function afterChange($event)
    {
        $eventName = strtoupper($event->name);
        $sender = $event->sender;

        $log = new Log();
        switch ($eventName) {
            case "AFTERINSERT":
                $log->event = Log::EVENT_INSERT;
                break;
            case "AFTERUPDATE":
                $log->event = Log::EVENT_UPDATE;
                break;
            case "AFTERDELETE":
                $log->event = Log::EVENT_DELETE;
                break;
        }
        $log->source = json_encode(array("className"=>$sender->className(),"id"=>$sender->id));
        if(Yii::$app->hasProperty("user")) {
            $log->user_id = Yii::$app->user->id;
        }
        $log->save();
    }
} 