onmessage = function(message) {
  importScripts('//cdnjs.cloudflare.com/ajax/libs/highlight.js/9.15.9/highlight.min.js');
  var result = self.hljs.highlightAuto(message.data.value);
  this.postMessage({id: message.data.id, value: result.value});
};
