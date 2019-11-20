<nav class="navbar is-fixed-top is-link" aria-label="main navigation">
  <div class="container">
    <div class="navbar-brand">
      <a class="navbar-item" href="/" title="Home">
        <figure class="image is-24x24">
          <img class="is-rounded" src="/profile-photo" />
        </figure>
        Geoffrey Oliver
      </a>
      <a role="button" class="navbar-burger burger" aria-label="menu" aria-expanded="false" data-target="primaryNav">
        <span aria-hidden="true"></span>
        <span aria-hidden="true"></span>
        <span aria-hidden="true"></span>
      </a>
    </div>
    <div id="primaryNav" class="navbar-menu">
      <div class="navbar-end">
        <?php
        echo $this->Html->link(
          __('Friends'),
          ['controller' => 'Friends', 'action' => 'index'],
          ['class' => 'navbar-item']
        );
        echo $this->Html->link(
          __('About'),
          '#',
          ['class' => 'navbar-item']
        );
        echo $this->Html->link(
          __('Photos'),
          ['_name' => 'photos'],
          ['class' => 'navbar-item']
        );
        echo $this->Html->link(
          __('Videos'),
          ['_name' => 'videos'],
          ['class' => 'navbar-item']
        );
        ?>
        <div class="nav-divider"></div>
        <?php
        if ($this->Identity->isLoggedIn()) {
          echo $this->Html->link(
            '<span class="fas fa-fw fa-plus-square"></span>&nbsp;' . __('Add Post'),
            ['controller' => 'Posts', 'action' => 'add'],
            ['class' => 'navbar-item', 'title' => __('Add Post'), 'escape' => false]
          );
          echo $this->Html->link(
            '<span class="fas fa-fw fa-rss"></span><span>&nbsp;' . __('My Feed') . '</span>',
            ['controller' => 'Homepage', 'action' => 'index'],
            ['class' => 'navbar-item', 'title' => __('Feed'), 'escape' => false]
          );
          echo $this->Html->link(
            '<span class="fas fa-fw fa-home"></span><span>&nbsp;' . __('Homepage') . '</span>',
            ['controller' => 'Homepage', 'action' => 'index', 'homepage'],
            ['class' => 'navbar-item', 'title' => __('My Homepage'), 'escape' => false]
          );
          echo $this->Html->link(
            '<span class="fas fa-fw fa-cog"></span><span>&nbsp;' . __('Settings') . '</span>',
            ['controller' => 'Settings', 'action' => 'index'],
            ['class' => 'navbar-item', 'title' => __('Settings'), 'escape' => false]
          );
          echo $this->Form->create(null, [
            'id' => 'nav-logout-form',
            'url' => [
              'controller' => 'Users',
              'action' => 'logout'
            ]
          ]);
          echo $this->Form->button(
            '<span class="fas fa-fw fa-sign-out-alt"></span><span>&nbsp;' . __('Logout') . '</span>',
            [
              'type' => 'submit',
              'title' => __('Logout')
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
