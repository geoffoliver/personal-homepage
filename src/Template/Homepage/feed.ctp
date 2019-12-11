<?php
$this->assign('title', __('My Feed'));
$this->append('css', $this->Html->css('feed.css'));

$this->extend('/Homepage/shell');
$this->start('main');
?>
    <div class="column is-half">
        <div id="feedItems">
            <div class="box">
                <span class="fas fa-spin fa-spinner"></span> <?= __('Loading Feed...'); ?>
            </div>
        </div>
    </div>
<?php
    $this->end();
    echo $this->Html->script('lib/nanoajax/nanoajax.min.js', ['inline' => true]);
?>
<script>
(function() {
    var feed = document.getElementById('feedItems');
    var loadErrorMessage = "<div class='box'><?= __('Error loading feed'); ?></div>";

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

            element.classList.add('is-loading');

            e.preventDefault();

            return false;
        }
    });

    loadFeed(<?= $this->request->getQuery('page', 'null'); ?>);
})();
</script>
