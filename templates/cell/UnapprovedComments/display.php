<?php

if ($unapproved > 0) {
    $total = number_format($unapproved);
    echo $this->Html->link(
            '<span class="fas fa-fw fa-comments"></span><span class="count">' . $total . '</span><span>&nbsp;' . __('Unapproved Comments') . '</span>',
            ['controller' => 'Comments', 'action' => 'unapproved'],
            [
                'class' => 'navbar-item unapproved-comments' . ($routeName === 'unapprovedComments' ? ' is-active' : ''),
                'title' => __('{0} Unapproved Comments', $total),
                'escape' => false
            ]
    );
}
