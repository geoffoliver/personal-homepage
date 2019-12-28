(function() {
  // get a handle on the comments form
  var commentsForm = document.getElementById('commentsForm');

  // no comments form? ok, bye
  if (!commentsForm) {
    return;
  }

  // listen for the submit event on the comments form so we can disable the submi
  // button when a form is submitted.
  commentsForm.addEventListener('submit', function() {
    commentsForm.querySelector('button[type="submit"]').classList.add('is-loading');
  });
})();
