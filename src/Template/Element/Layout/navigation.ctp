<?php
use Cake\Utility\Hash;
?>
<nav class="navbar is-fixed-top is-link" aria-label="main navigation">
    <div class="container">
        <div class="navbar-brand">
            <a class="navbar-item" href="<?= $this->Url->build(['_name' => 'homepage']); ?>" title="Home">
                <?= Hash::get($settings, 'site-name'); ?>
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
                        ['_name' => 'about'],
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

                    echo '<div class="nav-divider"></div>';

                    if ($this->Identity->isLoggedIn()) {
                        echo $this->Html->link(
                                '<span class="fas fa-fw fa-plus-square"></span>&nbsp;' . __('Add Post'),
                                ['controller' => 'Posts', 'action' => 'add'],
                                ['class' => 'navbar-item', 'title' => __('Add Post'), 'escape' => false]
                        );
                        echo $this->Html->link(
                                '<span class="fas fa-fw fa-globe"></span>&nbsp;' . __('Feed'),
                                ['_name' => 'feed'],
                                ['class' => 'navbar-item', 'title' => __('Feed'), 'escape' => false]
                        );
                        /*
                        echo $this->Html->link(
                                '<span class="fas fa-fw fa-user"></span><span>&nbsp;' . __('Homepage') . '</span>',
                                ['controller' => 'Homepage', 'action' => 'index', 'homepage'],
                                ['class' => 'navbar-item', 'title' => __('Homepage'), 'escape' => false]
                        );
                        */
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
