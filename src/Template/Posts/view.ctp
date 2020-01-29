<?php
use Cake\Utility\Hash;

$this->element('social-meta-tags', ['item' => $post]);
$this->assign('title', $post->name);
$this->append('css', $this->Html->css('posts/view.css'));
$this->append('script', $this->Html->script('posts/view.js'));
?>
<section class="section" id="viewPost">
    <article>
        <div class="columns">
            <div class="column is-three-quarters">
                <div class="post-name">
                    <?php if ($post->name): ?>
                    <h1 class="title is-3"><?= $post->name; ?></h1>
                    <?php endif; ?>
                    <h2 class="subtitle is-6 has-text-grey">
                        <?= __('Posted'); ?>&nbsp;<time><?= $post->created->setTimezone(Hash::get($settings, 'timezone'))->format('F j, Y \a\t g:i a'); ?></time>
                        <?php if ($post->created != $post->modified): ?>
                            &middot; <?= __('Updated'); ?>&nbsp;<time><?= $post->modified->setTimezone(Hash::get($settings, 'timezone'))->format('F j, Y \a\t g:i a'); ?></time>
                        <?php endif; ?>
                    </h2>
                </div>
                <div class="post-body">
                    <div class="box">
                        <div class="post-content content">
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
                            <?= $this->element('item-footer', ['item' => $post, 'comments' => false]); ?>
                        </div>
                    </div>
                </div>
            </div>
            <div class="column is-one-quarter">
                <?= $this->element('comments', ['post' => $post]); ?>
            </div>
        </div>
    </article>
</section>
