<?php
$this->assign('hero', true);
?>
<div id="<?= $this->fetch('pageId'); ?>">
    <?= $this->fetch("main"); ?>
</div>
<?= $this->fetch('body'); ?>
