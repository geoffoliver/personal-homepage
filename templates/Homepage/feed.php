<?php
echo $this->Html->scriptBlock(sprintf(
    'var csrfToken = %s;',
    json_encode($this->request->getAttribute('csrfToken'))
));

$this->assign('title', __('My Feed'));

$ajaxUrl = '/homepage/ajax-feed';

if ($followingId) {
    $ajaxUrl .= '?following=' . $followingId;
}

$this->append('css', $this->Html->css('feed.css'));
$this->append('script', $this->Html->script('lib/nanoajax/nanoajax.min.js'));
$this->append('script', $this->Html->script('util/ajax-paginate.js'));
$this->append('script', $this->Html->script('feed/index.js'));
$this->append('script', $this->Html->scriptBlock("(function() {
    document.addEventListener('DOMContentLoaded', () => {
        window.ajaxPaginate && window.ajaxPaginate({
            container: 'feedItems',
            url: '{$ajaxUrl}'
        });
    });
})();"));
?>
<div class="columns">
    <div class="column is-one-quarter">
        <ul class="following-list">
            <li>
                <?= $this->Html->link(
                    __('All Feeds'),
                    [
                        '_name' => 'feed'
                    ],
                    [
                        'class' => !$followingId ? 'active' : null,
                    ]
                ); ?>
            </li>
            <?php foreach($followings as $following): ?>
                <li>
                    <a
                        href="<?= $this->Url->build(['_name' => 'followingFeed', 'following_id' => $following->id]); ?>"
                        <?= $following->id === $followingId ? 'class="active"' : '' ?>
                    >
                        <div class="image is-16x16">
                            <?= $this->Html->image(
                                $this->Url->build([
                                    'controller' => 'Followings',
                                    'action' => 'icon',
                                    $following->id
                                ]),
                                ['class' => 'is-rounded']
                            ); ?>
                        </div>
                        <?= $following->name; ?>
                    </a>
                </li>
            <?php endforeach; ?>
        </ul>
    </div>
    <div class="column is-three-quarters">
        <div id="feedItems">
            <div class="feed-loader"><span class="fas fa-spin fa-spinner"></span> <?= __('Loading Feed...'); ?></div>
        </div>
    </div>
</div>
