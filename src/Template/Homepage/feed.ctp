<?php
$faker = Faker\Factory::create();
$this->assign('title', 'My Feed');
$this->append('css', $this->Html->css('feed.css'));
$this->append('css', $this->Html->css('/build/css/feed.css'));
?>
<div id="feedPage">
    <div class="columns">
        <div class="column is-one-quarter">
            <div class="box">
                <h3><a href="/friends"><strong>Friends</strong></a></h3>
                <?php if ($friends->count()): ?>
                    <div class="sidebar-thumbnail-grid">
                        <?php foreach($friends as $friend): ?>
                            <?= $this->Html->image(
                                $friend->icon,
                                [
                                    'alt' => "Icon for {$friend->name}"
                                ]
                            ); ?>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <p>
                        You have not setup any friends yet.
                    </p>
                <?php endif; ?>
            </div>
        </div>
        <div class="column">
            <div id="myFeed">
                <div class="box">
                    <i class="fas fa-spin fa-spinner"></i> Loading Feed...
                </div>
            </div>
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
