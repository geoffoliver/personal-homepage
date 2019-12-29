onmessage = function(message) {

  var xhr = new XMLHttpRequest();

  var onload = function() {
    self.postMessage({
      id: message.data.id/*,
      src: xhr.status === 200 ? URL.createObjectURL(xhr.response) : message.data.url
      */
    });
  }

  xhr.open('GET', message.data.url, true);

  xhr.responseType = 'blob';

  xhr.onload = xhr.onerror = onload;

  xhr.send();
};
