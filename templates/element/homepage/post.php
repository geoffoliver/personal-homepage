<?php
use Cake\Utility\Hash;
$linkSpan = '<span class="fas ml-2 fa-link" aria-hidden="true"></span>';
?>
<article class="homepage-post h-entry">
    <div class="media">
        <div class="media-content">
            <div class="content">
                <?php if ($post->name): ?>
                <h1 class="is-marginless post-name p-name<?= $post->is_link ? ' is-link' : ''; ?>">
                    <?= $this->Html->link(
                        $post->name . ($post->is_link ? $linkSpan : ''),
                        ($post->is_link && $post->source) ? $post->source : ['_name' => 'viewPost', $post->id],
                        ['escape' => false]
                    ); ?>
                </h1>
                <?php endif; ?>
                <h5 class="is-size-7 has-text-grey-light post-date">
                    <span class="author">
                        <a href="/" rel="author" class="p-author h-card"><?= $post->user->name; ?></a>
                    </span>
                    on
                    <?php if ($post->import_source): ?>
                        <span class="fab fa-<?= $post->import_source; ?>" aria-hidden="true"></span>
                    <?php endif; ?>
                    <time>
                        <?= $this->Html->link(
                            $this->Html->tag(
                                'time',
                                $post->created->setTimezone(Hash::get($settings, 'timezone'))->format(Hash::get($settings, 'time-format')),
                                [
                                    'class' => 'dt-published',
                                    'datetime' => $post->created->format('Y-m-d H:i:s'),
                                ]
                            ),
                            ['_name' => 'viewPost', $post->id],
                            ['class' => 'u-url', 'escape' => false]
                        ); ?>
                    </time>
                    <?php if ($post->created != $post->modified): ?>
                        &middot; <?= __('Updated'); ?>&nbsp;<?= $this->Html->tag(
                            'time',
                            $post->modified->setTimezone(Hash::get($settings, 'timezone'))->format(Hash::get($settings, 'time-format')),
                            [
                                'class' => 'dt-published',
                                'datetime' => $post->modified->format('Y-m-d H:i:s'),
                            ]
                        );
                        ?>
                    <?php endif; ?>
                </h5>
                <div class="e-content">
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
                </div>
                <?= $this->element('item-footer', [
                    'item' => $post,
                    'share' => !$this->Identity->isLoggedIn()
                ]); ?>
            </div>
        </div>
    </div>
</article>
