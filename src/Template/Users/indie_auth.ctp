<?php
$this->assign('title', __('Edit Post'));
$this->append('css', $this->Html->css('users/indie-auth.css'));
?>
<section class="section" id="indie-auth">
    <?= $this->Form->create(); ?>
    <div class="columns">
        <div class="column is-half is-offset-one-quarter">
            <div class="box">
                <h1 class="is-size-3"><?= __('Indie Auth Login'); ?></h1>
                <p>
                    <?= __('You are attempting to login with client <strong>{0}</strong>.', htmlspecialchars($clientId)); ?>
                </p>
                <?php if ($scopes): ?>
                    <div class="scopes">
                        <p>
                            <?= __('It is requesting the following scopes, uncheck any you do not wish to grant:'); ?>
                        </p>
                        <?php
                            foreach ($scopes as $scope) {
                                echo $this->Form->control('scopes[]', [
                                    'id' => false,
                                    'type' => 'checkbox',
                                    'value' => $scope,
                                    'label' => ucfirst($scope),
                                    'checked' => true
                                ]);
                            }
                        ?>
                    </div>
                <?php endif; ?>
                <p>
                    <?= __('After login, you will be redirected to <strong>{0}</strong>.', htmlspecialchars($redirectUri)); ?>
                </p>
            </div>
            <div class="box">
                <h2 class="is-size-4"><?= __('Login'); ?></h2>
                <?php
                    echo $this->Form->control('email', [
                        'type' => 'email',
                        'label' => __('Email Address'),
                        'required' => true
                    ]);

                    echo $this->Form->control('password', [
                        'type' => 'password',
                        'label' => __('Password'),
                        'required' => true
                    ]);

                    echo $this->Form->button(__('Login'), [
                        'type' => 'submit',
                        'class' => 'button is-dark'
                    ]);

                    echo $this->Form->hidden('me', ['value' => $me]);
                    echo $this->Form->hidden('client_id', ['value' => $clientId]);
                    echo $this->Form->hidden('redirect_uri', ['value' => $redirectUri]);
                    echo $this->Form->hidden('state', ['value' => $state]);
                    echo $this->Form->hidden('response_type', ['value' => $responseType]);
                ?>
            </div>
        </div>
    </div>
    <?= $this->Form->end(); ?>
</section>
