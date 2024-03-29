<?php
use Cake\Utility\Hash;

$this->element('social-meta-tags', ['item' => $post]);
$this->assign('title', $post->name ?? $post->created->setTimezone(Hash::get($settings, 'timezone'))->format(Hash::get($settings, 'time-format')));
$this->append('css', $this->Html->css('posts/view.css'));
$this->append('script', $this->Html->script('posts/view.js'));
?>
<section class="section" id="viewPost">
    <article class="h-entry">
        <div class="columns">
            <div class="column">
                <div class="post-name">
                    <?php if ($post->name): ?>
                        <h1 class="title is-marginless p-name">
                            <?= $post->name; ?>
                            <?php if ($post->is_link): ?>
                                <span class="fas fa-link ml-2" aria-hidden="true"></span>
                            <?php endif; ?>
                        </h1>
                    <?php endif; ?>
                    <h2 class="subtitle is-size-7 post-date">
                        <span class="author">
                            <a href="/" rel="author" class="p-author h-card"><?= $post->user->name; ?></a>
                        </span>
                        on
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
                    </h2>
                </div>
                <div class="post-body">
                    <div class="post-content content e-content">
                        <?php
                            if ($post->content) {
                                echo $this->element(
                                    'posts/content',
                                    ['content' => $post->content]
                                );
                            }
                        ?>
                    </div>
                    <?php
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

                        echo $this->element('item-footer', ['item' => $post, 'comments' => false]);
                    ?>
                </div>
            </div>
        </div>
        <div class="post-comments">
            <div class="columns">
                <div class="column">
                    <?= $this->element('comments', ['post' => $post]); ?>
                </div>
            </div>
        </div>
    </article>
</section>
