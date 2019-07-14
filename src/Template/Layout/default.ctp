<!DOCTYPE html>
<html>
<head>
    <?=$this->Html->charset()?>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>
        <?=$this->fetch('title')?>
    </title>
    <?=$this->Html->meta('icon')?>

    <?=$this->Html->css('base.css')?>

    <?=$this->fetch('meta')?>
    <?=$this->fetch('css')?>
    <?=$this->fetch('script')?>
    <script defer src="https://use.fontawesome.com/releases/v5.3.1/js/all.js"></script>
</head>
<body class="has-navbar-fixed-top">
  <nav class="navbar is-fixed-top is-link" role="navigation" aria-label="main navigation">
    <div class="container">
      <div class="navbar-brand">
        <a class="navbar-item" href="/" title="Home">
          <i class="fas fa-home"></i>
        </a>

        <a role="button" class="navbar-burger burger" aria-label="menu" aria-expanded="false" data-target="navbarBasicExample">
          <span aria-hidden="true"></span>
          <span aria-hidden="true"></span>
          <span aria-hidden="true"></span>
        </a>
      </div>

      <div id="navbarBasicExample" class="navbar-menu">
        <div class="navbar-end">
           <a href="#" class="navbar-item">About</a>
          <a href="#" class="navbar-item">Photos</a>
          <a href="#" class="navbar-item">Videos</a>
        </div>
      </div>
    </div>
  </nav>
  <main>
    <div class="container">
      <?php if ($this->fetch('hero')): ?>
        <section class="hero is-light" id="hero" style="background-image: url('/hero-background');">
          <div class="hero-body">
            <div class="container">
              <div id="hero-main">
                <div id="hero-profile-photo">
                  <figure class="image is-128x128">
                    <img class="is-rounded" src="/profile-photo" />
                  </figure>
                </div>
                <div id="hero-copy">
                  <h1 class="title">
                    <?=$this->fetch('hero');?>
                  </h1>
                  <?php if ($this->fetch('hero_subtitle')): ?>
                  <h2 class="subtitle">
                    <?=$this->fetch('hero_subtitle');?>
                  </h2>
                  <?php endif;?>
                </div>
              </div>
            </div>
          </div>
        </section>
      <?php endif;?>
      <?=$this->Flash->render()?>
      <?=$this->fetch('content')?>
    </div>
  </main>
  <footer class="footer">
    <div class="content has-text-centered">
      <p>
        <strong>My Personal Homepage</strong> by <a href="http://www.plan8studios.com" target="_blank">Plan8 Studios</a>
        All content, copyright <?= date('Y'); ?> site owner.
      </p>
    </div>
  </footer>
</body>
</html>
