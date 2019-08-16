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

  var content = document.querySelector('#content');

  // implement uploads from here...
  // https://github.com/quilljs/quill/issues/1089#issuecomment-510084530
  var quill = new Quill('#content', {
    modules: {
      toolbar: [
        [{ header: [1, 2, false] }],
        ['bold', 'italic', 'underline',],
        ['blockquote'],
        ['link', 'code-block']
      ]
    },
    formats: [
      'background',
      'bold',
      'color',
      'font',
      'code',
      'italic',
      'link',
      'size',
      'strike',
      'script',
      'underline',
      'blockquote',
      'header',
      'indent',
      'list',
      'align',
      'direction',
      'code-block',
      'formula'
    ],
    placeholder: content.attributes.getNamedItem("data-placeholder").value,
    theme: 'bubble'
  });

  quill.on('text-change', function(delta, old, src) {
    // console.log('d', delta);
    // console.log('o', old);
    // console.log('s', src);

    console.log('content', quill.getContents());
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

})();
