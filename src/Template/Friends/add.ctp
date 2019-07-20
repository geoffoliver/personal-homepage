<?php
$this->assign('title', __('Add Friend'));
$this->append('script', $this->Html->script('lib/nanoajax/nanoajax.min.js'));
?>
<div id="addFriendPage">
    <div class="container">
        <div class="columns">
            <div class="column">
                <h1 class="is-size-2"><?= __('Add Friend'); ?></h1>
                <?php
                    echo $this->Form->create();
                        echo $this->Form->control(
                            'url',
                            [
                                'label' => __('Website URL'),
                                'type' => 'url',
                                'required' => true
                            ]
                        );
                        echo $this->Form->control(
                            'name',
                            [
                                'label' => __('Name'),
                                'type' => 'text',
                                'required' => true
                            ]
                        );
                        echo $this->Form->control(
                            'description',
                            [
                                'label' => __('Description'),
                                'type' => 'textarea'
                            ]
                        );
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
                        echo $this->Form->button(
                            __('Add Friend'),
                            [
                                'type' => 'submit',
                                'class' => 'button is-primary'
                            ]
                        );
                    echo $this->Form->end();
                ?>
            </div>
        </div>
    </div>
</div>
<script type="text/javascript">
(function() {
    var $ = function(id) {
        return document.getElementById(id);
    };

    var url = $('url');

    url.addEventListener('blur', function() {
        var urlValue = url.value.trim();
        if (!urlValue || urlValue.indexOf('http') !== 0) {
            return;
        }

        nanoajax.ajax({
            url: "/friends/fetch-details.json",
            method: "POST",
            body: "url=" + urlValue,
            responseType: "json",
            headers: {
                "X-CSRF-Token": "<?= $this->request->getParam('_csrfToken'); ?>"
            }
        }, function (code, response) {
            if (code !== 200) {
                return;
            }

            $('name').value = response.name;
            $('description').value = response.description;

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

            if (response.samePlatform) {
                $('feed-url').closest('.field').classList.add('hidden');
                $('icon').closest('.field').classList.add('hidden');
            } else {
                $('feed-url').closest('.field').classList.remove('hidden');
                $('icon').closest('.field').classList.remove('hidden');
            }
        });
    });
})();
</script>
