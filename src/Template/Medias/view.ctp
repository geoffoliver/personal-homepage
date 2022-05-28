<?php
use Cake\Utility\Hash;

$this->element('social-meta-tags', ['item' => $media]);
$this->assign('title', $media->name);
$this->append('css', $this->Html->css('medias/view.css'));
$Parsedown = new Parsedown();
$Parsedown->setStrictMode(true);
?>
<section class="section" id="viewMedia">
    <article>
        <div class="columns">
            <div class="column">
                <div class="media-title">
                    <?php if ($media->name): ?>
                    <h1 class="title"><?= $media->name; ?></h1>
                    <?php endif; ?>
                    <h2 class="subtitle is-size-7 has-text-grey media-date">
                        <?= __('Posted'); ?>&nbsp;<time><?= $media->created->setTimezone(Hash::get($settings, 'timezone'))->format(Hash::get($settings, 'time-format')); ?></time>
                    </h2>
                </div>
                <div class="media-body">
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
        <div class="columns">
            <div class="column">
                <?= $this->element('comments', ['post' => $media]); ?>
            </div>
        </div>
    </div>
</section>
