(function() {
  // setup some variables
  var lazyObserver;
  var lazyImages = [];
  var worker = null;
  var useNative = ('loading' in HTMLImageElement.prototype);

  if (window.Worker) {
    // make a worker for lazyloading images
    worker = new Worker('/js/util/lazyload-worker.js');

    // when the worker responds, we can update the image with the new src
    worker.onmessage = function(message) {
      var image = document.getElementById(message.data.id);
      if (!image.dataset.lazySrc) {
        return;
      }
      image.src = image.dataset.lazySrc;
    };
  }

  function getLazyImages() {
    lazyImages = document.querySelectorAll('*[data-lazy-src]');
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
    var removeLazyloadStuff = function() {
      delete image.dataset.lazySrc;
      delete image.dataset.loading;
      delete image.onload;
    };

    if (image.tagName === "VIDEO") {
      image.oncanplay = removeLazyloadStuff;
    } else if (image.tagName === "IMG") {
      image.onload = removeLazyloadStuff;
    }

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

  // observes an image for interaction (scrolling into view)
  function observeImage(images) {
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
    getLazyImages();
    lazyImages.forEach(function(image) {
      if (useNative) {
        // yay, native lazy loading support!!
        image.src = image.dataset.lazySrc;
        delete image.dataset.lazySrc;
      } else {
        lazyObserver.observe(image);
      }
    });
  }

  document.addEventListener('DOMContentLoaded', function() {
    lazyObserver = new IntersectionObserver(observeImage, {
      rootMargin: '200px 0px',
      threshold: 0
    });

    initLazyLoadImages();

    // create an observer that listens for changes in the DOM so we can trigger
    // lazy loading
    var observer = new MutationObserver(function(mutations) {
      for (var mut of mutations) {
        // if something was added, let's try to find and fix iframe embeds
        if (mut.addedNodes.length > 0) {
          initLazyLoadImages();
          return;
        }
      }
    });

    // tell the observer to start observing
    observer.observe(document.body, {childList: true, subtree: true});
  });
})();
