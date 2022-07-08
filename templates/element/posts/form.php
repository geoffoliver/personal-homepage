<?php
echo $this->Html->scriptBlock(sprintf(
    'var csrfToken = %s;',
    json_encode($this->request->getAttribute('csrfToken'))
));

echo $this->Form->create($post, ['id' => 'postForm']);

    echo '<div id="edit">';
        echo $this->Form->control('name', [
            'type' => 'text',
            'label' => false,
            'placeholder' => __('Title'),
            'maxlength' => 255,
            'autofocus' => true
        ]);

        echo $this->Form->control('content', [
            'type' => 'textarea',
            'label' => false,
            'required' => true,
            'placeholder' => __('What do you want to say?')
        ]);

        echo $this->Form->control('source', [
            'type' => 'text',
            'label' => __('Source'),
            'placeholder' => __('A URL where people can see the original content, if applicable. Include http(s)://...'),
            'pattern' => 'https?://.+',
        ]);

        if ($post->id && $post->embeds && $post->show_embeds) {
            echo $this->Form->control('embeds', [
                'label' => __('Embeds Code'),
                'type' => 'textarea',
            ]);
        }

        echo $this->Html->tag('hr');

        echo $this->Html->div('add-post-checkboxes', implode('', [
            $this->Form->control('public', [
                'type' => 'checkbox',
                'label' => __('Public'),
                'checked' => $post->id ? $post->public : true
            ]),
            $this->Form->control('allow_comments', [
                'type' => 'checkbox',
                'label' => __('Allow Comments'),
                'checked' => $post->id ? $post->allow_comments : true
            ]),
            $this->Form->control('is_link', [
                'type' => 'checkbox',
                'label' => __('Display as link'),
                'checked' => $post->id ? $post->is_link : false
            ]),
            $this->Form->control('show_embeds', [
                'type' => 'checkbox',
                'label' => __('Show Embeds'),
                'checked' => $post->id ? $post->show_embeds : true
            ]),
        ]));
?>
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

    echo '</div><div id="preview"><div id="postPreview"></div></div>';

    echo $this->Form->button(
        __('{0} Post', $post->id ? __('Save') : ($sharing ? __('Share') : __('Add'))),
        [
            'type' => 'submit',
            'class' => 'button is-dark'
        ]
    );

echo $this->Form->end();
?>
