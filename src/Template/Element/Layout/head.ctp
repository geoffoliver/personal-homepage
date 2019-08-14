<?php
    echo $this->Html->charset();

    echo $this->Html->meta([
        'name' => 'viewport',
        'content' => 'width=device-width, initial-scale=1.0'
    ]);

    echo $this->Html->tag('title', $this->fetch('title'));

    echo $this->Html->meta('icon');

    echo $this->Html->css('base.css');

    echo $this->fetch('meta');
    echo $this->fetch('css');

