<!DOCTYPE html>
<html>
<head>
    <?=$this->element('Layout/head');?>
</head>
<body class="popup">
    <main>
        <div class="container">
            <div class="content">
                <?=$this->Flash->render()?>
                <?=$this->fetch('content')?>
            </div>
        </div>
    </main>
    <?php
        $this->append('script', $this->Html->script('https://use.fontawesome.com/releases/v5.3.1/js/all.js', ['defer']));
        echo $this->fetch('script');
    ?>
</body>
</html>
