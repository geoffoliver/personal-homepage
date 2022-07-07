<?php

use Cake\Utility\Hash;
use App\Lib\ParsedownHashTags;

echo '<?xml version="1.0" encoding="UTF-8" ?>' . "\n";
$Parsedown = new ParsedownHashTags();
$Parsedown->setStrictMode(true);

?>
<rss
    version="2.0"
    xmlns:atom="http://www.w3.org/2005/Atom"
    xmlns:slash="http://purl.org/rss/1.0/modules/slash/"
>
    <channel>
        <title><?= Hash::get($settings, 'site-name'); ?></title>
        <description><?= Hash::get($settings, 'site-name'); ?></description>
        <link><?= $this->Url->build('/', ['fullBase' => true]); ?></link>
        <atom:link href="<?= $this->Url->build(['_name' => 'rssFeed', '_ext' => 'xml'], ['fullBase' => true]); ?>" rel="self" type="application/rss+xml" />
    <?php if ($posts): ?>
        <lastBuildDate><?= $posts->first()->modified->format('D, d M Y h:i:s O'); ?></lastBuildDate>
    <?php endif; ?>
    <?php foreach ($posts as $post): ?>
        <item>
            <guid><?= $this->Url->build(['_name' => 'viewPost', $post->id], ['fullBase' => true]); ?></guid>
            <title><?= $post->name ?? $post->created->setTimezone(Hash::get($settings, 'timezone'))->format(Hash::get($settings, 'time-format')); ?></title>
            <link><?= $this->Url->build(['_name' => 'viewPost', $post->id], ['fullBase' => true]); ?></link>
            <pubDate><?= $post->modified->format('D, d M Y h:i:s O'); ?></pubDate>
            <description><![CDATA[<?= $Parsedown->text($post->content); ?>]]></description>
            <?php if($post->allow_comments): ?>
            <comments><?= $this->Url->build(['_name' => 'viewPost', $post->id], ['fullBase' => true]); ?>#comments</comments>
            <slash:comments><?= count($post->comments); ?></slash:comments>
            <?php endif; ?>
        </item>
    <?php endforeach; ?>
    </channel>
</rss>
