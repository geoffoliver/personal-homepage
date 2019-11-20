<?php
    use Cake\Utility\Hash;

    echo $this->Html->charset();

    echo $this->Html->meta([
        'name' => 'viewport',
        'content' => 'width=device-width, initial-scale=1.0'
    ]);

    $siteName = Hash::get($settings, 'site-name');

    if ($siteTitle = Hash::get($settings, 'site-title')) {
        if ($this->fetch('title')) {
            $this->append('title', __(' - {0}', $siteTitle));
        } elseif ($siteName) {
            $this->assign('title', __('{0} - {1}', $siteName, $siteTitle));
        } else {
            $this->assign('title', $siteTitle);
        }
    }

    echo $this->Html->tag('title', $this->fetch('title'));

    echo $this->Html->meta('icon', '/profile-photo');

    echo $this->Html->css('base.css');
    echo $this->Html->css('//cdnjs.cloudflare.com/ajax/libs/highlight.js/9.15.9/styles/default.min.css');

    echo $this->fetch('meta');
    echo $this->fetch('css');

    echo "<link rel=\"dns-prefetch\" href=\"http://{$this->request->host()}/\">";
