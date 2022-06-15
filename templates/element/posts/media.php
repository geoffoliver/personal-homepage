<?php
if ($post->medias) {
    echo $this->Html->div('post-media', implode('', array_map(function($media) {
        return $this->element('medias/thumbnail', ['media' => $media]);
    }, $post->medias)));
}
