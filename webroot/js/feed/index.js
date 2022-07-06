(function() {
  const watching = {};

  const getFeedItems = () => {
    return document.querySelectorAll('*[data-unread="true"]');
  };

  const markAsRead = (item) => {
    item.classList.add('is-read');
    lazyObserver.unobserve(item);
    const url = `/feed-item/read/${item.dataset.feedItemId}.json`;
    nanoajax.ajax({
      url: url,
      method: 'POST',
      headers: {
        'X-CSRF-Token': window.csrfToken,
      }
    }, function (/* status, response */) {
      // we don't really care what happens here... yet
    });
  };

  const checkScrolledOut = () => {
    const ids = Object.keys(watching);
    ids.forEach((id) => {
      const item = watching[id];
      const rect = item.getBoundingClientRect();
      const bottom = rect.bottom;

      if (bottom > 0) {
        return;
      }

      markAsRead(watching[id]);

      delete watching[id];
    });
  };

  const observeItem = (items) => {
    items.forEach(function(item) {
      if (item.isIntersecting && item.intersectionRatio === 1) {
        markAsRead(item.target);
      } else {
        watching[item.target.dataset.feedItemId] = item.target;
      }
    });
  };

  const initFeedItems = () => {
    const feedItems = getFeedItems();
    feedItems.forEach(function(item, n) {
      lazyObserver.observe(item);
      delete item.dataset.unread;
    });
  };

  const lazyObserver = new IntersectionObserver(observeItem, {
    threshold: [0, 1.0],
  });

  document.addEventListener('DOMContentLoaded', function() {
    var observer = new MutationObserver(function(mutations) {
      for (var mut of mutations) {
        if (mut.addedNodes.length > 0) {
          initFeedItems();
          return;
        }
      }
    });

    observer.observe(document.body, {childList: true, subtree: true});
  });

  window.addEventListener('scroll', checkScrolledOut);
})();
