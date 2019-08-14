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
    echo $this->fetch('script');
?>
