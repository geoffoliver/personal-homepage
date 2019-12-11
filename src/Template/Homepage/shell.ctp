<?php
use Cake\Utility\Hash;
$this->assign('hero', true);
?>
<div id="<?= $this->fetch('pageId'); ?>">
    <div class="columns">
    <div class="column is-one-quarter">
        <div class="sidebar sticky-sidebar">
        <div class="box">
            <h3 class="title is-6">
                <a href="<?= $this->Url->build(['_name' => 'about']); ?>">
                    <span class="fas fa-fw fa-address-card"></span>
                    &nbsp;
                    <strong><?= __('About Me'); ?></strong>
                </a>
            </h3>
            <p>
            <?= Hash::get($settings, 'homepage-about'); ?>
            </p>
            <?php if ($friends->count()) : ?>
            <hr />
            <h3 class="title is-6">
                <a href="<?= $this->Url->build(['_name' => 'friends']); ?>">
                    <span class="fas fa-fw fa-user-friends"></span>
                    &nbsp;
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
    <?= $this->fetch("main"); ?>
    <div class="column is-one-quarter">
        <div class="sidebar sticky-sidebar">
        <?php if ($photos->count()) : ?>
            <div class="box">
            <h3 class="title is-6">
                <a href="<?= $this->Url->build(['_name' => 'photos']); ?>">
                <span class="fas fa-fw fa-camera"></span>
                &nbsp;
                <strong><?= __('Photos'); ?></strong>
                </a>
            </h3>
            <div id="homepage-photos" class="sidebar-thumbnail-grid">
                <?php foreach ($photos as $photo) : ?>
                <?= $this->element('medias/thumbnail', ['media' => $photo, 'size' => 'square_thumbnail']); ?>
                <?php endforeach; ?>
            </div>
            </div>
        <?php endif; ?>
        <?php if ($videos->count()) : ?>
            <div class="box">
            <h3 class="title is-6">
                <a href="<?= $this->Url->build(['_name' => 'videos']); ?>">
                <span class="fas fa-fw fa-video"></span>
                &nbsp;
                <strong><?= __('Videos'); ?></strong>
                </a>
            </h3>
            <div id="homepage-videos" class="sidebar-thumbnail-grid">
                <?php foreach ($videos as $video) : ?>
                <?= $this->element('medias/thumbnail', ['media' => $video, 'size' => 'square_thumbnail']); ?>
                <?php endforeach; ?>
            </div>
            </div>
        <?php endif; ?>
        </div>
    </div>
    </div>
</div>
<?= $this->fetch('content'); ?>
