<?php

if (!isset($content) || !$content) {
    return;
}

$Parsedown = new \Parsedown();
$Parsedown->setStrictMode(true);
$parsed = $Parsedown->text($content);

echo $this->Html->div('post-content', $parsed);
