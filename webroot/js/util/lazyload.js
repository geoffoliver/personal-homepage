// TODO: Add functionality to display a throbber while the image is loading

(function() {
  // setup some variables
  var lazyObserver;
  var lazyImages = [];
  var lazyloadThrottleTimeout;
  var worker = null;

  if (window.Worker) {
    // make a worker for lazyloading images
    worker = new Worker('/js/util/lazyload-worker.js');

    // when the worker responds, we can update the image with the new src
    worker.onmessage = function(message) {
      var image = document.getElementById(message.data.id);
      // image.src = message.data.src;
      image.src = image.dataset.lazySrc;
    };
  }

  // puts the data-lazy-src of an image into the src of itself and then removes
  // the data-lazy-src attribute
  function loadImage(image) {
    // if the image is already loading, don't do jack shit
    if (image.dataset.loading) {
      return;
    }

    // make sure the image has an ID
    if (!image.id) {
      image.id = (new Date().getTime() + (Math.random() * 200)).toString(16);
    }

    // when the image loads, remove the data-lazy-src and the data-loading attributes
    image.onload = function() {
      delete image.dataset.lazySrc;
      delete image.dataset.loading;
      delete image.onload;
    };

    // so we don't try to load this image multiple times
    image.dataset.loading = true;

    // if we can, load this in a web worker
    if (worker) {
      worker.postMessage({
        id: image.id,
        url: image.dataset.lazySrc
      });
    } else {
      // set the src attribute
      image.src = image.dataset.lazySrc;
    }
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
        loadImage(img);
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
    console.log('lazy');

    if(lazyloadThrottleTimeout) {
      cancelAnimationFrame(lazyloadThrottleTimeout);
    }

    lazyloadThrottleTimeout = requestAnimationFrame(function() {
      lazyImages = document.querySelectorAll('img[data-lazy-src]');

      console.log('li', lazyImages);

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
      initLazyLoadImages();
    } else {
      console.info('Loading all images at once, that sucks. Upgrade your browser!');
      loadImages();
    }
  });

  // create an observer that listens for changes in the DOM so we can trigger
  // lazy loading
  var observer = new MutationObserver(function(mutations) {
    for (var mut of mutations) {
      // if something was added, let's try to find and fix iframe embeds
      if (mut.addedNodes.length > 0) {
        // find any code blocks that aren't being/have been highlighted
        var lazy = document.querySelectorAll('img[data-lazy-src]');
        if (lazy.length > 0) {
          lazyload();
        }
        return;
      }
    }
  });

  // tell the observer to start observing
  observer.observe(document.body, {childList: true, subtree: true});

})();
