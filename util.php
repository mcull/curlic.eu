<?php  

function makeFontUrl($font, $text) {
    return "https://www.curlic.eu/namesvg/?text=" .
    $text .
    "&font=" . 
    $font->ttfName .
    "&stroke=" .
    (($font->stroke && strlen($font->stroke) > 0) ? $font->stroke : "") .
    "&spacing=" .
    (($font->spacing && strlen($font->spacing) > 0) ? $font->spacing : "") .
    "&size=" .
    (($font->size && strlen($font->size) > 0) ? $font->size : "120") .
    "&t=" .
    (($font->top && strlen($font->top) > 0) ? $font->top : "") .
    "&l=" .
    (($font->left && strlen($font->left) > 0) ? $font->left : "");
}

function fontNameComparitor($a, $b)
{
    return  strcasecmp($a->ttfName, $b->ttfName);
}



?>