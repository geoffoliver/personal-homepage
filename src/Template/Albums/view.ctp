<?php
$this->assign('title', $album->name);
$this->append('css', $this->Html->css('medias/index.css'));
$this->append('css', $this->Html->css('albums/view.css'));
?>
<section class="section" id="viewAlbum">
    <div class="columns">
        <div class="column">
            <h1 class="is-size-2">
                <?= $album->name; ?>
            </h1>
            <?php if ($album->description): ?>
                <p class="is-size-4"><?= h($album->description); ?></p>
            <?php endif; ?>
            <div class="box">
                <div class="content">
                    <div class="album-entries medias">
                        <?php
                        foreach ($album->medias as $media) {
                            echo $this->Html->link(
                                $this->Html->image(
                                    '#',
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
