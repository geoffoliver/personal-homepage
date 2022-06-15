<?php
$this->assign('title', __('Edit Media'));
$this->append('css', $this->Html->css('medias/edit.css'));
?>
<section class="section" id="editMedia">
    <div class="columns">
        <div class="column is-three-fifths is-offset-one-fifth">
            <h1 class="is-size-3"><?= __('Edit Media'); ?></h1>
            <?= $this->element('medias/form', ['media' => $media]); ?>
            <hr />
            <?= $this->Form->postLink(
                '<span class="fas fa-trash" aria-hidden="true"></span>&nbsp;' . __('Delete'),
                [
                    'controller' => 'Medias',
                    'action' => 'delete',
                    $media->id
                ],
                [
                    'confirm' => __('Are you sure you want to delete this item?\n\nYou can not undo this!'),
                    'escape' => false
                ]
            ); ?>
        </div>
    </div>
</section>
