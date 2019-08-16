<?php
$this->assign('title', 'My Personal Homepage');
$this->assign('css', $this->Html->css('home.css'));

$this->assign('hero', 'Welcome to my personal homepage');
$this->assign('hero_subtitle', 'Thanks for visiting 😎');
?>
<div class="columns">
  <div class="column is-one-quarter">
    <div class="sidebar sticky-sidebar">
      <div class="box">
        <h3>
          <a href="/about">
            <i class="fas fa-fw fa-address-card"></i>
            <strong>About Me</strong>
          </a>
        </h3>
        <p>
          Lorem ipsum dolor sit amet, consectetur adipiscing elit. Curabitur
          maximus tempus lectus non commodo. 👍 🐶
        </p>
        <?php if ($friends->count()): ?>
          <hr />
          <h3>
            <a href="/friends">
              <i class="fas fa-fw fa-user-friends"></i>
              <strong>My Friends</strong>
            </a>
          </h3>
          <div id="homepage-friends" class="sidebar-thumbnail-grid">
            <?php foreach($friends as $friend): ?>
              <img src="<?= $friend->icon; ?>" />
            <?php endforeach; ?>
          </div>
        <?php endif; ?>
      </div>
      <?php if ($photos->count()): ?>
      <div class="box">
        <h3>
          <a href="/photos">
            <i class="fas fa-fw fa-camera"></i>
            <strong>Photos</strong>
          </a>
        </h3>
        <div id="homepage-photos" class="sidebar-thumbnail-grid">
          <?php foreach($photos as $photo): ?>
            <img src="/media/<?= $photo->square_thumbnail; ?>" />
          <?php endforeach; ?>
        </div>
      </div>
      <?php endif; ?>
      <?php if ($videos->count()): ?>
      <div class="box">
        <h3><strong>Videos</strong></h3>
        <div id="homepage-videos" class="homepage-thumbnail-grid">
          <?php foreach($videos as $video): ?>
            <img src="/media/<?= $video->square_thumbnail; ?>" />
          <?php endforeach; ?>
        </div>
      </div>
      <?php endif; ?>
    </div>
  </div>
  <div class="column">
    <?php if (count($posts) === 0): ?>
        <div class="box">
            <h4 class="is-size-4"><?= __('There is nothing to show here.'); ?></h4>
            <p><?= __('If you are the site owner, you should add your first post!'); ?></p>
        </div>
    <?php endif; ?>
    <?php foreach ($posts as $post): ?>
        <?= $this->element('homepage/post', ['post' => $post]); ?>
    <?php endforeach; ?>
  </div>
</div>
