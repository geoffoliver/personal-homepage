<?php
$Parsedown = new ParsedownExtra();
$this->assign('title', $post->title);
$this->append('css', $this->Html->css('posts/view.css'));
$this->append('script', $this->Html->script('posts/view.js'));
?>
<section class="section" id="viewPost">
    <article>
        <div class="post-title">
            <div class="columns">
                <div class="column is-10 is-offset-1">
                    <h1 class="is-size-2"><?= $post->title; ?></h1>
                    <div class="level is-mobile">
                        <h3 class="is-size-6 has-text-grey level-left">
                            <?= __('Posted'); ?>&nbsp;<time><?= $post->created->format('F j, Y \a\t g:i a'); ?></time>
                        </h3>
                    </div>
                </div>
            </div>
        </div>
        <div class="post-body">
            <div class="columns">
                <div class="column is-10 is-offset-1">
                    <div class="box">
                        <div class="post-content content">
                            <?= $Parsedown->text($post->content); ?>
                        </div>
                        <?php if ($post->medias): ?>
                        <div class="post-media">
                            <?php
                                foreach ($post->medias as $media) {
                                    echo $this->element('media', ['media' => $media]);
                                }
                            ?>
                        </div>
                        <?php endif; ?>
                        <hr />
                        <?= $this->element('item-footer', ['item' => $post, 'comments' => false]); ?>
                    </div>
                </div>
            </div>
        </div>
    </article>
    <?php
        if ($post->allow_comments) {
            echo  $this->element('comments', ['post' => $post]);
        }
    ?>
</section>
