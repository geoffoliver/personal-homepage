// TODO: handle adding/evaluating <script src="..."> tags from response
(function () {
  // lets us do ajax pagination, so long as that pagination is just fetching the
  // next page of results and appending them.
  window.ajaxPaginate = function (props) {
    if (!window.nanoajax) {
      alert('Missing Nanoajax! Cannot continue.');
      return;
    }

    // this is where the paginated content will live
    var container = document.getElementById(props.container);

    // a nice error message in case things go wrong
    var loadErrorMessage = "<div class='box'><?= __('Error loading page'); ?></div>";

    // this does all the work of retrieving the page and stuffing the result
    // back into the page
    var loadPage = function (page) {
      // the URL we want to load
      var url = props.url;

      // are we paginating?
      if (page) {
        url += "?page=" + page
      }

      // make the ajax call
      nanoajax.ajax({
        url: url
      }, function (status, response) {
        // look for the pagination control
        var pag = container.querySelector('.load-next');

        // something went wrong. shite
        if (status !== 200) {
          if (pag) {
            // we have pagination, but something went wrong
            pag.innerHTML = loadErrorMessage;
          } else {
            // there was no pagination, but we still need to display the message
            container.innerHTML = loadErrorMessage;
          }
          // bye
          return;
        }

        // we don't need the pagination anymore
        if (pag) {
          container.removeChild(pag);
        }

        if (container.getAttribute('loaded')) {
          // append new conten
          container.innerHTML += response;
        } else {
          // first load, just set the innerHTML and be done
          container.setAttribute('loaded', true);
          container.innerHTML = response;
        }
      });
    }

    // listen for clicks on the container that will trigger the pagination event
    container.addEventListener('click', function (e) {
      var element = e.target;

      // make sure the thing being clicked has the appropriate data we need to paginate
      if (element.classList.contains('paginate') && element.getAttribute('data-page')) {
        // try to turn the page into an int
        var page = parseInt(element.getAttribute('data-page'));

        // make sure it is actually a valid int
        if (isNaN(page) || page < 1) {
          return;
        }

        // disable the 'load more' button
        element.classList.add('is-loading');

        // start loading the page
        loadPage(page);

        // stop the click from propagating
        e.preventDefault();

        // all done!
        return false;
      }
    });

    // do the initial page load
    loadPage();
  };
})();
