<?php
$this->assign('title', __('Edit Media'));
$this->append('css', $this->Html->css('medias/edit.css'));
?>
<section class="section" id="editMedia">
    <div class="columns">
        <div class="column is-three-fifths is-offset-one-fifth">
            <h1 class="is-size-3"><?= __('Edit Media'); ?></h1>
            <div class="box">
                <?= $this->element('medias/form', ['media' => $media]); ?>
            </div>
        </div>
    </div>
</section>
