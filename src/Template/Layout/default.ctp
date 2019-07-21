<!DOCTYPE html>
<html>
<head>
    <?= $this->element('Layout/head'); ?>
</head>
<body class="has-navbar-fixed-top">
  <nav class="navbar is-fixed-top is-link" role="navigation" aria-label="main navigation">
    <div class="container">
      <div class="navbar-brand">
        <a class="navbar-item" href="/" title="Home">
          <i class="fas fa-home"></i>
        </a>

        <a role="button" class="navbar-burger burger" aria-label="menu" aria-expanded="false" data-target="navbarBasicExample">
          <span aria-hidden="true"></span>
          <span aria-hidden="true"></span>
          <span aria-hidden="true"></span>
        </a>
      </div>

      <div id="navbarBasicExample" class="navbar-menu">
        <div class="navbar-end">
            <?php if ($this->Identity->isLoggedIn()): ?>
                <a href="/" class="navbar-item"><?= __('My Feed'); ?></a>
                <a href="/homepage" class="navbar-item"><?= __('My Homepage'); ?></a>
                <div class="nav-divider"></div>
            <?php endif; ?>
            <a href="/friends" class="navbar-item"><?= __('Friends'); ?></a>
            <a href="#" class="navbar-item"><?= __('About'); ?></a>
            <a href="#" class="navbar-item"><?= __('Photos'); ?></a>
            <a href="#" class="navbar-item"><?= __('Videos'); ?></a>
            <?php if ($this->Identity->isLoggedIn()): ?>
                <div class="nav-divider"></div>
                <?= $this->Form->create(null, [
                    'id' => 'nav-logout-form',
                    'url' => [
                        'controller' => 'Users',
                        'action' => 'logout'
                    ]
                ]); ?>
                    <?= $this->Form->button(
                        '<strong>' . __('Logout') .' <i class="fas fa-sign-out-alt"></i></strong>',
                        [
                            'type' => 'submit'
                        ]
                    ); ?>
                <?= $this->Form->end(); ?>
            <?php else: ?>
                <a href="/users/login" class="navbar-item">
                    <strong><?= __('Login'); ?> <i class="fas fa-sign-in-alt"></i></strong>
                </a>
            <?php endif; ?>
        </div>
    </div>
  </nav>
  <main>
    <div class="container">
      <?php if ($this->fetch('hero')): ?>
        <section class="hero is-light" id="hero" style="background-image: url('/hero-background');">
          <div class="hero-body">
            <div class="container">
              <div id="hero-main">
                <div id="hero-profile-photo">
                  <figure class="image is-128x128">
                    <img class="is-rounded" src="/profile-photo" />
                  </figure>
                </div>
                <div id="hero-copy">
                  <h1 class="title">
                    <?=$this->fetch('hero');?>
                  </h1>
                  <?php if ($this->fetch('hero_subtitle')): ?>
                  <h2 class="subtitle">
                    <?=$this->fetch('hero_subtitle');?>
                  </h2>
                  <?php endif;?>
                </div>
              </div>
            </div>
          </div>
        </section>
      <?php endif;?>
      <?=$this->Flash->render()?>
      <?=$this->fetch('content')?>
    </div>
  </main>
  <?= $this->element('Layout/footer'); ?>
</body>
</html>
