<?php
$this->assign('title', __('Add Post'));
$this->append('css', $this->Html->css('posts/add.css'));
$this->append('script', $this->Html->script('lib/dropzone/dropzone.js'));
$this->append('script', $this->Html->script('posts/add.js'));
?>
<section class="section" id="addPostForm">
    <div class="columns">
        <div class="column is-three-fifths is-offset-one-fifth">
            <h1 class="is-size-3">Add Post</h1>
            <div class="box">
                <?php
                echo $this->Form->create();
                    echo $this->Form->control('name', [
                        'type' => 'text',
                        'label' => __('Title'),
                        'placeholder' => __('Give your post a title'),
                        'required' => true,
                        'maxlength' => 255
                    ]);

                    echo $this->Form->control('content', [
                        'type' => 'textarea',
                        'label' => __('Content'),
                        'placeholder' => __('What do you want to say?'),
                    ]);
                ?>
                    <div id="add-post-attachments-container">
                        <div class="field">
                            <label class="label">Upload Files</label>
                            <div id="add-post-attachments-list">
                                <div id="add-post-attachments"></div>
                                <div id="add-post-attachment-button">
                                    <span class="fas fa-plus"></span>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php
                    echo $this->Form->button(__('Add Post'), [
                        'type' => 'submit',
                        'class' => 'button is-link'
                    ]);

                echo $this->Form->end();
                ?>
            </div>
        </div>
    </div>
</section>
<template id="add-post-attachment-preview-template">
    <div class="add-post-attachment-item dz-preview dz-file-preview">
        <div class="dz-details">
            <!--
            <div class="dz-filename"><span data-dz-name></span></div>
            <div class="dz-size" data-dz-size></div>
            -->
            <img data-dz-thumbnail />
        </div>
        <div class="dz-progress"><span class="dz-upload" data-dz-uploadprogress></span></div>
        <!--
        <div class="dz-success-mark"><span>✔</span></div>
        <div class="dz-error-mark"><span>✘</span></div>
        <div class="dz-error-message"><span data-dz-errormessage></span></div>
        -->
    </div>
</template>
<template id="post-attachment-template">
    <div class="add-post-attachment-item">
        <img data-thumbnail />
        <div class="add-post-attachment-item-controls">
            <a href="#" class="button is-rounded is-dark is-small" title="Edit"><span class="fas fa-edit"></span></a>
            <a href="#" class="button is-rounded is-dark is-small" title="Delete"><span class="fas fa-trash"></span></a>
        </div>
    </div>
</template>
