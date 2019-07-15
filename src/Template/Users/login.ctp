<?php
$this->loadHelper('Form', [
    'templates' => 'bulma_form',
]);
?>
<section class="section">
    <div class="columns">
        <div class="column is-half is-offset-one-quarter">
            <div class="box">
                <?php
                echo $this->Form->create();

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
                        'class' => 'button is-link'
                    ]);

                echo $this->Form->end();
                ?>
            </div>
        </div>
    </div>
</section>
