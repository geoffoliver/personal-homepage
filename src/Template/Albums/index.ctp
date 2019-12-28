<?php
use Cake\Utility\Inflector;

$title = __("{0} Albums", Inflector::singularize($title));

$this->assign('title', $title);
$this->append('css', $this->Html->css('albums/index.css'));

$routeName = ($type === 'photos' ? 'photoAlbum' : 'videoAlbum');

?>
<section class="section" id="listAlbums">
    <div class="columns">
        <div class="column">
            <h1 class="is-size-2"><?= $title; ?></h1>
            <div class="box">
                <div class="content">
                    <div class="albums">
                        <?php
                        foreach ($albums as $album) {
                            $cover = "<span>{$album->name}</span>";

                            $coverImage = $album->cover_media;

                            if (!$coverImage && $album->medias) {
                                $coverImage = $album->medias[0];
                            }

                            if ($coverImage) {
                                $cover = $this->Html->image(
                                    null,
                                    [
                                        'data-lazy-src' => $this->Url->build([
                                            'controller' => 'Medias',
                                            'action' => 'download',
                                            $coverImage->id,
                                            'square_thumbnail'
                                        ])
                                    ]
                                ) . $cover;
                            }

                            echo $this->Html->link(
                                "<span>{$cover}</span>",
                                [
                                    '_name' => 'viewAlbum',
                                    $album->id
                                ],
                                [
                                    'escape' => false,
                                    'class' => 'album-link'
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
