javascript:(() => {
  var p = document.title;
  var q = location.href;
  var d = '';

  if(document.getSelection) {
    d = document.getSelection().toString();
  }

  if (!d || d.trim() === '') {
    var selectors = {
      'facebook.com': '*[data-ad-preview]',
      'twitter.com': '*[data-testid=\'tweetText\']'
    };

    Object.keys(selectors).some((sel) => {
      if (document.location.href.includes(sel)) {
        var post = document.querySelector(selectors[sel]);
        if (post) {
          d = post.textContent;
          return true;
        }
      }
    });
  }

  void (
    open(
      '__DOMAIN__posts/share?source=' + encodeURIComponent(q) + '&body=' + encodeURIComponent(d) + '&name=' + encodeURIComponent(p),
      'Share',
      'toolbar=no,width=650,height=750'
    )
  );
})();
