<?php
use App\Model\Entity\Media;
use App\Model\Entity\Post;

// make sure we have a post or media
if (!$item) {
    return;
}

// figure out what
$isPost = is_a($item ,Post::class);
$isMedia = is_a($item, Media::class);

// if this isn't a post or media, wtf are we doing?
if (!$isPost && !$isMedia) {
    return;
}

// keep the list of links here
$links = [];

// do we want to show the 'share' link?
if (!isset($share)) {
    $share = true;
}

if ($isPost && $item->source) {
    $itemUrl = $item->source;
} else {
    $itemUrl = $this->Url->build([
        'controller' => $isPost ? 'Posts' : 'Medias',
        'action' => 'view',
        $item->id
    ], true);
}

if ($share) {
    $links[]= $this->Html->link(
        '<span class="fas fa-share" aria-hidden="true"></span>&nbsp;' . __('Share'),
        '#',
        [
            'data-url' => $itemUrl,
            'escape' => false,
            'class' => 'level-item share-item'
        ]
    );
}

// this only applies to posts... for now, anyway
if ($isPost) {
    // do we want to show the 'view original' link?
    if (!isset($original)) {
        $original = true;
    }

    if ($original && $item->source) {
        $links[]= $this->Html->link(
            '<span class="fas fa-external-link-alt" aria-hidden="true"></span>&nbsp' . __('View Original'),
            $item->source,
            [
                'escape' => false,
                'class' => 'level-item',
                'target' => '_blank'
            ]
        );
    }
}

// do we want to show the 'comments' link?
if (!isset($comments)) {
    $comments = true;
}

if ($comments) {
    $links[]= $this->Html->link(
        '<span class="fas fa-comment" aria-hidden="true"></span>&nbsp;' . __('{0} Comments', count($item->comments)),
        [
            '_name' => $isPost ? 'viewPost' : 'viewMedia',
            $item->id . '#comments',
        ],
        [
            'escape' => false,
            'class' => 'level-item'
        ]
    );
}

// this only applies to media items
if ($isMedia) {
    /*
    if ($item->album) {
        $links[]= $this->Html->link(
            '<span class="fas fa-book-open" aria-hidden="true"></span>&nbsp;' . __('View Album'),
            [
                '_name' => 'viewAlbum',
                $item->album->id
            ],
            [
                'escape' => false,
                'class' => 'level-item'
            ]
        );
    }
    */

    if ($item->post) {
        $links[]= $this->Html->link(
            '<span class="fas fa-file-alt" aria-hidden="true"></span>&nbsp;' . __('View Post'),
            [
                '_name' => 'viewPost',
                $item->post->id
            ],
            [
                'escape' => false,
                'class' => 'level-item'
            ]
        );
    }
}

?>
<nav class="level is-mobile is-size-7">
    <div class="level-left">
        <?= implode('', $links); ?>
    </div>
    <?php if ($this->Identity->isLoggedIn()) :?>
        <div class="level-right">
            <?= $this->Html->link(
                '<span class="fas fa-edit" aria-hidden="true"></span>&nbsp;' . __('Edit'),
                [
                    'controller' => $isPost ? 'Posts' : 'Medias',
                    'action' => 'edit',
                    $item->id
                ],
                [
                    'class' => 'level-item',
                    'escape' => false
                ]
            ); ?>
            <?= $this->Form->postLink(
                '<span class="fas fa-trash" aria-hidden="true"></span>&nbsp;' . __('Delete'),
                [
                    'controller' => $isPost ? 'Posts' : 'Medias',
                    'action' => 'delete',
                    $item->id
                ],
                [
                    'confirm' => __('Are you sure you want to delete this item?\n\nYou can not undo this!'),
                    'escape' => false
                ]
            ); ?>
        </div>
    <?php endif; ?>
</nav>
