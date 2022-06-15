<?php
$Parsedown = new Parsedown();
$Parsedown->setStrictMode(true);

$this->assign('title', __('About Me'));
?>
<section class="section" id="aboutMe">
    <div class="columns">
        <div class="column">
            <h1 class="title"><?= __('About Me'); ?></h1>
            <?php
                if ($aboutIntro) {
                    echo $this->Html->para('is-size-4', $aboutIntro->value);
                    echo '<hr>';
                }
                if ($aboutPage) {
                    echo $Parsedown->text($aboutPage->value);
                } else {
                    echo $this->Html->div('box', __('There is no about page content.'));
                }
            ?>
        </div>
    </div>
</section>
