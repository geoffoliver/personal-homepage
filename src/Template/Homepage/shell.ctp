<?php
use Cake\Utility\Hash;
$this->assign('hero', true);
?>
<div id="<?= $this->fetch('pageId'); ?>">
    <div class="columns">
        <?= $this->fetch("main"); ?>
    </div>
</div>
<?= $this->fetch('body'); ?>
