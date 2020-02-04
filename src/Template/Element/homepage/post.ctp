<?php
use Cake\Utility\Hash;
?>
<div class="box homepage-post">
    <article class="media">
        <div class="media-content">
            <div class="content">
                <?php if ($post->name): ?>
                <h1 class="is-marginless is-size-4">
                    <?= $this->Html->link(
                        $post->name,
                        ['_name' => 'viewPost', $post->id]
                    ); ?>
                </h1>
                <?php endif; ?>
                <h5 class="is-size-7 has-text-grey-light">
                    <?php if ($post->import_source): ?>
                        <span class="fab fa-<?= $post->import_source; ?>" aria-hidden="true"></span>
                    <?php endif; ?>
                    <time><?php
                        echo $this->Html->link(
                            $post->created->setTimezone(Hash::get($settings, 'timezone'))->format(Hash::get($settings, 'time-format')),
                            ['_name' => 'viewPost', $post->id]
                        );
                    ?></time>
                    <?php if ($post->created != $post->modified): ?>
                        &middot; <?= __('Updated'); ?>&nbsp;<time><?= $post->modified->setTimezone(Hash::get($settings, 'timezone'))->format(Hash::get($settings, 'time-format')); ?></time>
                    <?php endif; ?>
                </h5>
                <?php
                    if ($post->content) {
                        echo $this->element(
                            'posts/content',
                            ['content' => $post->content]
                        );
                    }

                    if ($post->embeds) {
                        echo $this->element(
                            'posts/embeds',
                            ['post' => $post]
                        );
                    }

                    if ($post->medias) {
                        echo $this->element(
                            'posts/media',
                            ['post' => $post]
                        );
                    }
                ?>
                <hr />
                <?= $this->element('item-footer', [
                    'item' => $post,
                    'share' => !$this->Identity->isLoggedIn()
                ]); ?>
            </div>
        </div>
    </article>
</div>
