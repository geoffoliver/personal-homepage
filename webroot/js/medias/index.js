(function() {
  // setup some variables
  var lazyObserver;
  var lazyImages = [];
  var lazyloadThrottleTimeout;

  // puts the data-lazy-src of an image into the src of itself
  function loadImage(image) {
    image.src = image.dataset['lazySrc'];
    delete image.dataset['lazySrc'];
  }

  // loads images all at once in the instance that the browser doesn't support
  // the IntersectionObserver
  function loadImages() {
    lazyImages = document.querySelectorAll('img[data-lazy-src]');
    for (let i = 0; i < lazyImages.length; i++) {
      loadImage(lazyImages.item(i));
    }
  }

  // observes an image for interaction (scrolling into view)
  function observeImage(images, observer) {
    images.forEach(function(image) {
      if (image.isIntersecting) {
        img = image.target;
        img.src = img.dataset['lazySrc'];
        delete img.dataset['lazySrc'];
        lazyObserver.unobserve(img);
      }
    });
  }

  // setup the intersection observer and tell it to watch all the lazy images
  function initLazyLoadImages() {
    lazyObserver = new IntersectionObserver(observeImage);

    lazyImages.forEach(function(image) {
      lazyObserver.observe(image);
    });

    document.addEventListener("scroll", lazyload);
    window.addEventListener("resize", lazyload);
    window.addEventListener("orientationChange", lazyload);

    lazyload();
  }

  // do the lazy loading of images
  function lazyload () {
    if(lazyloadThrottleTimeout) {
      cancelAnimationFrame(lazyloadThrottleTimeout);
    }

    lazyloadThrottleTimeout = requestAnimationFrame(function() {
      lazyImages = document.querySelectorAll('img[data-lazy-src]');

      var scrollTop = window.pageYOffset;
      lazyImages.forEach(function(img) {
          if(img.offsetTop < (window.innerHeight + scrollTop)) {
            loadImage(img);
          }
      });

      if(lazyImages.length == 0) {
        document.removeEventListener("scroll", lazyload);
        window.removeEventListener("resize", lazyload);
        window.removeEventListener("orientationChange", lazyload);
      }
    });
  }

  document.addEventListener('DOMContentLoaded', function() {
    if ("IntersectionObserver" in window) {
      console.log('Lazy loading images');
      initLazyLoadImages();
    } else {
      console.log('Loading all images at once. Oooof. Upgrade your browser!');
      loadImages();
    }
  });
})();
