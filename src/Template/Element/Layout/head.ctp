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

    echo $this->Html->tag('link', null, [
        'rel' => 'alternate',
        'title' => __('{0} Feed', $siteName),
        'type' => 'application/json',
        'href' => $this->Url->build([
            '_name' => 'jsonFeed',
            '_ext' => 'json'
        ], true)
    ]);

    echo $this->Html->tag('link', null, [
        'rel' => 'alternate',
        'title' => __('{0} Feed', $siteName),
        'type' => 'application/atom+xml',
        'href' => $this->Url->build([
            '_name' => 'rssFeed',
            '_ext' => 'xml'
        ], true)
    ]);

    echo $this->Html->tag('title', $this->fetch('title'));

    echo $this->Html->meta('icon', '/profile-photo');

    echo $this->Html->css('base.css');
    echo $this->Html->css('//cdnjs.cloudflare.com/ajax/libs/highlight.js/9.15.9/styles/default.min.css');

    $ogName = $siteName;
    if ($siteTitle) {
        $ogName .= ' - ' . $siteTitle;
    }

    $this->Html->meta(
        [
            'property' => 'og:site_name',
            'content' => $ogName
        ],
        null,
        [
            'block' => true
        ]
    );

    echo $this->fetch('meta');
    echo $this->fetch('css');
