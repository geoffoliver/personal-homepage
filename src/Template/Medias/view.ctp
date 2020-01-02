<?php
use Cake\Utility\Hash;
$this->assign('title', $media->name);
$this->append('css', $this->Html->css('medias/view.css'));
$Parsedown = new Parsedown();
$Parsedown->setStrictMode(true);
?>
<section class="section" id="viewMedia">
    <article>
        <div class="columns">
            <div class="column is-three-quarters">
                <div class="media-title">
                    <h1 class="title is-3"><?= $media->name ? $media->name : __('Untitled Upload'); ?></h1>
                    <h2 class="subtitle is-6 has-text-grey">
                        <?= __('Posted'); ?>&nbsp;<time><?= $media->created->setTimezone(Hash::get($settings, 'timezone'))->format('F j, Y \a\t g:i a'); ?></time>
                    </h2>
                </div>
                <div class="media-body">
                    <div class="box">
                        <?= $this->element('media', ['media' => $media]); ?>
                        <?php if($media->description): ?>
                            <div class="media-description">
                                <hr />
                                <?= nl2br(h($media->description)); ?>
                            </div>
                        <?php endif; ?>
                        <?php if ($media->post && $media->post->content): ?>
                            <hr />
                            <p>
                                <?= $Parsedown->text($media->post->content); ?>
                            </p>
                        <?php endif; ?>
                        <hr />
                        <?= $this->element('item-footer', ['item' => $media, 'comments' => false]); ?>
                    </div>
                </div>
            </div>
            <div class="column is-one-quarter">
                <?= $this->element('comments', ['post' => $media]); ?>
            </div>
        </div>
    </div>
</section>
