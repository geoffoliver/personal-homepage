(function() {
  var commentsForm = document.getElementById('commentsForm');

  if (!commentsForm) {
    return;
  }

  commentsForm.addEventListener('submit', function() {
    commentsForm.querySelector('button[type="submit"]').classList.add('is-loading');
  });
})();
