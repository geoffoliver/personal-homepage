<?php
use Cake\Utility\Hash;

$routeName = $this->request->getParam('_name') ? $this->request->getParam('_name') : null;
?>
<nav class="navbar is-fixed-top is-dark" aria-label="main navigation">
    <div class="navbar-brand">
        <a class="navbar-item h-card" href="<?=$this->Url->build(['_name' => 'homepage']);?>" title="Home" rel="me">
            <figure class="image is-24x24">
                <img class="is-rounded u-photo" src="/profile-photo" loading="lazy" alt="Profile Photo" />
            </figure>
            <span class="p-name"><?= Hash::get($settings, 'site-name') ;?></span>
            <p class="p-note">
                <?=Hash::get($settings, 'homepage-about');?>
            </p>
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
                    __('Following'),
                    ['_name' => 'followings'],
                    ['class' => 'navbar-item ' . ($routeName === 'followings' ? 'is-active' : '')]
                );
                echo $this->Html->link(
                    __('About'),
                    ['_name' => 'about'],
                    ['class' => 'navbar-item ' . ($routeName === 'about' ? 'is-active' : '')]
                );
                echo $this->Html->link(
                    __('Photos'),
                    ['_name' => 'photos'],
                    ['class' => 'navbar-item ' . ($routeName === 'photos' ? 'is-active' : '')]
                );
                echo $this->Html->link(
                    __('Videos'),
                    ['_name' => 'videos'],
                    ['class' => 'navbar-item ' . ($routeName === 'videos' ? 'is-active' : '')]
                );

                echo '<div class="nav-divider"></div>';

                if ($this->Identity->isLoggedIn()) {

                    echo $this->Html->link(
                        '<span class="fas fa-fw fa-globe"></span>&nbsp;' . __('Feed'),
                        ['_name' => 'feed'],
                        [
                            'class' => 'navbar-item ' . ($routeName === 'feed' ? 'is-active' : ''),
                            'title' => __('Feed'),
                            'escape' => false,
                        ]
                    );
                    echo $this->Html->link(
                        '<span class="fas fa-fw fa-plus-square"></span>&nbsp;' . __('Add Post'),
                        ['_name' => 'addPost'],
                        [
                            'class' => 'navbar-item ' . ($routeName === 'addPost' ? 'is-active' : ''),
                            'title' => __('Add Post'),
                            'escape' => false,
                        ]
                    );

                    echo $this->cell('UnapprovedComments', [$routeName]);

                    echo $this->Html->link(
                        '<span class="fas fa-fw fa-cog"></span><span>&nbsp;' . __('Settings') . '</span>',
                        ['controller' => 'Settings', 'action' => 'index'],
                        [
                            'class' => 'navbar-item ' . ($routeName === 'settings' ? 'is-active' : ''),
                            'title' => __('Settings'),
                            'escape' => false,
                        ]
                    );

                    echo $this->Form->create(null, [
                        'id' => 'nav-logout-form',
                        'url' => [
                            'controller' => 'Users',
                            'action' => 'logout',
                        ],
                    ]);

                    echo $this->Form->button(
                        '<span class="fas fa-fw fa-sign-out-alt"></span><span>&nbsp;' . __('Logout') . '</span>',
                        [
                            'type' => 'submit',
                            'title' => __('Logout'),
                            'escapeTitle' => false,
                            'escape' => false,
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
</nav>
