<?php

use Cake\Utility\Hash;

$this->assign('title', Hash::get($settings, 'site-name'));
$this->assign('css', $this->Html->css('home.css'));
$this->assign('hero', true);

?>
<div class="columns">
  <div class="column is-one-quarter">
    <div class="sidebar sticky-sidebar">
      <div class="box">
        <h3 class="title is-6">
          <a href="/about">
            <span class="fas fa-fw fa-address-card"></span>
            <strong><?= __('About Me'); ?></strong>
          </a>
        </h3>
        <p>
          <?= Hash::get($settings, 'homepage-about'); ?>
        </p>
        <?php if ($friends->count()) : ?>
          <hr />
          <h3 class="title is-6">
            <a href="/friends">
              <span class="fas fa-fw fa-user-friends"></span>
              <strong><?= __('My Friends'); ?></strong>
            </a>
          </h3>
          <div id="homepage-friends" class="sidebar-thumbnail-grid">
            <?php foreach ($friends as $friend) : ?>
              <img src="" data-lazy-src="<?= $friend->icon; ?>" />
            <?php endforeach; ?>
          </div>
        <?php endif; ?>
      </div>
    </div>
  </div>
  <div class="column is-half">
    <?php if (count($posts) === 0) : ?>
      <div class="box">
        <h4 class="title is-6"><?= __('There is nothing to show here.'); ?></h4>
        <p><?= __('If you are the site owner, you should add your first post!'); ?></p>
      </div>
    <?php endif; ?>
    <?php
        foreach ($posts as $post) {
            echo $this->element('homepage/post', ['post' => $post]);
        }
    ?>
    <nav class="pagination is-centered" role="navigation" aria-label="pagination">
      <?= $this->Paginator->prev(__('Newer')); ?>
      <?= $this->Paginator->next(__('Older')); ?>
      <ul class="pagination-list">
        <?= $this->Paginator->numbers([
          'modulus' => 4,
          'first' => __('1')
        ]); ?>
      </ul>
    </nav>
  </div>
  <div class="column is-one-quarter">
    <div class="sidebar sticky-sidebar">
      <?php if ($photos->count()) : ?>
        <div class="box">
          <h3 class="title is-6">
            <a href="<?= $this->Url->build(['_name' => 'photos']); ?>">
              <span class="fas fa-fw fa-camera"></span>
              <strong><?= __('Photos'); ?></strong>
            </a>
          </h3>
          <div id="homepage-photos" class="sidebar-thumbnail-grid">
            <?php foreach ($photos as $photo) : ?>
              <?= $this->element('medias/thumbnail', ['media' => $photo]); ?>
            <?php endforeach; ?>
          </div>
        </div>
      <?php endif; ?>
      <?php if ($videos->count()) : ?>
        <div class="box">
          <h3 class="title is-6">
            <a href="<?= $this->Url->build(['_name' => 'videos']); ?>">
              <span class="fas fa-fw fa-video"></span>
              <strong><?= __('Videos'); ?></strong>
            </a>
          </h3>
          <div id="homepage-videos" class="sidebar-thumbnail-grid">
            <?php foreach ($videos as $video) : ?>
              <?= $this->element('medias/thumbnail', ['media' => $video]); ?>
            <?php endforeach; ?>
          </div>
        </div>
      <?php endif; ?>
    </div>
  </div>
</div>
