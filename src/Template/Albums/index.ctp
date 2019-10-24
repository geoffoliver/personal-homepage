<?php
use Cake\Utility\Inflector;

$title = __("{0} Albums", Inflector::singularize($title));

$this->assign('title', $title);
$this->append('css', $this->Html->css('albums/index.css'));
$this->append('script', $this->Html->script('util/lazyload.js'));

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

                            if ($album->cover_media) {
                                $cover = $this->Html->image(
                                    "/media/{$album->cover_media->thumbnail}"
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
