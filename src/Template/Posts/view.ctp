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
                        <?php if ($this->Identity->isLoggedIn()) :?>
                            <div class="level-right">
                                <?= $this->Html->link(
                                    '<span class="fas fa-edit" aria-hidden="true"></span>&nbsp;' . __('Edit'),
                                    [
                                        'controller' => 'Posts',
                                        'action' => 'edit',
                                        $post->id
                                    ],
                                    [
                                        'class' => 'level-item',
                                        'escape' => false
                                    ]
                                ); ?>
                                <?= $this->Form->postLink(
                                    '<span class="fas fa-trash" aria-hidden="true"></span>&nbsp;' . __('Delete'),
                                    [
                                        'controller' => 'Posts',
                                        'action' => 'delete',
                                        $post->id
                                    ],
                                    [
                                        'confirm' => __('Are you sure you want to delete this post?'),
                                        'escape' => false
                                    ]
                                ); ?>
                            </div>
                        <?php endif; ?>
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
                            <?php foreach ($post->medias as $media): ?>
                                <img
                                    src="/media/<?= $media->thumbnail; ?>"
                                    data-original="/media/<?= $media->local_filename; ?>"
                                    alt="<?= $media->name; ?>"
                                />
                            <?php endforeach; ?>
                        </div>
                        <?php endif; ?>
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
