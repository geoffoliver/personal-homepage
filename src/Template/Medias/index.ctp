<?php
$this->assign('title', $title);
$this->append('css', $this->Html->css('medias/index.css'));
?>
<section class="section" id="viewMedias">
    <div class="columns">
        <div class="column">
            <div class="level">
                <div class="level-left">
                    <h1 class="is-size-2"><?= $title; ?>
                </div>
                <div class="level-right">
                    <?php if ($type): ?>
                        <?= $this->Html->link(
                            '<span class="fas fa-book-open"></span>&nbsp;' . __('View Albums'),
                            [
                                '_name' => 'listAlbums',
                                $type
                            ],
                            [
                                'class' => 'is-size-5',
                                'escape' => false
                            ]
                        ); ?>
                    <?php endif; ?>
                </div>
            </div>
            <div class="box">
                <div class="content">
                    <div class="medias">
                        <?php
                        foreach ($medias as $media) {
                            echo $this->Html->link(
                                $this->Html->image(
                                    null,
                                    [
                                        'data-lazy-src' => $this->Url->build([
                                            'controller' => 'Medias',
                                            'action' => 'download',
                                            $media->id,
                                            'thumbnail'
                                        ])
                                    ]
                                ),
                                [
                                    'controller' => 'Medias',
                                    'action' => 'view',
                                    $media->id
                                ],
                                [
                                    'escape' => false,
                                    'class' => 'media-link',
                                ]
                            );
                        }
                        ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
