(function() {
  Dropzone.autoDiscover = false;

  var templateHtml = document.querySelector('#add-post-attachment-preview-template').innerHTML
  var previewTemplate = Dropzone.createElement(templateHtml);

  var addedFile = function(file) {
    console.log('added', file);
    console.log('pt', previewTemplate);
    //file.previewElement = templa;
  };

  var complete = function(file) {
    console.log("complete", file);
    // dropzone.removeFile(file);
  };

  var thumbnail = function(file, dataUrl) {
    console.log('pe', file.previewElement);
  };

  var sending = function(file, xhr, formData) {
    xhr.setRequestHeader('X-CSRF-Token', document.querySelector('[name="_csrfToken"]').value);
  };

  var dropzone = new Dropzone('#add-post-attachment-button', {
    url: "/medias/upload",
    autoProcessQueue: true,
    previewTemplate: templateHtml,
    previewsContainer: '#add-post-attachments',
    init: function() {
      this.on("addedfile", addedFile);
      this.on("complete", complete);
      this.on("thumbnail", thumbnail);
      this.on("sending", sending);
    }
  });
})();
