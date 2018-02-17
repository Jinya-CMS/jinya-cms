'use strict';

var _createClass = function () { function defineProperties(target, props) { for (var i = 0; i < props.length; i++) { var descriptor = props[i]; descriptor.enumerable = descriptor.enumerable || false; descriptor.configurable = true; if ("value" in descriptor) descriptor.writable = true; Object.defineProperty(target, descriptor.key, descriptor); } } return function (Constructor, protoProps, staticProps) { if (protoProps) defineProperties(Constructor.prototype, protoProps); if (staticProps) defineProperties(Constructor, staticProps); return Constructor; }; }();

function _classCallCheck(instance, Constructor) { if (!(instance instanceof Constructor)) { throw new TypeError("Cannot call a class as a function"); } }

/**
 * Utility class for DOM manipulation
 */
var HtmlUtils = function () {
  function HtmlUtils() {
    _classCallCheck(this, HtmlUtils);
  }

  _createClass(HtmlUtils, null, [{
    key: 'htmlToElement',

    /**
     * Converts the given html string into a node
     * @param {string} html
     * @returns {Node}
     */
    value: function htmlToElement(html) {
      var range = document.createRange();
      range.selectNode(document.querySelector('body'));
      return range.createContextualFragment(html).firstElementChild;
    }
  }]);

  return HtmlUtils;
}();
"use strict";

var _createClass = function () { function defineProperties(target, props) { for (var i = 0; i < props.length; i++) { var descriptor = props[i]; descriptor.enumerable = descriptor.enumerable || false; descriptor.configurable = true; if ("value" in descriptor) descriptor.writable = true; Object.defineProperty(target, descriptor.key, descriptor); } } return function (Constructor, protoProps, staticProps) { if (protoProps) defineProperties(Constructor.prototype, protoProps); if (staticProps) defineProperties(Constructor, staticProps); return Constructor; }; }();

function _classCallCheck(instance, Constructor) { if (!(instance instanceof Constructor)) { throw new TypeError("Cannot call a class as a function"); } }

var i18n = function () {
    function i18n() {
        _classCallCheck(this, i18n);
    }

    _createClass(i18n, null, [{
        key: "getText",

        /**
         * Gets the text for the specified identifier
         * @param identifier string
         */
        value: function getText(identifier) {
            return texts[identifier];
        }
    }]);

    return i18n;
}();
'use strict';

var _typeof = typeof Symbol === "function" && typeof Symbol.iterator === "symbol" ? function (obj) { return typeof obj; } : function (obj) { return obj && typeof Symbol === "function" && obj.constructor === Symbol && obj !== Symbol.prototype ? "symbol" : typeof obj; };

var _createClass = function () { function defineProperties(target, props) { for (var i = 0; i < props.length; i++) { var descriptor = props[i]; descriptor.enumerable = descriptor.enumerable || false; descriptor.configurable = true; if ("value" in descriptor) descriptor.writable = true; Object.defineProperty(target, descriptor.key, descriptor); } } return function (Constructor, protoProps, staticProps) { if (protoProps) defineProperties(Constructor.prototype, protoProps); if (staticProps) defineProperties(Constructor, staticProps); return Constructor; }; }();

function _classCallCheck(instance, Constructor) { if (!(instance instanceof Constructor)) { throw new TypeError("Cannot call a class as a function"); } }

var JSONTools = function () {
    function JSONTools() {
        _classCallCheck(this, JSONTools);
    }

    _createClass(JSONTools, null, [{
        key: 'jsonStringifyWithoutCycle',

        /**
         * Code from https://stackoverflow.com/a/24075430
         */
        value: function jsonStringifyWithoutCycle(obj, replacer, space) {
            var cache = [];
            var json = JSON.stringify(obj, function (key, value) {
                if ((typeof value === 'undefined' ? 'undefined' : _typeof(value)) === 'object' && value !== null) {
                    if (cache.indexOf(value) !== -1) {
                        // circular reference found, discard key
                        return;
                    }
                    // store value in our collection
                    cache.push(value);
                }
                return replacer ? replacer(key, value) : value;
            }, space);
            cache = null;
            return json;
        }
    }]);

    return JSONTools;
}();
"use strict";
//# sourceMappingURL=data:application/json;charset=utf8;base64,eyJ2ZXJzaW9uIjozLCJzb3VyY2VzIjpbImh0bWxVdGlscy5qcyIsImkxOG4uanMiLCJKU09OVG9vbHMuanMiLCJkZXNpZ25lci5qcyJdLCJuYW1lcyI6WyJIdG1sVXRpbHMiLCJodG1sIiwicmFuZ2UiLCJkb2N1bWVudCIsImNyZWF0ZVJhbmdlIiwic2VsZWN0Tm9kZSIsInF1ZXJ5U2VsZWN0b3IiLCJjcmVhdGVDb250ZXh0dWFsRnJhZ21lbnQiLCJmaXJzdEVsZW1lbnRDaGlsZCIsImkxOG4iLCJpZGVudGlmaWVyIiwidGV4dHMiLCJKU09OVG9vbHMiLCJvYmoiLCJyZXBsYWNlciIsInNwYWNlIiwiY2FjaGUiLCJqc29uIiwiSlNPTiIsInN0cmluZ2lmeSIsImtleSIsInZhbHVlIiwiaW5kZXhPZiIsInB1c2giXSwibWFwcGluZ3MiOiI7Ozs7OztBQUFBOzs7SUFHTUEsUzs7Ozs7Ozs7QUFDRjs7Ozs7a0NBS3FCQyxJLEVBQU07QUFDdkIsVUFBTUMsUUFBUUMsU0FBU0MsV0FBVCxFQUFkO0FBQ0FGLFlBQU1HLFVBQU4sQ0FBaUJGLFNBQVNHLGFBQVQsQ0FBdUIsTUFBdkIsQ0FBakI7QUFDQSxhQUFPSixNQUFNSyx3QkFBTixDQUErQk4sSUFBL0IsRUFBcUNPLGlCQUE1QztBQUNIOzs7Ozs7Ozs7OztJQ2JDQyxJOzs7Ozs7OztBQUNGOzs7O2dDQUllQyxVLEVBQVk7QUFDdkIsbUJBQU9DLE1BQU1ELFVBQU4sQ0FBUDtBQUNIOzs7Ozs7Ozs7Ozs7O0lDUENFLFM7Ozs7Ozs7O0FBQ0Y7OztrREFHaUNDLEcsRUFBS0MsUSxFQUFVQyxLLEVBQU87QUFDbkQsZ0JBQUlDLFFBQVEsRUFBWjtBQUNBLGdCQUFNQyxPQUFPQyxLQUFLQyxTQUFMLENBQWVOLEdBQWYsRUFBb0IsVUFBVU8sR0FBVixFQUFlQyxLQUFmLEVBQXNCO0FBQ25ELG9CQUFJLFFBQU9BLEtBQVAseUNBQU9BLEtBQVAsT0FBaUIsUUFBakIsSUFBNkJBLFVBQVUsSUFBM0MsRUFBaUQ7QUFDN0Msd0JBQUlMLE1BQU1NLE9BQU4sQ0FBY0QsS0FBZCxNQUF5QixDQUFDLENBQTlCLEVBQWlDO0FBQzdCO0FBQ0E7QUFDSDtBQUNEO0FBQ0FMLDBCQUFNTyxJQUFOLENBQVdGLEtBQVg7QUFDSDtBQUNELHVCQUFPUCxXQUFXQSxTQUFTTSxHQUFULEVBQWNDLEtBQWQsQ0FBWCxHQUFrQ0EsS0FBekM7QUFDSCxhQVZZLEVBVVZOLEtBVlUsQ0FBYjtBQVdBQyxvQkFBUSxJQUFSO0FBQ0EsbUJBQU9DLElBQVA7QUFDSDs7Ozs7QUNuQkwiLCJmaWxlIjoiZGVzaWduZXIuanMiLCJzb3VyY2VzQ29udGVudCI6WyIvKipcclxuICogVXRpbGl0eSBjbGFzcyBmb3IgRE9NIG1hbmlwdWxhdGlvblxyXG4gKi9cclxuY2xhc3MgSHRtbFV0aWxzIHtcclxuICAgIC8qKlxyXG4gICAgICogQ29udmVydHMgdGhlIGdpdmVuIGh0bWwgc3RyaW5nIGludG8gYSBub2RlXHJcbiAgICAgKiBAcGFyYW0ge3N0cmluZ30gaHRtbFxyXG4gICAgICogQHJldHVybnMge05vZGV9XHJcbiAgICAgKi9cclxuICAgIHN0YXRpYyBodG1sVG9FbGVtZW50KGh0bWwpIHtcclxuICAgICAgICBjb25zdCByYW5nZSA9IGRvY3VtZW50LmNyZWF0ZVJhbmdlKCk7XHJcbiAgICAgICAgcmFuZ2Uuc2VsZWN0Tm9kZShkb2N1bWVudC5xdWVyeVNlbGVjdG9yKCdib2R5JykpO1xyXG4gICAgICAgIHJldHVybiByYW5nZS5jcmVhdGVDb250ZXh0dWFsRnJhZ21lbnQoaHRtbCkuZmlyc3RFbGVtZW50Q2hpbGQ7XHJcbiAgICB9O1xyXG59IiwiY2xhc3MgaTE4biB7XHJcbiAgICAvKipcclxuICAgICAqIEdldHMgdGhlIHRleHQgZm9yIHRoZSBzcGVjaWZpZWQgaWRlbnRpZmllclxyXG4gICAgICogQHBhcmFtIGlkZW50aWZpZXIgc3RyaW5nXHJcbiAgICAgKi9cclxuICAgIHN0YXRpYyBnZXRUZXh0KGlkZW50aWZpZXIpIHtcclxuICAgICAgICByZXR1cm4gdGV4dHNbaWRlbnRpZmllcl07XHJcbiAgICB9O1xyXG59IiwiY2xhc3MgSlNPTlRvb2xzIHtcclxuICAgIC8qKlxyXG4gICAgICogQ29kZSBmcm9tIGh0dHBzOi8vc3RhY2tvdmVyZmxvdy5jb20vYS8yNDA3NTQzMFxyXG4gICAgICovXHJcbiAgICBzdGF0aWMganNvblN0cmluZ2lmeVdpdGhvdXRDeWNsZShvYmosIHJlcGxhY2VyLCBzcGFjZSkge1xyXG4gICAgICAgIGxldCBjYWNoZSA9IFtdO1xyXG4gICAgICAgIGNvbnN0IGpzb24gPSBKU09OLnN0cmluZ2lmeShvYmosIGZ1bmN0aW9uIChrZXksIHZhbHVlKSB7XHJcbiAgICAgICAgICAgIGlmICh0eXBlb2YgdmFsdWUgPT09ICdvYmplY3QnICYmIHZhbHVlICE9PSBudWxsKSB7XHJcbiAgICAgICAgICAgICAgICBpZiAoY2FjaGUuaW5kZXhPZih2YWx1ZSkgIT09IC0xKSB7XHJcbiAgICAgICAgICAgICAgICAgICAgLy8gY2lyY3VsYXIgcmVmZXJlbmNlIGZvdW5kLCBkaXNjYXJkIGtleVxyXG4gICAgICAgICAgICAgICAgICAgIHJldHVybjtcclxuICAgICAgICAgICAgICAgIH1cclxuICAgICAgICAgICAgICAgIC8vIHN0b3JlIHZhbHVlIGluIG91ciBjb2xsZWN0aW9uXHJcbiAgICAgICAgICAgICAgICBjYWNoZS5wdXNoKHZhbHVlKTtcclxuICAgICAgICAgICAgfVxyXG4gICAgICAgICAgICByZXR1cm4gcmVwbGFjZXIgPyByZXBsYWNlcihrZXksIHZhbHVlKSA6IHZhbHVlO1xyXG4gICAgICAgIH0sIHNwYWNlKTtcclxuICAgICAgICBjYWNoZSA9IG51bGw7XHJcbiAgICAgICAgcmV0dXJuIGpzb247XHJcbiAgICB9O1xyXG59IixudWxsXX0=
