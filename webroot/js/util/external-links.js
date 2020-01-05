// TODO: Fix this up so it'll work on links added after the initial render (ajax feed)
(function() {
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
})();
