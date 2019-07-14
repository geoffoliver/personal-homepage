<?php
$this->assign('title', 'My Personal Homepage');
$this->assign('css', $this->Html->css('home.css'));

$this->assign('hero', 'Welcome to my personal homepage');
$this->assign('hero_subtitle', 'Thanks for visiting 😎');
?>
<div class="columns">
  <div class="column is-one-quarter">
    <div id="sidebar">
      <div class="box">
        <h3><strong>About Me</strong></h3>
        <p>
          Lorem ipsum dolor sit amet, consectetur adipiscing elit. Curabitur
          maximus tempus lectus non commodo. 👍 🐶
        </p>
        <hr />
        <h3><strong>My Friends</strong></h3>
        <p>...</p>
      </div>
      <div class="box">
        <h3><strong>Photos</strong></h3>
        <p>...</p>
      </div>
      <div class="box">
        <h3><strong>Videos</strong></h3>
        <p>...</p>
      </div>
    </div>
  </div>
  <div class="column">
    <?php foreach ($posts as $post): ?>
      <div class="box">
        <article class="media">
          <div class="media-content">
            <div class="content">
              <h2><?=$post->name;?></h2>
              <p>
                <?=nl2br($post->content);?>
              </p>
              <?php if ($post->medias): ?>
                <?php foreach ($post->medias as $media): ?>
                  <?php if ($media->thumbnail): ?>
                    <img src="/media/<?=$media->thumbnail;?>" />
                  <?php else: ?>

                  <?php endif;?>
                <?php endforeach;?>
              <?php endif;?>
            </div>
          </div>
        </article>
      </div>
    <?php endforeach; ?>
  </div>
</div>
