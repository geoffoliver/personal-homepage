<?php
    $embeds = [];

    if ($post->embeds) {
        $embeds = json_decode($post->embeds);
    }

    if ($embeds && is_array($embeds)) {
        echo $this->Html->div('post-embeds', implode('', array_map(function($embed) {
            return $this->Html->div('pf-oembed', $embed);
        }, $embeds)));
    }
