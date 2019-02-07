<?php
namespace site1\helpers;

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
}