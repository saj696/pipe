<?php

namespace App\Http;


class Helpers
{
    public static function getUpperCase($string)
    {
        $upperCase = strtoupper($string);
        return $upperCase;
    }
}