(function() {
  // no web worker? too bad, no code highlighting either.
  if (!window.Worker) {
    return;
  }

  // make a worker
  var worker = new Worker('/js/util/highlight-code-worker.js');

  // so we can keep track of what we've done
  var working = 'hl-work';
  var done = 'hl-done';

  // handle the message (highlighted code) from the worker
  worker.onmessage = function(message) {
    var element = document.getElementById(message.data.id);
    element.innerHTML = message.data.value;
    element.classList.remove(working);
    element.classList.add(done);
  };

  // highlight code in the worker
  var highlightCode = function(codeBlocks) {
    codeBlocks.forEach(function(block) {
      // so we don't pick this up again later
      block.classList.add(working);
      if (!block.id) {
        // generate a random ID for the element so we can find it when the worker
        // messages us back
        block.id = (new Date().getTime() + (Math.random() * 200)).toString(16);
      }
      // tell the worker to do some work
      worker.postMessage({id: block.id, value: block.textContent});
    });
  };

  // create an observer that listens for changes in the DOM so we can trigger
  // the code highlighting stuff
  var observer = new MutationObserver(function(mutations) {
    for (var mut of mutations) {
      // if something was added, let's try to find and fix iframe embeds
      if (mut.addedNodes.length > 0) {
        // find any code blocks that aren't being/have been highlighted
        var codeBlocks = document.querySelectorAll('code:not(.' + done + '):not(.' + working +')');
        if (codeBlocks.length > 0) {
          highlightCode(codeBlocks);
        }
        return;
      }
    }
  });

  // tell the observer to start observing
  observer.observe(document.body, {childList: true, subtree: true});
})();
