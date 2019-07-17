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
              <img src="<?= $friend->url; ?>/profile-photo" />
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
            <img src="/media/<?= $photo->thumbnail; ?>" />
          <?php endforeach; ?>
        </div>
      </div>
      <?php endif; ?>
      <?php if ($videos->count()): ?>
      <div class="box">
        <h3><strong>Videos</strong></h3>
        <div id="homepage-videos" class="homepage-thumbnail-grid">
          <?php foreach($videos as $video): ?>
            <img src="/media/<?= $video->thumbnail; ?>" />
          <?php endforeach; ?>
        </div>
      </div>
      <?php endif; ?>
    </div>
  </div>
  <div class="column">
    <?php foreach ($posts as $post): ?>
      <div class="box">
        <article class="media">
          <div class="media-content">
            <div class="content">
              <h1 class="is-marginless is-size-4">
                <a href="/posts/<?= $post->id; ?>/<?= $post->url_alias; ?>"><?=$post->name;?></a>
              </h1>
              <h5 class="is-size-7 has-text-grey-light"><?= $post->created->format('F j, Y \a\t G:i a'); ?></h5>
              <p>
                <?=nl2br($post->content);?>
              </p>
              <?php if ($post->medias): ?>
                <?php foreach ($post->medias as $media): ?>
                  <?php if ($media->thumbnail): ?>
                    <img src="/media/<?=$media->thumbnail;?>" />
                  <?php else: ?>
                    ...
                  <?php endif;?>
                <?php endforeach;?>
              <?php endif;?>
              <hr />
              <nav class="level is-mobile">
                <div class="level-left">
                  <a class="level-item" aria-label="reply">
                    <span class="icon is-small">
                      <i class="fas fa-comment" aria-hidden="true"></i>
                    </span>
                    &nbsp;
                    <?= count($post->comments); ?> Comments
                  </a>
                  <a class="level-item" aria-label="retweet">
                    <span class="icon is-small">
                      <i class="fas fa-share" aria-hidden="true"></i>
                    </span>
                    &nbsp;
                    Share
                  </a>
                </div>
              </nav>
            </div>
          </div>
        </article>
      </div>
    <?php endforeach; ?>
  </div>
</div>
