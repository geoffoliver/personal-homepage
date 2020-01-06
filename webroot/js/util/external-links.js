(function() {
  var setupExternalLinks = function() {
    var links = document.querySelectorAll('a');
    var here = document.location.protocol + '//' + document.location.hostname;
    for (link of links) {
      if (
        !link.target &&
        link.href.slice(0, 10) !== 'javascript' &&
        link.href.slice(0, here.length) !== here
      ) {
        link.target = '_blank';
      }
    }
  };

  // create an observer that listens for changes in the DOM so we can run the
  // `setupExternalLinks` function
  var observer = new MutationObserver(function(mutations) {
    for (var mut of mutations) {
      // if something was added, let's try to find and fix iframe embeds
      if (mut.addedNodes.length > 0) {
        setupExternalLinks();
      }
    }
  });

  // tell the observer to start observing
  observer.observe(document.body, {childList: true, subtree: true});
})();
