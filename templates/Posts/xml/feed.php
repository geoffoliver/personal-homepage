<?php

use Cake\Utility\Hash;
echo '<?xml version="1.0" encoding="UTF-8" ?>' . "\n";
$Parsedown = new Parsedown();
$Parsedown->setStrictMode(true);

?>
<rss version="2.0" xmlns:atom="http://www.w3.org/2005/Atom">
    <channel>
        <title><?= Hash::get($settings, 'site-name'); ?></title>
        <description><?= Hash::get($settings, 'site-name'); ?></description>
        <link href="<?= $this->Url->build('/', ['fullBase' => true]); ?>" />
    <?php if ($posts): ?>
        <lastBuildDate><?= $posts->first()->modified->format('c'); ?></lastBuildDate>
    <?php endif; ?>
    <?php foreach ($posts as $post): ?>
        <item>
            <guid><?= $this->Url->build(['_name' => 'viewPost', $post->id], ['fullBase' => true]); ?></guid>
            <title><?= $post->name ?? $post->created->setTimezone(Hash::get($settings, 'timezone'))->format(Hash::get($settings, 'time-format')); ?></title>
            <link><?= $this->Url->build(['_name' => 'viewPost', $post->id], ['fullBase' => true]); ?></link>
            <pubDate><?= $post->modified->format('c'); ?></pubDate>
            <description><![CDATA[<?= $Parsedown->text($post->content); ?>]]></description>
        <?php if($post->source): ?>
            <source url="<?= urlencode($post->source); ?>"><?= $post->source; ?></source>
        <?php endif; ?>
        </item>
    <?php endforeach; ?>
    </channel>
</rss>
