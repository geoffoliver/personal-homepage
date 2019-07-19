<?php
$faker = Faker\Factory::create();
$this->assign('title', 'My Feed');
$this->append('css', $this->Html->css('feed.css'));
$this->append('css', $this->Html->css('/build/css/feed.css'));
?>
<div id="feedPage">
    <div id="myFeed">
        <div class="box">
            <i class="fas fa-spin fa-spinner"></i> Loading Feed...
        </div>
    </div>
</div>
<script type="text/javascript">
    var friends = <?= json_encode($friends); ?>;
</script>
<?php
echo $this->Html->script('/build/js/manifest.js');
echo $this->Html->script('/build/js/Feed.js');
?>
