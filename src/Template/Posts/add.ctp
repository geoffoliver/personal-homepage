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
                        'label' => __('Name'),
                        'placeholder' => __('Set the name for your post'),
                        'required' => true
                    ]);

                    echo $this->Form->control('content', [
                        'type' => 'textarea',
                        'placeholder' => __('Enter your post content here'),
                        'label' => __('Post Content'),
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
