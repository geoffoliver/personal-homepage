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
    var loadingMessage = "<i class='fas fa-spin fa-spinner'></i> <?= __('Loading...'); ?>";
    var loadErrorMessage = "<?= __('Error loading feed'); ?>";

    var loadFeed = function(page) {
        var url = "/homepage/ajax-feed";
        if (page) {
            url+= "?page=" + page
        }

        nanoajax.ajax({
            url: url
        }, function(status, response) {
            var pag = feed.querySelector('.feed-pagination');

            if (status !== 200) {
                if (pag) {
                    pag.innerHTML = loadErrorMessage;
                } else {
                    feed.innerHTML = loadErrorMessage;
                }
                return;
            }

            if (pag) {
                feed.removeChild(pag);
            }

            if (feed.getAttribute('loaded')) {
                feed.innerHTML += response;
            } else {
                feed.setAttribute('loaded', true);
                feed.innerHTML = response;
            }
        });
    }

    feed.addEventListener('click', function(e) {
        var element = e.target;
        if (element.classList.contains('paginate') && element.getAttribute('data-page')) {
            var page = parseInt(element.getAttribute('data-page'));

            if (isNaN(page) || page < 1) {
                return;
            }

            loadFeed(page);

            element.closest('div').innerHTML = loadingMessage;

            e.preventDefault();

            return false;
        }
    });

    loadFeed(<?= $this->request->getQuery('page', 'null'); ?>);
})();
</script>
