<!DOCTYPE html>
<html>
<head>
  <?= $this->element('layout/head'); ?>
</head>
<body class="has-navbar-fixed-top error-page">
  <?= $this->element('layout/navigation'); ?>
  <main>
    <div class="container is-max-desktop">
      <?= $this->Flash->render() ?>
      <?= $this->element('layout/hero'); ?>
      <div class="content">
        <?= $this->fetch('content') ?>
      </div>
    </div>
  </main>
  <?= $this->element('layout/footer'); ?>
</body>
</html>
