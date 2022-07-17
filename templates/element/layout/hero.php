<?php
use Cake\Utility\Hash;
$user = $this->Identity->get();
?>
<?php if ($this->fetch('hero')) : ?>
  <section class="hero is-light <?= $this->fetch('miniHero') ? 'mini' : ''; ?>" id="hero" style="background-image: url('/hero-background');">
    <div class="hero-body">
      <div class="container">
        <div id="hero-main">
          <div id="hero-copy">
            <?php if ($user): ?>
                <h1 class="title is-spaced">
                    <span>
                        <?= __('Welcome back, {0}!', $user->name); ?>
                    </span>
                </h1>
                <h2 class="subtitle">
                    <span>
                        <?= __('Nice to see you again 😀'); ?>
                    </span>
                </h2>
            <?php else: ?>
                <h1 class="title is-spaced">
                    <span>
                        <?= Hash::get($settings, 'cover-title'); ?>
                    </span>
                </h1>
                <?php if (Hash::get($settings, 'cover-subtitle')): ?>
                <h2 class="subtitle">
                    <span>
                        <?= Hash::get($settings, 'cover-subtitle'); ?>
                    </span>
                </h2>
                <?php endif; ?>
            <?php endif; ?>
          </div>
        </div>
      </div>
    </div>
  </section>
<?php endif; ?>
