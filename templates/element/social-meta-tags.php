<?php

if ($item->name) {
    $this->Html->meta([
        'property' => 'og:title',
        'content' => $item->name
    ],
    null,
    [
        'block' => true
    ]);
}

$this->Html->meta([
    'property' => 'og:type',
    'content' => 'article'
],
null,
[
    'block' => true
]);

$this->Html->meta([
    'property' => 'og:url',
    'content' => $this->Url->build($this->request->getRequestTarget(), ['fullBase' => true])
],
null,
[
    'block' => true
]);

$content = isset($item->content) ? $item->content : (isset($item->description) ? $item->description : null);

if ($content) {
    $this->Html->meta([
        'name' => 'description',
        'content' => preg_replace("/\r\n?/", " ", substr($content, 0, 155))
    ],
    null,
    [
        'block' => true
    ]);

    $this->Html->meta([
        'property' => 'og:description',
        'content' => preg_replace("/\r\n?/", " ", $this->Text->truncate($content, 500))
    ],
    null,
    [
        'block' => true
    ]);
}