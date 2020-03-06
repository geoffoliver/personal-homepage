(function() {
  Dropzone.autoDiscover = false;

  var $ = function(qs) {
    return document.querySelector(qs);
  };

  var postForm = $('#postForm');
  var attachmentTpl = $('#post-attachment-template');
  var attachmentsContainer = $('#add-post-attachments');
  var templateHtml = $('#add-post-attachment-preview-template').innerHTML
  var title = $('#name');
  var body = $('#content');
  var preview = $('#postPreview');

  var error = function(file, message) {
    dropzone.removeFile(file);
    alert(message.message || "Error uploading file. Please try again.");
  };

  var success = function(file, result) {
    dropzone.removeFile(file);

    var newAttachment = document.importNode(attachmentTpl.content, true);

    newAttachment.querySelector('img[data-thumbnail]').src = '/medias/download/' + result.data.media.id +'/square_thumbnail' ;
    newAttachment.querySelector('input[data-media-id]').value = result.data.media.id;

    attachmentsContainer.appendChild(newAttachment);
  };

  var thumbnail = function(_file, thumb) {
    thumbnailUrl = thumb;
  };

  // thank you https://stackoverflow.com/a/43041683 for giving me this magic power
  var drop = function(event) {
    var imageUrl = event.dataTransfer.getData('URL');

    if (!imageUrl) {
      // no url, this is just a regular drop operation, so nothing to do
      return;
    }

    var fileName = imageUrl.split('/').pop().split('?').shift();
    var extension = fileName.split('.').pop();

    // make up a new filename
    fileName = 'drag-drop-upload-' + new Date().getTime().toString() + '.' + extension;

    // set the effectAllowed for the drag item
    event.dataTransfer.effectAllowed = 'copy';

    function getDataUri(url, callback) {
      var image = new Image();
      // var image = document.createElement('img');

      image.onload = function() {
        var canvas = document.createElement('canvas');
        canvas.width = this.naturalWidth; // or 'width' if you want a special/scaled size
        canvas.height = this.naturalHeight; // or 'height' if you want a special/scaled size

        canvas.getContext('2d').drawImage(this, 0, 0);

        // Get raw image data
        // callback(canvas.toDataURL('image/png').replace(/^data:image\/(png|jpg);base64,/, ''));

        // ... or get as Data URI
        callback(canvas.toDataURL('image/jpeg'));
      };

      image.setAttribute('crossOrigin', 'anonymous');
      image.src = url;
    }

    function dataURItoBlob(dataURI) {
      var byteString,
          mimestring

      if (dataURI.split(',')[0].indexOf('base64') !== -1) {
        byteString = atob(dataURI.split(',')[1])
      } else {
        byteString = decodeURI(dataURI.split(',')[1])
      }

      mimestring = dataURI.split(',')[0].split(':')[1].split(';')[0]

      var content = new Array();
      for (var i = 0; i < byteString.length; i++) {
        content[i] = byteString.charCodeAt(i)
      }

      return new Blob([new Uint8Array(content)], {
        type: mimestring
      });
    }

    getDataUri(imageUrl, function(dataUri) {
      var blob = dataURItoBlob(dataUri);
      blob.name = fileName;
      dropzone.addFile(blob);
    });
  };

  var updatePostPreview = function() {
    var previewContent = marked(body.value);
    var titleText = title.value.replace(/&/g, '&amp;')
      .replace(/</g, '&lt;')
      .replace(/>/g, '&gt;')
      .replace(/"/g, '&quot;');

    if (titleText) {
      previewContent = '<h1>' + titleText + '</h1>\n\n' + previewContent;
    }

    preview.innerHTML = previewContent;
  };

  var resizeTextArea = function() {
    body.style.height = 'auto';
    body.style.height = body.scrollHeight + 'px';
  };

  resizeTextArea();

  var dropzone = new Dropzone('#add-post-attachment-button', {
    url: "/medias/upload.json",
    autoProcessQueue: true,
    previewTemplate: templateHtml,
    previewsContainer: '#add-post-attachments',
    headers: {
      "X-CSRF-Token": $('input[name="_csrfToken"]').value
    },
    init: function() {
      this.on("success", success);
      this.on("error", error);
      this.on("thumbnail", thumbnail);
      this.on("drop", drop);
    },
    params: function(files, xhr) {
      return {
        allow_comments: $('#allow-comments').checked ? '1' : '0',
        public: $('#public').checked ? '1' : '0'
      }
    }
  });

  document.addEventListener('click', function(e) {
    if (e.target.classList.contains('delete-post-attachment')) {
      if (confirm('Are you sure you want to remove this file from the post?')) {
        var remove = e.target.closest('.add-post-attachment-item');
        remove.parentNode.removeChild(remove);
      }
      e.preventDefault();
      return;
    }

    if (e.target.classList.contains('edit-post-attachment')) {
      var element = e.target.closest('.add-post-attachment-item');
      var inputId = element.querySelectorAll('input[data-media-id]').item(0);
      var mediaId = inputId.value;

      window.open("/medias/edit/" + mediaId);

      e.preventDefault();
      return;
    }
  });

  body.addEventListener('input', resizeTextArea);

  $('#showEditor').addEventListener('click', function() {
    postForm.classList.remove('preview');
    $('#showEditor').classList.add('active');
    $('#showPreview').classList.remove('active');
  });

  $('#showPreview').addEventListener('click', function() {
    updatePostPreview();
    postForm.classList.add('preview');
    $('#showPreview').classList.add('active');
    $('#showEditor').classList.remove('active');
  });
})();
