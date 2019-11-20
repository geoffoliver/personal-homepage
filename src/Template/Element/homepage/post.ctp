<?php
use Cake\Utility\Hash;
use App\Lib\oEmbed;

$oEmbed = oEmbed::getInstance();

$content = $this->element(
    'posts/content',
    ['content' => $post->content]
);

$hasEmbed = strpos($content, 'pf-oembed') !== false;
?>
<div class="box homepage-post">
    <article class="media">
        <div class="media-content">
            <div class="content">
                <h1 class="is-marginless is-size-4">
                    <?= $this->Html->link(
                        $post->name,
                        ['_name' => 'viewPost', $post->id]
                    ); ?>
                </h1>
                <h5 class="is-size-7 has-text-grey-light">
                    <?php if ($post->import_source): ?>
                        <span class="fab fa-<?= $post->import_source; ?>" aria-hidden="true"></span>
                    <?php endif; ?>
                    <?= $post->created->setTimezone(Hash::get($settings, 'timezone'))->format('F j, Y \a\t g:i a'); ?>
                    <?php if ($post->created != $post->modified): ?>
                        &middot; <?= __('Updated'); ?>&nbsp;<?= $post->modified->setTimezone(Hash::get($settings, 'timezone'))->format('F j, Y \a\t g:i a'); ?>
                    <?php endif; ?>
                </h5>
                <p>
                    <?= $content; ?>
                    <?php
                        if (!$hasEmbed && $post->source) {
                            $embed = $oEmbed->getEmbedInfo($post->source);
                            if ($embed && $embed->code) {
                                $hasEmbed = true;
                                echo $oEmbed->wrapEmbed($embed->code);
                            }
                        }
                    ?>
                </p>
                <?php if (
                    $post->medias && (
                        $post->import_source !== 'twitter' || !$hasEmbed
                    )
                ): ?>
                    <div class="post-media">
                        <?php foreach ($post->medias as $media): ?>
                            <?= $this->element('medias/thumbnail', ['media' => $media]); ?>
                        <?php endforeach;?>
                    </div>
                <?php endif;?>
                <hr />
                <?= $this->element('item-footer', ['item' => $post]); ?>
            </div>
        </div>
    </article>
</div>
