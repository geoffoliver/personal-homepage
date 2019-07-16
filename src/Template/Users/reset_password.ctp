<?php
$this->loadHelper('Form', [
    'templates' => 'bulma_form',
]);
?>
<section class="section">
    <div class="columns">
        <div class="column is-half is-offset-one-quarter">
            <div class="box">
                <h1 class="is-size-4">
                    Enter your new password below
                </h1>
                <hr />
                <?php
                echo $this->Form->create();

                    echo $this->Form->control('password', [
                        'type' => 'password',
                        'label' => __('New Password'),
                        'required' => true
                    ]);

                    echo $this->Form->button(__('Reset Password'), [
                        'type' => 'submit',
                        'class' => 'button is-link'
                    ]);

                echo $this->Form->end();
                ?>
            </div>
        </div>
    </div>
</section>
