<?php

namespace App\Helpers;

class StringHelper
{
    public static function leftZero($lenght, $number){
        $nLen = strlen($number);
        $zeros = '';
        for($i=0; $i<($lenght-$nLen); $i++){
            $zeros = $zeros.'0';
        }
        return $zeros.$number;
    }
}
