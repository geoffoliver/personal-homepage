<footer class="footer">
    <div class="content has-text-centered">
        <p class="is-size-7">
            Powered by <strong>My Personal Homepage</strong> by <a href="http://www.plan8studios.com" target="_blank">Plan8 Studios</a>
            <br />
            All content, &copy<?=date('Y');?> site owner.
        </p>
    </div>
</footer>
<?php
    echo $this->Html->script('https://use.fontawesome.com/releases/v5.3.1/js/all.js', ['defer']);
    echo $this->Html->script('//cdnjs.cloudflare.com/ajax/libs/highlight.js/9.15.9/highlight.min.js');

    $this->append('script', $this->Html->script('util/lazyload.js'));
    $this->append('script', $this->Html->script('util/fix-iframe-embeds.js'));
    $this->append('script', $this->Html->script('util/nav.js'));

    echo $this->fetch('script');
?>
<script type="text/javascript">
    hljs.initHighlightingOnLoad();
</script>
