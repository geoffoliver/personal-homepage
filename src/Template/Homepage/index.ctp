<?php

use Cake\Utility\Hash;

$this->assign('title', Hash::get($settings, 'site-name'));
$this->assign('css', $this->Html->css('home.css'));
$this->append('script', $this->Html->script('lib/nanoajax/nanoajax.min.js'));
$this->append('script', $this->Html->script('util/ajax-paginate.js'));
$this->append('script', $this->Html->scriptBlock("(function() {
    window.ajaxPaginate && window.ajaxPaginate({
        container: 'homepagePosts',
        url: '/homepage/ajax-homepage'
    });
})();"));

$this->extend('/Homepage/shell');
$this->start('main');
?>
  <div class="column is-half" id="homepagePosts">
    <div class="box">
        <span class="fas fa-spin fa-spinner"></span> <?= __('Loading Posts...'); ?>
    </div>
  </div>
<?php
    $this->end();
?>
