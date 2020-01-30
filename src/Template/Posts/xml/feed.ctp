<?php

use Cake\Utility\Hash;
echo '<?xml version="1.0" encoding="UTF-8" ?>' . "\n";
$Parsedown = new Parsedown();
$Parsedown->setStrictMode(true);

?>
<feed xmlns="http://www.w3.org/2005/Atom">
    <title><?= Hash::get($settings, 'site-name'); ?></title>
    <subtitle><?= Hash::get($settings, 'site-name'); ?></subtitle>
    <link href="<?= $this->Url->build('/', true); ?>" />
    <link href="<?= $this->Url->build(['_name' => 'rssFeed', '_ext' => 'xml'], true); ?>" />
<?php if ($posts): ?>
    <updated><?= $posts->first()->modified->format('c'); ?></updated>
<?php endif; ?>
    <id><?= $this->Url->build('/', true); ?></id>
    <author>
        <name><?= Hash::get($settings, 'site-name'); ?></name>
    </author>
<?php foreach ($posts as $post): ?>
    <entry>
        <title type="html"><?= $post->name; ?></title>
        <link href="<?= $this->Url->build(['_name' => 'viewPost', $post->id]); ?>" />
        <updated><?= $post->modified->format('c'); ?></updated>
        <content type="html"><![CDATA[<?= $Parsedown->text($post->content); ?>]]></content>
    </entry>
<?php endforeach; ?>
</feed>
