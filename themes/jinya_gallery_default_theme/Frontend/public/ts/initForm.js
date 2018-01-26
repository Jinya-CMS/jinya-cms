document.addEventListener('DOMContentLoaded', function () {
    // trim polyfill : https://developer.mozilla.org/en-US/docs/Web/JavaScript/Reference/Global_Objects/String/Trim
    if (!String.prototype.trim) {
        (function () {
            // Make sure we trim BOM and NBSP
            var rtrim = /^[\s\uFEFF\xA0]+|[\s\uFEFF\xA0]+$/g;
            String.prototype.trim = function () {
                return this.replace(rtrim, '');
            };
        })();
    }

    [].slice.call(document.querySelectorAll('input.field')).forEach(function (inputEl) {
        // in case the input is already filled..
        if (inputEl.value.trim() !== '') {
            classie.add(inputEl.parentNode, 'filled');
        }

        // events:
        inputEl.addEventListener('focus', onInputFocus);
        inputEl.addEventListener('blur', onInputBlur);
    });
    [].slice.call(document.querySelectorAll('textarea')).forEach(function (inputEl) {
        // in case the input is already filled..
        if (inputEl.value.trim() !== '') {
            classie.add(inputEl.parentNode, 'filled');
        }

        // events:
        inputEl.addEventListener('focus', onInputFocus);
        inputEl.addEventListener('blur', onInputBlur);
    });
    [].slice.call(document.querySelectorAll('select.cs-select')).forEach(function (el) {
        new SelectFx(el);
    });

    function onInputFocus(ev) {
        classie.add(ev.target.parentNode, 'filled');
    }

    function onInputBlur(ev) {
        if (ev.target.value.trim() === '') {
            classie.remove(ev.target.parentNode, 'filled');
        }
    }
});