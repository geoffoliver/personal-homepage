(function() {
  Dropzone.autoDiscover = false;

  var $ = function(qs) {
    return document.querySelector(qs);
  };

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

  var updatePreview = function() {
    var titleText = title.value;
    var previewContent = body.value;

    if (titleText) {
      previewContent = '# ' + titleText + '\n\n' + previewContent;
    }

    body.style.height = 'auto';

    body.style.height = body.scrollHeight + 'px';

    preview.innerHTML = marked(previewContent);
  }

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

  document.getElementById('postForm').addEventListener('input', updatePreview);
  updatePreview();
})();
