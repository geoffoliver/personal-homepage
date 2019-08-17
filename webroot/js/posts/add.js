(function() {
  Dropzone.autoDiscover = false;

  var attachmentTpl = document.querySelector('#post-attachment-template');
  var attachmentsContainer = document.querySelector('#add-post-attachments');
  var templateHtml = document.querySelector('#add-post-attachment-preview-template').innerHTML

  var error = function(file, message) {
    dropzone.removeFile(file);
    alert(message.message || "Error uploading file. Please try again.");
  };

  var success = function(file, result) {
    dropzone.removeFile(file);

    var newAttachment = document.importNode(attachmentTpl.content, true);

    newAttachment.querySelector('img[data-thumbnail]').src = '/media/' + result.data.media.square_thumbnail;
    newAttachment.querySelector('input[data-media-id]').value = result.data.media.id;

    attachmentsContainer.appendChild(newAttachment);
  };

  var thumbnail = function(_file, thumb) {
    thumbnailUrl = thumb;
  };

  var dropzone = new Dropzone('#add-post-attachment-button', {
    url: "/medias/upload.json",
    autoProcessQueue: true,
    previewTemplate: templateHtml,
    previewsContainer: '#add-post-attachments',
    headers: {
      "X-CSRF-Token": document.querySelector('[name="_csrfToken"]').value
    },
    init: function() {
      this.on("success", success);
      this.on("error", error);
      this.on("thumbnail", thumbnail);
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
      alert('edit');
      e.preventDefault();
      return;
    }
  });

  /*
  var content = document.querySelector('#content');

  var uploading = false;

  var uploadAndReplaceImage = function (image) {
    uploading = true;

    var dataURI = image.src;
    var imageBase64 = atob(dataURI.split(',')[1]);
    var mimeType = dataURI.split(',')[0].split(':')[1].split(';')[0];

    var ia = new Uint8Array(imageBase64.length);

    for (var i = 0; i < imageBase64.length; i++) {
        ia[i] = imageBase64.charCodeAt(i);
    }

    var fileBlob = new Blob([ia], {type: mimeType});
    var uploadData = new FormData();
    var filename = 'dragged-file-' + new Date().getTime();

    switch (mimeType) {
      case 'image/jpeg':
        filename += '.jpg';
        break;
      case 'image/png':
        filename += '.png';
        break;
      case 'image/gif':
        filename += '.gif';
        break;
      case 'video/mp4':
        filename += '.mp4';
        break;
      case 'video/mov':
        filename += '.mov';
        break;
    }

    uploadData.append('file', fileBlob, filename);

    nanoajax.ajax({
      url: '/medias/upload.json',
      method: 'POST',
      responseType: 'json',
      body: uploadData,
      headers: {
        "X-CSRF-Token": document.querySelector('[name="_csrfToken"]').value
      },
      onprogress: function(a) {
        console.log('a', a);
      }
    }, function(code, response) {
      uploading = false;

      if (code !== 200) {
        return;
      }

      if (!response.success) {
        return;
      }

      image.src = '/media/' + response.data.media.thumbnail;
      image.setAttribute('data-original', '/media/' + response.data.media.local_filename);

      var newAttachment = document.importNode(attachmentTpl.content, true);

      newAttachment.querySelector('img[data-thumbnail]').src = '/media/' + response.data.media.square_thumbnail;
      newAttachment.querySelector('input[data-media-id]').value = response.data.media.id;
      attachmentsContainer.appendChild(newAttachment);
    });
  }

  var quill = new Quill('#content', {
    modules: {
      toolbar: [
        [{ header: [1, 2, false] }],
        ['bold', 'italic', 'underline', 'blockquote'],
        ['image', 'video'],
        ['link', 'code-block']
      ]
    },
    placeholder: content.attributes.getNamedItem("data-placeholder").value,
    theme: 'bubble'
  });

  quill.on('text-change', function(delta, old, src) {
    console.log('content', quill.getContents());

    if (uploading) {
      return;
    }

    var images = quill.root.querySelectorAll('img');
    var img, src;
    for (let i = 0; i < images.length; i++) {
      img = images[i];
      src = img.src;
      if (img.src && img.src[0] === 'd') {
        uploading = true;
        uploadAndReplaceImage(img);
      }
    }
  });

  var postForm = document.getElementById('postForm');

  postForm.addEventListener('submit', function(e) {
    postForm.querySelector('button[type="submit"]').classList.add('is-loading');

    var contentInput = document.createElement('input');

    contentInput.name = "delta";
    contentInput.style.display = "none";
    contentInput.value = JSON.stringify(quill.getContents());//quill.root.innerHTML;

    postForm.querySelector('div').appendChild(contentInput);
  });
  */
})();
