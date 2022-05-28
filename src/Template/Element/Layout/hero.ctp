<?php
use Cake\Utility\Hash;
$user = $this->Identity->get();
?>
<?php if ($this->fetch('hero')) : ?>
  <section class="hero is-light <?= $this->fetch('miniHero') ? 'mini' : ''; ?>" id="hero" style="background-image: url('/hero-background');">
    <div class="hero-body">
      <div class="container">
        <div id="hero-main">
          <?php /*
          <div id="hero-profile-photo">
            <figure class="image <?= $this->fetch('miniHero') ? 'is-48x48' : 'is-128x128'; ?>">
              <img class="is-rounded" data-lazy-src="/profile-photo" loading="lazy" />
            </figure>
          </div>
          */ ?>
          <div id="hero-copy">
            <?php if ($user): ?>
                <h1 class="title">
                    <?= __('Welcome back, {0}!', $user->name); ?>
                </h1>
                <h2 class="subtitle"><?= __('Nice to see you again 😀'); ?></h2>
            <?php else: ?>
                <h1 class="title">
                <?= Hash::get($settings, 'cover-title'); ?>
                </h1>
                <?php if (Hash::get($settings, 'cover-subtitle')): ?>
                <h2 class="subtitle">
                    <?= Hash::get($settings, 'cover-subtitle'); ?>
                </h2>
                <?php endif; ?>
            <?php endif; ?>
          </div>
        </div>
      </div>
    </div>
  </section>
<?php endif; ?>
