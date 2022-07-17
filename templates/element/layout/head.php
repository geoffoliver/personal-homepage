<?php
    use Cake\Utility\Hash;

    echo $this->Html->charset();

    echo $this->Html->meta([
        'name' => 'viewport',
        'content' => 'width=device-width, initial-scale=1.0'
    ]);

    // generate the title for the window/tab
    $siteName = Hash::get($settings, 'site-name');

    $siteTitle = Hash::get($settings, 'site-title');

    if ($siteTitle) {
        if ($this->fetch('title')) {
            $this->append('title', __(' - {0}', $siteTitle));
        } elseif ($siteName) {
            $this->assign('title', __('{0} - {1}', $siteName, $siteTitle));
        } else {
            $this->assign('title', $siteTitle);
        }
    }

    echo $this->Html->tag('title', $this->fetch('title'));

    // JSON feed
    echo $this->Html->tag('link', null, [
        'rel' => 'alternate',
        'title' => __('{0} Feed', $siteName),
        'type' => 'application/json',
        'href' => $this->Url->build([
            '_name' => 'jsonFeed',
            '_ext' => 'json'
        ], ['fullBase' => true])
    ]);

    // ATOM feed
    echo $this->Html->tag('link', null, [
        'rel' => 'alternate',
        'title' => __('{0} Feed', $siteName),
        'type' => 'application/rss+xml',
        'href' => $this->Url->build([
            '_name' => 'rssFeed',
            '_ext' => 'xml'
        ], ['fullBase' => true])
    ]);

    // indie auth authorization endpoint
    echo $this->Html->tag('link', null, [
        'rel' => 'authorization_endpoint',
        'href' => $this->Url->build([
            'controller' => 'users',
            'action' => 'indieAuth'
        ], ['fullBase' => true])
    ]);

    // webmentions
    echo $this->Html->tag('link', null, [
        'rel' => 'webmention',
        'href' => $this->Url->build([
            'controller' => 'webmentions',
            'action' => 'add',
        ], ['fullBase' => true])
    ]);

    $meLinks = Hash::get($settings, 'indieweb-me-links');
    if ($meLinks) {
        $meLinks = preg_split('/(\r\n)/', $meLinks);
        foreach ($meLinks as $ml) {
            echo $this->Html->tag('link', null, [
                'rel' => 'me',
                'href' => $ml,
            ]);
        }
    }

    // TODO: implement token endpoint stuff
    // echo $this->Html->tag('link', null, [
    //     'rel' => 'token_endpoint',
    //     'href' => $this->Url->build([
    //         'controller' => 'users',
    //         'action' => 'indieToken'
    //     ], ['fullBase' => true])
    // ]);

    // TODO: implement micropub stuff
    // echo $this->Html->tag('link', null, [
    //     'rel' => 'micropub',
    //     'href' => $this->Url->build([
    //         'controller' => '??',
    //         'action' => '??'
    //     ], ['fullBase' => true])
    // ]);

    // make the og:site_name meta tag
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

    // favicon
    echo $this->Html->meta('icon', '/profile-photo');

    // base stylesheet
    echo $this->Html->css('base.css');

    // meta blocks
    echo $this->fetch('meta');

    // css blocks
    echo $this->fetch('css');
