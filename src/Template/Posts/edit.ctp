<?php
$this->assign('title', __('Edit Post'));
$this->append('css', $this->Html->css('posts/add.css'));
$this->append('script', $this->Html->script('lib/dropzone/dropzone.js'));
$this->append('script', $this->Html->script('lib/marked/marked.js'));
$this->append('script', $this->Html->script('posts/add.js'));
?>
<section class="section" id="addPostForm">
    <div class="columns">
        <div class="column is-half">
            <h1 class="is-size-3"><?= __('Edit Post'); ?></h1>
            <div class="box">
                <?= $this->element('posts/form', ['post' => $post]); ?>
            </div>
        </div>
        <div class="column is-half">
            <h2 class="is-size-4" style="margin-top: 10px;"><?= __('Post Preview'); ?></h2>
            <div class="box">
                <div id="postPreview"></div>
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
