<?php
$this->assign('title', __('Add Friend'));
$this->assign('css', $this->Html->css('friends/add.css'));
$this->append('script', $this->Html->script('lib/nanoajax/nanoajax.min.js'));
?>
<section class="section" id="addFriendPage">
    <div class="container">
        <div class="columns">
            <div class="column is-three-fifths is-offset-one-fifth">
                <h1 class="is-size-3"><?= __('Add Friend'); ?></h1>
                <?php
                    echo $this->Form->create();
                        echo $this->Form->control(
                            'url',
                            [
                                'label' => __('Website URL'),
                                'type' => 'url',
                                'required' => true,
                                'templateVars' => [
                                    'help' => $this->Html->para(
                                        'help',
                                        __('Paste/enter the URL to your friend\'s website and we will try to fill out the rest of the fields automatically.')
                                    )
                                ]
                            ]
                        );
                ?>
                <div class="add-friend-fields">
                <?php
                        echo $this->Form->control(
                            'name',
                            [
                                'label' => __('Name'),
                                'type' => 'text',
                                'required' => true
                            ]
                        );
                        // echo $this->Form->control(
                        //     'description',
                        //     [
                        //         'label' => __('Description'),
                        //         'type' => 'textarea'
                        //     ]
                        // );
                        echo $this->Form->control(
                            'feed_url',
                            [
                                'label' => __('Feed URL'),
                                'type' => 'select',
                                'required' => true,
                                'options' => []
                            ]
                        );
                        echo $this->Form->control(
                            'icon',
                            [
                                'label' => __('Icon'),
                                'type' => 'select',
                                'options' => []
                            ]
                        );
                ?>
                    <div id="addFriendFieldOverlay" style="display: none;">
                        <span class="fas fa-spin fa-spinner"></span> Loading...
                    </div>
                </div>
                <?php
                        echo $this->Form->button(
                            __('Add Friend'),
                            [
                                'type' => 'submit',
                                'class' => 'button is-dark'
                            ]
                        );
                    echo $this->Form->end();
                ?>
            </div>
        </div>
    </div>
</section>
<script type="text/javascript">
(function() {
    var $ = function(id) {
        return document.getElementById(id);
    };

    var url = $('url');
    var overlay = $('addFriendFieldOverlay');

    $('feed-url').closest('.field').classList.add('hidden');
    $('icon').closest('.field').classList.add('hidden');

    url.addEventListener('blur', function() {
        var urlValue = url.value.trim();
        if (!urlValue || urlValue.indexOf('http') !== 0) {
            return;
        }

        overlay.style.display = null;

        nanoajax.ajax({
            url: "/friends/fetch-details.json",
            method: "POST",
            body: "url=" + urlValue,
            responseType: "json",
            headers: {
                "X-CSRF-Token": "<?= $this->request->getAttribute('csrfToken'); ?>"
            }
        }, function (code, response) {
            overlay.style.display = 'none';

            if (code !== 200) {
                return;
            }

            $('name').value = response.name;
            // $('description').value = response.description;

            var feedTags = [];
            if (response.feeds && response.feeds.length) {
                feedTags = response.feeds.map(function(f) {
                    return '<option value="' + f +'">' + f + '</option>';
                });
            }
            $('feed-url').innerHTML = feedTags.join('');

            var icons = [];
            if (response.icons && response.icons.length) {
                icons = response.icons.map(function(f) {
                    return '<option value="' + f +'">' + f + '</option>';
                });
            }
            $('icon').innerHTML = icons.join('');

            if (!response.samePlatform) {
                $('feed-url').closest('.field').classList.remove('hidden');
                $('icon').closest('.field').classList.remove('hidden');
            } else {
                $('feed-url').closest('.field').classList.add('hidden');
                $('icon').closest('.field').classList.add('hidden');
            }
        });
    });
})();
</script>
