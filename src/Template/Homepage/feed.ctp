<?php
$faker = Faker\Factory::create();
$this->assign('title', 'My Feed');
$this->assign('css', $this->Html->css('feed.css'));
?>
<section class="section">
    <div class="columns">
        <div class="column">
            <h1 class="is-size-2">My Feed</h1>
            <?php for ($i = 0; $i < 10; $i++): ?>
                <div class="box">
                    <?= nl2br($faker->paragraphs(2, true)); ?>
                </div>
            <?php endfor; ?>
        </div>
    </div>
</section>
