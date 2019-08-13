<?php
$faker = Faker\Factory::create();
$this->assign('title', 'My Feed');
$this->append('css', $this->Html->css('feed.css'));
$this->append('script', $this->Html->script('lib/nanoajax/nanoajax.min.js'));
?>
<div id="feedPage">
    <div class="columns">
        <div class="column is-one-quarter">
            <div class="sidebar sticky-sidebar">
                <div class="box">
                    <h3>
                        <a href="/friends">
                            <i class="fas fa-fw fa-user-friends"></i>
                            <strong><?= __('My Friends'); ?></strong>
                        </a>
                    </h3>
                    <?php if ($friends->count()): ?>
                        <div class="sidebar-thumbnail-grid">
                            <?php foreach ($friends as $friend): ?>
                            <div class="friend-icon">
                                <?= $this->Html->link(
                                    $this->Html->image(
                                        $friend->icon,
                                        ['alt' => $friend->name]
                                    ),
                                    $friend->url,
                                    [
                                        'target' => '_blank',
                                        'rel' => 'noopener noreferrer',
                                        'title' => $friend->name,
                                        'escape' => false
                                    ]
                                ); ?>
                            </div>
                            <?php endforeach; ?>
                        </div>
                    <?php else: ?>
                        <p>
                            <?= __('You have not setup any friends yet.'); ?>
                        </p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        <div class="column">
            <div id="feedItems">
                <div class="box">
                    <i class="fas fa-spin fa-spinner"></i> <?= __('Loading Feed...'); ?>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
(function() {
    var feed = document.getElementById('feedItems');

    nanoajax.ajax({
        url: "/homepage/ajax-feed"
    }, function(status, response) {
        if (status !== 200) {
            feed.innerHTML = "<?= __('Error loading feed'); ?>";
            return;
        }
        feed.innerHTML = response;
    });
})();
</script>
