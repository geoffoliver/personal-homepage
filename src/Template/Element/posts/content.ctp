<?php

use App\Lib\oEmbed;

if (!isset($content) || !$content) {
    return;
}

$Parsedown = new ParsedownExtra();
$oEmbed = new oEmbed();
$parsed = $Parsedown->text($content);

echo $oEmbed->embed($parsed);

