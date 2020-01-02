<?php
use Cake\Utility\Hash;

echo $this->Html->tag(
    'footer',
    implode('', [
        $this->Html->div(
            'content has-text-centered',
            $this->Html->para(
                'is-size-7',
                __('&copy; {0} &mdash; {1}', date('Y'), Hash::get($settings, 'site-name'))
            )
        )
    ]),
    [
        'class' => 'footer'
    ]
);

// external dependencies
$this->append('script', $this->Html->script('https://use.fontawesome.com/releases/v5.3.1/js/all.js', ['defer']));

// utilities
$this->append('script', $this->Html->script('util/lazyload.js'));
$this->append('script', $this->Html->script('util/fix-iframe-embeds.js'));
$this->append('script', $this->Html->script('util/nav.js'));
$this->append('script', $this->Html->script('util/highlight-code.js'));
$this->append('script', $this->Html->script('util/external-links.js'));
$this->append('script', $this->Html->script('util/share.js'));

// code highlighting
/*
$this->append('script', $this->Html->script('//cdnjs.cloudflare.com/ajax/libs/highlight.js/9.15.9/highlight.min.js'));
$this->append('script', $this->Html->scriptBlock('hljs.initHighlightingOnLoad();'));
*/
echo $this->fetch('script');
