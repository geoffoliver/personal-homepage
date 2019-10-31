(function() {
  // get links to media items
  var mediaLinks = document.querySelectorAll('a[href^="/medias/view"]');

  // no media links? ok, bye
  if (mediaLinks.length === 0) {
    return;
  }

  // create elements for the modal
  var container = document.createElement('div');
  var modal = document.createElement('div');
  var iframe = document.createElement('iframe');

  // hide the modal
  function hideMediaModal(event) {
    container.style = 'display: none;';
  }

  // show the modal for a media item
  function showMediaModal(event) {
    event.preventDefault();

    var link = this.href;

    iframe.src = link;

    container.style = '';
  }

  // add classes/styles for the container
  container.classList.add('media-modal-container');
  container.style = 'display: none;';

  // close the modal when somebody clicks on the container
  container.addEventListener('click', hideMediaModal);

  // add a class to the modal
  modal.classList.add('media-modal');

  // put the iframe into the modal
  modal.appendChild(iframe);

  // put the modal into the container
  container.appendChild(modal);

  // put the container into the body
  document.body.appendChild(container);

  // loop over the media links and add a click listener that will trigger the
  var ml;
  for (var i = 0; i < mediaLinks.length; i++) {
    ml = mediaLinks[i];

    ml.addEventListener('click', showMediaModal);
  }

})();
