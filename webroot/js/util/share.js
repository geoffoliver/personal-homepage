(function () {
  var shareLinks = document.querySelectorAll('.share-item');

  // all this logic shamelessly taken from https://stackoverflow.com/questions/400212/how-do-i-copy-to-the-clipboard-in-javascript
  var fallbackCopyTextToClipboard = function (text) {
    var textArea = document.createElement("textarea");
    textArea.value = text;
    textArea.style.position = "fixed";  //avoid scrolling to bottom
    document.body.appendChild(textArea);
    textArea.focus();
    textArea.select();

    var successful = false;

    try {
      successful = document.execCommand('copy');
      var msg = successful ? 'successful' : 'unsuccessful';
    } catch (err) {
      console.error('Fallback: Oops, unable to copy', err);
    }

    document.body.removeChild(textArea);
    return successful;
  };
  var copyTextToClipboard = function (text, callback) {
    if (!navigator.clipboard) {
      callback(fallbackCopyTextToClipboard(text));
      return;
    }
    navigator.clipboard.writeText(text).then(function () {
      callback(true);
    }, function (err) {
      console.error('Async: Could not copy text: ', err);
      callback(false);
    });
  };

  // past here is code that I actually wrote
  var shareItem = function (event) {
    var target = event.target;

    if (!target.dataset.url) {
      return;
    }

    copyTextToClipboard(target.dataset.url, function (copied) {
      if (copied) {
        alert("Link copied to clipboard!");
      } else {
        alert("Unable to copy link!");
      }
    });

    return false;
  };

  for (link of shareLinks) {
    link.addEventListener('click', shareItem);
  };
})();
