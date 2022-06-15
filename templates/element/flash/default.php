<?php
$class = 'message';
if (!empty($params['class'])) {
    $class .= ' ' . $params['class'];
}
if (!isset($params['escape']) || $params['escape'] !== false) {
    $message = h($message);
}
?>
<article class="message <?= h($class) ?>" onclick="this.classList.add('hidden');">
  <div class="message-body">
    <?= $message; ?>
  </div>
</article>
