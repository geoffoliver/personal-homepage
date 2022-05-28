<?php
$this->assign('title', __('Login'));
?>
<section class="section">
    <div class="columns">
        <div class="column is-half is-offset-one-quarter">
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
                    'class' => 'button is-dark'
                ]);

            echo $this->Form->end();
            ?>
        </div>
    </div>
</section>
