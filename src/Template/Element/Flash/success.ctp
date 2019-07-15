<?php
if (!isset($params['escape']) || $params['escape'] !== false) {
    $message = h($message);
}
?>
<article class="message is-success" onclick="this.classList.add('hidden');">
  <div class="message-body">
    <?= $message; ?>
  </div>
</article>
