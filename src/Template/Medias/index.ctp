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
                    <?php
                        echo $this->Html->link(
                            '<span class="fas fa-star-of-life"></span>&nbsp;' . __('All'),
                            [
                                'controller' => 'Medias',
                                'action' => 'index'
                            ],
                            [
                                'class' => 'is-size-5 level-item ' . ($type === 'all' ? 'has-text-dark' : null),
                                'escape' => false
                            ]
                        );

                        echo $this->Html->link(
                            '<span class="fas fa-camera"></span>&nbsp;' . __('Photos'),
                            [
                                '_name' => 'photos',
                            ],
                            [
                                'class' => 'is-size-5 level-item ' . ($type === 'photos' ? 'has-text-dark' : null),
                                'escape' => false
                            ]
                        );

                        echo $this->Html->link(
                            '<span class="fas fa-video"></span>&nbsp;' . __('Videos'),
                            [
                                '_name' => 'videos',
                            ],
                            [
                                'class' => 'is-size-5 level-item ' . ($type === 'videos' ? 'has-text-dark' : null),
                                'escape' => false
                            ]
                        );
                    /*
                    if ($type !== 'all') {
                        echo $this->Html->link(
                            '<span class="fas fa-book-open"></span>&nbsp;' . __('View Albums'),
                            [
                                '_name' => 'listAlbums',
                                $type
                            ],
                            [
                                'class' => 'is-size-5 level-item',
                                'escape' => false
                            ]
                        );
                    }
                    */
                    ?>
                </div>
            </div>
            <div class="box">
                <div class="content">
                    <div class="medias">
                        <?php
                        foreach ($medias as $media) {
                            echo $this->Html->link(
                                $this->Html->tag(
                                    'img',
                                    null,
                                    [
                                        'data-lazy-src' => $this->Url->build([
                                            'controller' => 'Medias',
                                            'action' => 'download',
                                            $media->id,
                                            'thumbnail'
                                        ]),
                                        'loading' => 'lazy',
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
