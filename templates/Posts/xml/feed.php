<?php

use Cake\Utility\Hash;
echo '<?xml version="1.0" encoding="UTF-8" ?>' . "\n";
$Parsedown = new Parsedown();
$Parsedown->setStrictMode(true);

?>
<feed xmlns="http://www.w3.org/2005/Atom">
    <title><?= Hash::get($settings, 'site-name'); ?></title>
    <subtitle><?= Hash::get($settings, 'site-name'); ?></subtitle>
    <link href="<?= $this->Url->build('/', ['fullBase' => true]); ?>" />
    <link href="<?= $this->Url->build(['_name' => 'rssFeed', '_ext' => 'xml'], ['fullBase' => true]); ?>" />
    <channel>
        <atom:link href="<?= $this->Url->build(['_name' => 'rssFeed', '_ext' => 'xml'], ['fullBase' => true]); ?>" rel="self" type="application/rss+xml" />
    </channel>
<?php if ($posts): ?>
    <updated><?= $posts->first()->modified->format('c'); ?></updated>
<?php endif; ?>
    <id><?= $this->Url->build('/', ['fullBase' => true]); ?></id>
    <author>
        <name><?= Hash::get($settings, 'site-name'); ?></name>
    </author>
<?php foreach ($posts as $post): ?>
    <entry>
        <id><?= $this->Url->build(['_name' => 'viewPost', $post->id], ['fullBase' => true]); ?></id>
        <title type="html"><?= $post->name ?? $post->created->setTimezone(Hash::get($settings, 'timezone'))->format(Hash::get($settings, 'time-format')); ?></title>
        <link href="<?= $this->Url->build(['_name' => 'viewPost', $post->id], ['fullBase' => true]); ?>" />
        <updated><?= $post->modified->format('c'); ?></updated>
        <content type="html"><![CDATA[<?= $Parsedown->text($post->content); ?>]]></content>
    </entry>
<?php endforeach; ?>
</feed>
