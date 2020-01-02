(function () {
  // var shareLinks = document.querySelectorAll('.share-item');

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
    // this _should_ be the link that we clicked on... hopefully
    var target = event.target;

    // not a link, fuck right off
    if (target.tagName !== 'A') {
      return;
    }

    // if we don't have the right class, also fuck off
    if (target.classList.contains('share-item') === false) {
      return;
    }

    // no url, for real? jesus. bye!!
    if (!target.dataset.url) {
      return;
    }

    // user is trying to share an item from their feed. popup the share dialog
    if (target.dataset.shareLocal) {
      const here = document.location.protocol + '//' + document.location.hostname;
      window.open(
        here + '/posts/share?name=' + encodeURIComponent(target.dataset.name) + '&source=' + encodeURIComponent(target.dataset.url),
        'shareWindow',
        'width=650,height=750'
      );
      event.preventDefault();
      return false;
    }

    // just copy the URL to the thing and throw up a nasty alert... or not, whatever.
    copyTextToClipboard(target.dataset.url, function (copied) {
      if (copied) {
        alert("Link copied to clipboard!");
      } else {
        alert("Unable to copy link!");
      }
    });

    event.preventDefault();
    return false;
  };

  // listen for all the clicks, because the feed loads over ajax
  document.body.addEventListener('click', shareItem);
})();
