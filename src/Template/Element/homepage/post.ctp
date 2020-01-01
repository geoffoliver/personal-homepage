<?php
use Cake\Utility\Hash;

$content = $this->element(
    'posts/content',
    ['content' => $post->content]
);

$embeds = $post->embeds ? json_decode($post->embeds) : false;
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
                <?php
                    echo $this->Html->div('main-content', $content);

                    if ($embeds && is_array($embeds)) {
                        foreach ($embeds as $embed) {
                            echo $this->Html->div('pf-oembed', $embed);
                        }
                    }

                    if (
                        $post->medias && (
                            $post->import_source !== 'twitter' || !$embeds
                        )
                    ):
                ?>
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
