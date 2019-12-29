(function() {
  // "fix" iframe embeds by slapping an extra class on the iframe parent so we
  // the iframes are displayed correctly... hopefully. this is really just a fix
  // for videos
  var fixIframeEmbeds = function() {
    // get all the iframes that haven't been fixed
    var iframes = document.querySelectorAll('.pf-oembed:not(.with-iframe) > iframe');
    var iframe;

    // nothing to do? later!
    if (iframes.length === 0) {
      return;
    }

    // loop over the iframes and append a class to their parent <div>
    for (var i = 0; i < iframes.length; i++) {
      iframe = iframes.item(i);
      iframe.parentElement.classList.add('with-iframe');
    }
  };

  // create an observer that listens for changes in the DOM so we can trigger
  // the iframe embed fix above
  var observer = new MutationObserver(function(mutations) {
    for (var mut of mutations) {
      // if something was added, let's try to find and fix iframe embeds
      if (mut.addedNodes.length > 0) {
        fixIframeEmbeds();
        return;
      }
    }
  });

  // tell the observer to start observing
  observer.observe(document.body, {childList: true, subtree: true});
})();
