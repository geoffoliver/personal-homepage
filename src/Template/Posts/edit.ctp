<?php
$this->assign('title', __('Edit Post'));
$this->append('css', $this->Html->css('posts/add.css'));
// $this->append('css', $this->Html->css('/js/lib/quill/quill.core.css'));
$this->append('css', $this->Html->css('/js/lib/quill/quill.bubble.css'));
$this->append('script', $this->Html->script('lib/nanoajax/nanoajax.min.js'));
$this->append('script', $this->Html->script('lib/quill/quill.min.js'));
$this->append('script', $this->Html->script('lib/dropzone/dropzone.js'));
$this->append('script', $this->Html->script('posts/add.js'));
?>
<section class="section" id="addPostForm">
    <div class="columns">
        <div class="column is-three-fifths is-offset-one-fifth">
            <h1 class="is-size-3"><?= __('Edit Post'); ?></h1>
            <div class="box">
                <?= $this->element('posts/form', ['post' => $post]); ?>
            </div>
        </div>
    </div>
</section>
<template id="add-post-attachment-preview-template">
    <div class="add-post-attachment-item dz-preview dz-file-preview">
        <div class="dz-details">
            <img data-dz-thumbnail />
        </div>
        <div class="dz-progress"><span class="dz-upload" data-dz-uploadprogress></span></div>
    </div>
</template>
<template id="post-attachment-template">
    <?= $this->element('posts/media_item'); ?>
</template>
