<!DOCTYPE html>
<html>
<head>
    <?=$this->element('layout/head');?>
</head>
<body class="simple">
    <main>
        <div class="container is-max-desktop">
            <div class="content">
                <?php
                    echo $this->Flash->render();
                    echo $this->fetch('content');
                ?>
            </div>
        </div>
    </main>
    <?php
        $this->append('script', $this->Html->script('https://use.fontawesome.com/releases/v5.12.0/js/all.js', ['defer']));
        $this->append('script', $this->Html->script('util/lazyload.js'));
        echo $this->fetch('script');
    ?>
</body>
</html>
