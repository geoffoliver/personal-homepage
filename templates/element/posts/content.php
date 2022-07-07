<?php

use App\Lib\CustomParsedown;

if (!isset($content) || !$content) {
    return;
}

$Parsedown = new CustomParsedown();
$Parsedown->setStrictMode(true);
$parsed = $Parsedown->text($content);

echo $this->Html->div('post-content', $parsed);
