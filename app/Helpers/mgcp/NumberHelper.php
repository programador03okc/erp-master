<?php

namespace App\Helpers\mgcp;

class NumberHelper
{
    public static function abreviar($numero)
    {
        if ($numero < 1000000) {
            $resultado = number_format($numero/1000, 1) . 'K';
        } else {
            $resultado = number_format($numero / 1000000, 1) . 'M';
        }
        return $resultado;
    }
}
