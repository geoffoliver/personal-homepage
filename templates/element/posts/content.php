<?php

use App\Lib\ParsedownHashTags;

if (!isset($content) || !$content) {
    return;
}

$Parsedown = new ParsedownHashTags();
$Parsedown->setStrictMode(true);
$parsed = $Parsedown->text($content);

echo $this->Html->div('post-content', $parsed);
