<div class="add-post-attachment-item">
    <?php if (isset($media) && isset($media->square_thumbnail) && $media->square_thumbnail): ?>
        <img
            data-thumbnail
            src="<?= $this->Url->build([
                'controller' => 'Medias',
                'action' => 'download',
                $media->id,
                'square_thumbnail'
            ]); ?>"
        />
    <?php else: ?>
        <img data-thumbnail />
    <?php endif; ?>
    <div class="add-post-attachment-item-controls">
        <a href="#" class="edit-post-attachment button is-rounded is-dark is-small" title="Edit"><span class="fas fa-edit"></span></a>
        <a href="#" class="delete-post-attachment button is-rounded is-dark is-small" title="Delete"><span class="fas fa-trash"></span></a>
    </div>
    <?php if (isset($media) && isset($media->id) && $media->id): ?>
        <input data-media-id type="hidden" name="medias[_ids][]" value="<?= $media->id; ?>" />
    <?php else: ?>
        <input data-media-id type="hidden" name="new_media[]" />
    <?php endif; ?>
</div>
