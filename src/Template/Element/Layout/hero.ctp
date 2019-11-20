<?php if ($this->fetch('hero')) : ?>
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
              <?= $this->fetch('hero'); ?>
            </h1>
            <?php if ($this->fetch('hero_subtitle')) : ?>
              <h2 class="subtitle">
                <?= $this->fetch('hero_subtitle'); ?>
              </h2>
            <?php endif; ?>
          </div>
        </div>
      </div>
    </div>
  </section>
<?php endif; ?>
