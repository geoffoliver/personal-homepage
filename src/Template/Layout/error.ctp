<!DOCTYPE html>
<html>
<head>
  <?= $this->element('Layout/head'); ?>
</head>
<body class="has-navbar-fixed-top error-page">
  <?= $this->element('Layout/navigation'); ?>
  <main>
    <div class="container">
      <?= $this->Flash->render() ?>
      <?= $this->element('Layout/hero'); ?>
      <div class="content">
        <?= $this->fetch('content') ?>
      </div>
    </div>
  </main>
  <?= $this->element('Layout/footer'); ?>
</body>
</html>
