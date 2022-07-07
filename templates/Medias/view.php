<?php
use Cake\Utility\Hash;
use App\Lib\CustomParsedown;

$this->element('social-meta-tags', ['item' => $media]);
$this->assign('title', $media->name);
$this->append('css', $this->Html->css('medias/view.css'));
$Parsedown = new CustomParsedown();
$Parsedown->setStrictMode(true);
?>
<section class="section" id="viewMedia">
    <article class="h-entry">
        <div class="columns">
            <div class="column">
                <div class="media-title">
                    <?php if ($media->name): ?>
                    <h1 class="title p-name"><?= $media->name; ?></h1>
                    <?php endif; ?>
                    <h2 class="subtitle is-size-7 media-date">
                        <span class="author">
                            <a href="/" rel="author" class="p-author h-card"><?= $media->user->name; ?></a>
                        </span>
                        on
                        <?= $this->Html->link(
                            $this->Html->tag(
                                'time',
                                $media->created->setTimezone(Hash::get($settings, 'timezone'))->format(Hash::get($settings, 'time-format')),
                                [
                                    'class' => 'dt-published',
                                    'datetime' => $media->created->format('Y-m-d H:i:s'),
                                ]
                            ),
                            ['_name' => 'viewMedia', $media->id],
                            ['class' => 'u-url', 'escape' => false]
                        ); ?>
                    </h2>
                </div>
                <div class="media-body">
                    <?= $this->element('media', ['media' => $media]); ?>
                    <div class="e-content<?= !$media->name ? ' p-name' : ''; ?>">
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
                    </div>
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
