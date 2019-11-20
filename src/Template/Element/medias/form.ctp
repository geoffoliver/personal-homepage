<?php
use Cake\Utility\Hash;

echo $this->Form->create($media, ['id' => 'mediaForm']);
    echo $this->Form->control('name', [
        'type' => 'text',
        'label' => __('Name'),
        'placeholder' => __('Give your media a name.'),
        'maxlength' => 255
    ]);

    echo $this->Form->control('description', [
        'type' => 'textarea',
        'label' => __('Description'),
        'placeholder' => __('Describe your media.')
    ]);

?>
    <div class="permissions-checkboxes">
        <?php

            echo $this->Form->control('public', [
                'type' => 'checkbox',
                'label' => __('Public'),
                'checked' => $media->public
            ]);

            echo $this->Form->control('allow_comments', [
                'type' => 'checkbox',
                'label' => __('Allow Comments'),
                'checked' => $media->allow_comments
            ]);
        ?>
    </div>
<?php
    echo $this->Form->button(
        __('Save Media'),
        [
            'type' => 'submit',
            'class' => 'button is-link'
        ]
    );

echo $this->Form->end();
?>
    <hr />
    <h2 class="title is-5"><?= __('Thumbnails'); ?></h2>
    <div class="thumbnails">
        <figure class="preview">
            <?= $this->Html->image(
                "/media/{$media->thumbnail}"
            ); ?>
            <figcaption><?= __('Fullsize'); ?></figcaption>
        </figure>
        <figure class="preview">
            <?= $this->Html->image(
                "/media/{$media->square_thumbnail}"
            ); ?>
            <figcaption><?= __('Square'); ?></figcaption>
        </figure>
    </div>
    <hr />
    <h2 class="title is-5"><?= __('Additional Information'); ?></h2>
    <div class="columns">
        <div class="column">
            <div class="field">
                <label class="label"><?= __('Uploaded'); ?></label>
                <div>
                    <?= $media->created->setTimezone(Hash::get($settings, 'timezone'))->format('F j, Y g:i A'); ?>
                </div>
            </div>
            <div class="field">
                <label class="label"><?= __('MIME Type'); ?></label>
                <div>
                    <?= $media->mime; ?>
                </div>
            </div>
            <div class="field">
                <label class="label"><?= __('Filesize'); ?></label>
                <div>
                    <?= $this->Number->toReadableSize($media->size); ?>
                </div>
            </div>
        </div>
        <div class="column">
            <div class="field">
                <label class="label"><?= __('Original Filename'); ?></label>
                <div>
                    <?= $this->Html->link(
                        $media->original_filename . '&nbsp;<span class="fas fa-external-link-alt"></span>',
                        "/media/{$media->local_filename}",
                        [
                            'target' => '_blank',
                            'escape' => false
                        ]
                    ); ?>
                </div>
            </div>
            <?php if ($media->album): ?>
                <div class="field">
                    <label class="label"><?= __('Album'); ?></label>
                    <div>
                        <?= $this->Html->link(
                            $media->album->name . '&nbsp;<span class="fas fa-external-link-alt"><span>',
                            [
                                '_name' => 'viewAlbum',
                                $media->album->id
                            ],
                            [
                                'target' => '_blank',
                                'escape' => false
                            ]
                        ); ?>
                    </div>
                </div>
            <?php endif; ?>
            <?php if ($media->post): ?>
                <div class="field">
                    <label class="label"><?= __('Post'); ?></label>
                    <div>
                        <?= $this->Html->link(
                            $media->post->name . '&nbsp;<span class="fas fa-external-link-alt"><span>',
                            [
                                '_name' => 'viewPost',
                                $media->post->id
                            ],
                            [
                                'target' => '_blank',
                                'escape' => false
                            ]
                        ); ?>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
