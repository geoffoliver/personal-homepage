<?php
$this->assign('title', __('My Feed'));
// $this->assign('miniHero', true);

$this->append('css', $this->Html->css('feed.css'));
$this->append('script', $this->Html->script('lib/nanoajax/nanoajax.min.js'));
$this->append('script', $this->Html->script('util/ajax-paginate.js'));
$this->append('script', $this->Html->scriptBlock("(function() {
    document.addEventListener('DOMContentLoaded', () => {
        window.ajaxPaginate && window.ajaxPaginate({
            container: 'feedItems',
            url: '/homepage/ajax-feed'
        });
    });
})();"));

//$this->extend('/Homepage/shell');
//$this->start('content');
?>
    <div id="feedItems">
        <div class="feed-loader"><span class="fas fa-spin fa-spinner"></span> <?= __('Loading Feed...'); ?></div>
    </div>
<?php
   //$this->end();
?>
