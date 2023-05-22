<?php

if(!function_exists('formatNumber')){
    function formatNumber($number) {
        $suffix = '';
        if ($number >= 1000 && $number < 1000000) {
            $number = $number / 1000;
            $suffix = ' rb';
        } elseif ($number >= 1000000 && $number < 1000000000) {
            $number = $number / 1000000;
            $suffix = ' jt';
        } elseif ($number >= 1000000000 && $number < 1000000000000) {
            $number = $number / 1000000000;
            $suffix = ' mil';
        } elseif ($number >= 1000000000000) {
            $number = $number / 1000000000000;
            $suffix = ' trl';
        }
        return number_format($number, 2, '.', '') . $suffix;
    }
}
