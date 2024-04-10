define('ace/theme/jinya', ['require', 'exports', 'module'], function(require, exports, module) {
  exports.cssClass = 'ace-jinya';
});
(function() {
  window.require(['ace/theme/jinya'], function(m) {
    if (typeof module == 'object' && typeof exports == 'object' && module) {
      module.exports = m;
    }
  });
})();

