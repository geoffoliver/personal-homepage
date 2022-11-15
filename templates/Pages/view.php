<?php

    if (isset($config->title)) {
        $this->assign('title', $config->title);
    }

    echo $pageContent;
?>
