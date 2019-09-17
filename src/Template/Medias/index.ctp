<?php
$this->assign('title', $title);
$this->append('css', $this->Html->css('medias/index.css'));
$this->append('script', $this->Html->script('medias/index.js'));
?>
<section class="section" id="viewMedias">
    <div class="columns">
        <div class="column">
            <h1 class="is-size-2"><?= $title; ?></h1>
            <div class="medias">
                <?php
                foreach ($medias as $media) {
                    echo $this->Html->link(
                        $this->Html->image(
                            '#', //"/media/{$media->thumbnail}"
                            [
                                'data-lazy-src' => "/media/{$media->thumbnail}",
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
</section>
