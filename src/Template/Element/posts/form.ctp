<?php
echo $this->Form->create($post, ['id' => 'postForm']);
    echo $this->Form->control('name', [
        'type' => 'text',
        'label' => __('Name'),
        'placeholder' => __('Give your post a name.'),
        'required' => true,
        'maxlength' => 255,
    ]);

    echo $this->Form->control('content', [
        'type' => 'textarea',
        'label' => __('Body'),
        'placeholder' => __('What do you want to say?'),
    ]);

    echo $this->Form->control('source', [
        'type' => 'text',
        'label' => __('Source'),
        'placeholder' => __('A URL where people can see the original content, if applicable. Include http(s)://...'),
        'pattern' => 'https?://.+',
    ]);
?>
    <div class="add-post-checkboxes">
        <?php
            echo $this->Form->control('public', [
                'type' => 'checkbox',
                'label' => __('Public'),
                'checked' => $post->id ? $post->public : true
            ]);

            echo $this->Form->control('allow_comments', [
                'type' => 'checkbox',
                'label' => __('Allow Comments'),
                'checked' => $post->id ? $post->allow_comments : true
            ]);
        ?>
    </div>
    <div id="add-post-attachments-container">
        <div class="field">
            <label class="label">Upload Files</label>
            <div id="add-post-attachments-list">
                <div id="add-post-attachments">
                    <?php if ($post->medias): ?>
                        <?php foreach($post->medias as $media): ?>
                            <?= $this->element('posts/media_item', ['media' => $media]); ?>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
                <div id="add-post-attachment-button">
                    <span class="fas fa-plus"></span>
                </div>
            </div>
        </div>
    </div>
<?php
    echo $this->Form->button(
        $post->id ? __('Save Post') : __('Add Post'),
        [
            'type' => 'submit',
            'class' => 'button is-link'
        ]
    );

echo $this->Form->end();
?>
