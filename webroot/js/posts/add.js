(function() {
  Dropzone.autoDiscover = false;

  var attachmentTpl = document.querySelector('#post-attachment-template');
  var attachmentsContainer = document.querySelector('#add-post-attachments');
  var templateHtml = document.querySelector('#add-post-attachment-preview-template').innerHTML
  var thumbnailUrl;

  var error = function(file, message) {
    dropzone.removeFile(file);
    alert(message.message || "Error uploading file. Please try again.");
  };

  var success = function(file, result) {
    console.log('r', result);

    dropzone.removeFile(file);

    var newAttachment = document.importNode(attachmentTpl.content, true);

    var img = newAttachment.querySelector('img[data-thumbnail]');

    if (thumbnailUrl) {
      img.src = thumbnailUrl;
    } else {
      img.src = file.previewElement.querySelector('img[data-dz-thumbnail]').src;
    }

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
})();
