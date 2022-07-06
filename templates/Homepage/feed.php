<?php
echo $this->Html->scriptBlock(sprintf(
    'var csrfToken = %s;',
    json_encode($this->request->getAttribute('csrfToken'))
));

$this->assign('title', __('My Feed'));

$ajaxUrl = '/homepage/ajax-feed';

if ($friendId) {
    $ajaxUrl .= '?friend=' . $friendId;
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
        <ul class="friends-list">
            <li>
                <?= $this->Html->link(
                    __('All Feeds'),
                    [
                        '_name' => 'feed'
                    ],
                    [
                        'class' => !$friendId ? 'active' : null,
                    ]
                ); ?>
            </li>
            <?php foreach($friends as $friend): ?>
                <li>
                    <a
                        href="<?= $this->Url->build(['_name' => 'friendFeed', 'friend_id' => $friend->id]); ?>"
                        <?= $friend->id === $friendId ? 'class="active"' : '' ?>
                    >
                        <?= $friend->name; ?>
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
