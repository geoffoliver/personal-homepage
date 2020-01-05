<?php
$this->assign('title', __('Share On Your Website'));
$this->append('css', $this->Html->css('posts/add.css'));
$this->append('script', $this->Html->script('lib/dropzone/dropzone.js'));
$this->append('script', $this->Html->script('posts/add.js'));

$this->start('body');
    if (isset($saved) && $saved) {
        echo $this->Html->tag(
            'div',
            $this->Html->tag(
                'button',
                __('Close Window'),
                [
                    'class' => 'button is-dark',
                    'onClick' => 'window.close()'
                ]
            ),[
                'style' => 'display: flex; width: 100%; height: 100%; align-items: center; justify-content: center;'
            ]
        );
    } else {
    ?>
        <section class="section" id="addPostForm">
            <div class="columns">
                <div class="column is-three-fifths is-offset-one-fifth">
                    <h1 class="is-size-3"><?= __('Share Post'); ?></h1>
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
        <script type="text/javascript">
        (function() {
            document.getElementById('content').focus();
        })();
        </script>
    <?php
    }
$this->end();
