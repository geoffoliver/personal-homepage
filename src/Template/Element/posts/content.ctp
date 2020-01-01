<?php

// use App\Lib\oEmbed;

if (!isset($content) || !$content) {
    return;
}

$Parsedown = new ParsedownExtra();
// $oEmbed = oEmbed::getInstance();
$parsed = $Parsedown->text($content);

// echo $oEmbed->embed($parsed);

echo $parsed;
