(function() {
  var iframes = document.querySelectorAll('.pf-oembed > iframe');
  var iframe;

  if (iframes.length === 0) {
    return;
  }

  for (var i = 0; i < iframes.length; i++) {
    iframe = iframes.item(i);
    iframe.parentElement.classList.add('with-iframe');
  }

})();
