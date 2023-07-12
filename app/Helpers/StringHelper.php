<?php

namespace App\Helpers;

class StringHelper
{
    public static function leftZero($lenght, $number) {
        $nLen = strlen($number);
        $zeros = '';
        for($i=0; $i<($lenght-$nLen); $i++){
            $zeros = $zeros.'0';
        }
        return $zeros.$number;
    }
    
    public static function encode5t($str) {
        for($i=0; $i<5;$i++){
            $str=strrev(base64_encode($str));
        }
        return $str;
    }

    public static function claveHash($str) {
        return password_hash($str, PASSWORD_DEFAULT);
    }
    
}
