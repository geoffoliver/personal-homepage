<!DOCTYPE html>
<html>
<head>
    <?= $this->element('Layout/head'); ?>
</head>
<body class="has-navbar-fixed-top">
  <nav class="navbar is-fixed-top is-link" aria-label="main navigation">
    <div class="container">
      <div class="navbar-brand">
        <a class="navbar-item" href="/" title="Home">
          <span class="fas fa-home"></span>
          &nbsp;
          Site Name
        </a>
        <a role="button" class="navbar-burger burger" aria-label="menu" aria-expanded="false" data-target="navbarBasicExample">
          <span aria-hidden="true"></span>
          <span aria-hidden="true"></span>
          <span aria-hidden="true"></span>
        </a>
      </div>
      <div id="navbarBasicExample" class="navbar-menu">
        <div class="navbar-end">
            <?php
                echo $this->Html->link(
                    __('Friends'),
                    ['controller' => 'Friends', 'action' => 'index'],
                    ['class' => 'navbar-item']
                );
            ?>
            <a href="#" class="navbar-item"><?= __('About'); ?></a>
            <a href="#" class="navbar-item"><?= __('Photos'); ?></a>
            <a href="#" class="navbar-item"><?= __('Videos'); ?></a>
            <div class="nav-divider"></div>
            <?php
                if ($this->Identity->isLoggedIn()) {
                    echo $this->Html->link(
                        __('Feed'),
                        ['controller' => 'Homepage', 'action' => 'index'],
                        ['class' => 'navbar-item']
                    );
                    echo $this->Html->link(
                        __('Posts'),
                        ['controller' => 'Homepage', 'action' => 'index', 'homepage'],
                        ['class' => 'navbar-item']
                    );
                    echo $this->Html->link(
                        __('Settings'),
                        ['controller' => 'Settings', 'action' => 'index'],
                        ['class' => 'navbar-item']
                    );
                    echo '<div class="nav-divider"></div>';
                }
            ?>
            <?php
                if($this->Identity->isLoggedIn()) {
                    echo $this->Html->div('navbar-item', $this->Html->link(
                        __('Add Post'),
                        ['controller' => 'Posts', 'action' => 'add'],
                        ['class' => 'button is-white']
                    ));
                    echo $this->Form->create(null, [
                        'id' => 'nav-logout-form',
                        'url' => [
                            'controller' => 'Users',
                            'action' => 'logout'
                        ]
                    ]);
                        echo $this->Form->button(
                            '<strong>' . __('Logout') .' <span class="fas fa-sign-out-alt"></span></strong>',
                            [
                                'type' => 'submit'
                            ]
                        );
                    echo $this->Form->end();
                } else {
                    echo $this->Html->link(
                        __('Login') . '&nbsp;<span class="fas fa-sign-in-alt"></span>',
                        ['controller' => 'Users', 'action' => 'login'],
                        ['class' => 'navbar-item', 'escape' => false]
                    );
                }
            ?>
        </div>
      </div>
    </div>
  </nav>
  <main>
    <div class="container">
      <?=$this->Flash->render()?>
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
      <?=$this->fetch('content')?>
    </div>
  </main>
  <?= $this->element('Layout/footer'); ?>
</body>
</html>
