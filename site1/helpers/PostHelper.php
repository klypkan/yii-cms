<?php
namespace site1\helpers;

use Yii;

class PostHelper
{
    static function trimWords($text, $numWords = 60)
    {
        $text = trim( preg_replace( "/[\n\r\t ]+/", ' ', $text ), ' ');
        preg_match_all( '/./u', $text, $words_array );
        $words_array = array_slice($words_array[0], 0, $numWords + 1 );
        $text = implode('', $words_array );
        return $text;
    }

    static function convertDbDateToPostDate($dbDate)
    {
        $date = \DateTime::createFromFormat('Y-m-d H:i:s', $dbDate, new \DateTimeZone('UTC'));
        $date->setTimezone(new \DateTimeZone(Yii::$app->timeZone));
        $format = Yii::t('app', 'POST_DATE_TIME_FORMAT');
        if ($format == 'POST_DATE_TIME_FORMAT') {
            $format = 'Y-m-d';
        }
        return $date->format($format);
    }
}