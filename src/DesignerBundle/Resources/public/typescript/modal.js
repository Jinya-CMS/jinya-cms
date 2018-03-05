var Modal = /** @class */ (function () {
    function Modal(selector, cache) {
        if (cache === void 0) { cache = true; }
        var _this = this;
        this.show = function () {
            _this.trigger('opening');
            _this.showOverlay();
            setTimeout(function () {
                classie.add(_this.modalElement, 'md-show');
            }, 300);
            Modal.current = _this;
            _this.trigger('opened');
        };
        this.hide = function (removeOverlay, noEvents) {
            if (removeOverlay === void 0) { removeOverlay = true; }
            if (noEvents === void 0) { noEvents = false; }
            if (!noEvents) {
                _this.trigger('closing');
            }
            classie.remove(_this.modalElement, 'md-show');
            if (removeOverlay) {
                classie.remove(Modal.body, 'md-show');
                setTimeout(function () {
                    if (Modal.body.querySelector('.md-overlay')) {
                        try {
                            Modal.body.removeChild(Modal.overlay);
                        }
                        catch (_a) {
                        }
                    }
                }, 300);
            }
            if (!noEvents) {
                _this.trigger('closed');
            }
        };
        this.on = function (event, callback) {
            _this.subscriber[event] = _this.subscriber[event] || [];
            _this.subscriber[event].push(callback);
        };
        this.trigger = function (event, data) {
            var callbacks = _this.subscriber[event] || [];
            for (var i = 0; i < callbacks.length; i++) {
                callbacks[i]({
                    event: event,
                    data: data,
                    callOrder: i
                });
            }
        };
        this.showOverlay = function () {
            Modal.body.appendChild(Modal.overlay);
            setTimeout(function () {
                classie.add(Modal.body, 'md-show');
            }, 300);
        };
        this.subscriber = {};
        if (typeof (selector) === 'string') {
            this.modalElement = document.querySelector(selector);
        }
        else {
            this.modalElement = selector;
        }
        classie.add(Modal.overlay, 'md-overlay');
        Modal.overlay.addEventListener('click', function () {
            if (Modal.current.canHide) {
                Modal.current.hide();
            }
        });
        var closeButtons = this.modalElement.querySelectorAll('[data-action=close]');
        for (var i = 0; i < closeButtons.length; i++) {
            closeButtons[i].addEventListener('click', function () {
                _this.hide();
            });
        }
        if (cache) {
            Modal.modals[this.modalElement.id] = this;
        }
    }
    Object.defineProperty(Modal.prototype, "canHide", {
        get: function () {
            return this._canHide;
        },
        set: function (value) {
            this._canHide = value;
        },
        enumerable: true,
        configurable: true
    });
    Modal.get = function (element, cache) {
        if (cache === void 0) { cache = true; }
        if (cache) {
            var modal = Modal.modals[element.id];
            if (modal) {
                return modal;
            }
        }
        return new Modal(element, cache);
    };
    Modal.alert = function (title, message, okButton) {
        if (okButton === void 0) { okButton = texts['generic.close']; }
        return new Promise(function (resolve, reject) {
            var id = Math.random();
            var template = "\n<div class=\"md-modal\" id=\"alert-modal-" + id + "\">\n    <div class=\"md-content\">\n        <span class=\"md-title\">" + title + "</span>\n        <div>\n            <p>" + message + "</p>\n        </div>    \n        <div class=\"md-footer\">\n            <button data-action=\"close\"\n                    class=\"button button-round-s button-border-medium primary\">" + okButton + "</button>\n        </div>\n    </div>\n</div>\n            ";
            var openModals = document.querySelectorAll('.md-show.md-modal');
            classiex.remove(openModals, 'md-show');
            classiex.add(openModals, 'md-hidden-for-alert');
            var modalElement = Util.htmlToElement(template);
            Modal.body.appendChild(modalElement);
            var modal = Modal.get(modalElement);
            modal.canHide = false;
            modal.on('closed', function () {
                var hiddenModals = document.querySelectorAll('.md-hidden-for-alert');
                classiex.add(openModals, 'md-show');
                classiex.remove(openModals, 'md-hidden-for-alert');
                Modal.body.removeChild(modalElement);
                resolve({
                    'clicked': true
                });
                modal.showOverlay();
            });
            modal.show();
        });
    };
    Modal.confirm = function (title, message, positiveButton, negativeButton, ignoreButton) {
        if (positiveButton === void 0) { positiveButton = Util.getText('generic.yes'); }
        if (negativeButton === void 0) { negativeButton = Util.getText('generic.no'); }
        if (ignoreButton === void 0) { ignoreButton = Util.getText('generic.cancel'); }
        return new Promise(function (resolve, reject) {
            var id = Math.random();
            var template = "\n<div class=\"md-modal\" id=\"alert-modal-" + id + "\">\n    <div class=\"md-content\">\n        <span class=\"md-title\">" + title + "</span>\n        <div>\n            <p>" + message + "</p>\n        </div>    \n        <div class=\"md-footer\">\n            <button data-action=\"positive\"\n                    class=\"button button-round-s button-border-medium primary\">" + positiveButton + "</button>\n            <button data-action=\"negative\"\n                    class=\"button button-round-s button-border-medium secondary\">" + negativeButton + "</button>\n            <button data-action=\"ignore\"\n                    class=\"button button-round-s button-border-medium secondary inverse button-right\">" + ignoreButton + "</button>\n        </div>\n    </div>\n</div>\n            ";
            var openModals = document.querySelectorAll('.md-show.md-modal');
            classiex.remove(openModals, 'md-show');
            classiex.add(openModals, 'md-hidden-for-alert');
            var modalElement = Util.htmlToElement(template);
            Modal.body.appendChild(modalElement);
            var modal = Modal.get(modalElement);
            modal.canHide = false;
            modal.on('closed', function () {
                var hiddenModals = document.querySelectorAll('.md-hidden-for-alert');
                classiex.add(openModals, 'md-show');
                classiex.remove(openModals, 'md-hidden-for-alert');
                Modal.body.removeChild(modalElement);
            });
            modal.on('opening', function () {
                var positiveButton = modalElement.querySelector('[data-action=positive]');
                positiveButton.addEventListener('click', function () {
                    resolve(true);
                    modal.hide(openModals.length === 0);
                });
                var negativeButton = modalElement.querySelector('[data-action=negative]');
                negativeButton.addEventListener('click', function () {
                    resolve(false);
                    modal.hide(openModals.length === 0);
                });
                var ignoreButton = modalElement.querySelector('[data-action=ignore]');
                ignoreButton.addEventListener('click', function () {
                    reject();
                    modal.hide(openModals.length === 0);
                });
            });
            modal.show();
        });
    };
    Modal.activate = (function () {
        var triggers = document.querySelectorAll('[data-toggle=modal]');
        var _loop_1 = function (i) {
            var trigger = triggers.item(i);
            var target = trigger.getAttribute('data-target');
            var modal = new Modal(target);
            trigger.addEventListener(trigger.getAttribute('data-trigger') || 'click', function () {
                modal.show();
            });
        };
        for (var i = 0; i < triggers.length; i++) {
            _loop_1(i);
        }
    })();
    Modal.modals = {};
    Modal.overlay = document.createElement('div');
    Modal.body = document.querySelector('body');
    return Modal;
}());
//# sourceMappingURL=data:application/json;base64,eyJ2ZXJzaW9uIjozLCJmaWxlIjoibW9kYWwuanMiLCJzb3VyY2VSb290IjoiIiwic291cmNlcyI6WyJtb2RhbC50cyJdLCJuYW1lcyI6W10sIm1hcHBpbmdzIjoiQUFBQTtJQStLSSxlQUFZLFFBQTBCLEVBQUUsS0FBcUI7UUFBckIsc0JBQUEsRUFBQSxZQUFxQjtRQUE3RCxpQkF3QkM7UUE3RUQsU0FBSSxHQUFHO1lBQ0gsS0FBSSxDQUFDLE9BQU8sQ0FBQyxTQUFTLENBQUMsQ0FBQztZQUN4QixLQUFJLENBQUMsV0FBVyxFQUFFLENBQUM7WUFDbkIsVUFBVSxDQUFDO2dCQUNQLE9BQU8sQ0FBQyxHQUFHLENBQUMsS0FBSSxDQUFDLFlBQVksRUFBRSxTQUFTLENBQUMsQ0FBQztZQUM5QyxDQUFDLEVBQUUsR0FBRyxDQUFDLENBQUM7WUFFUixLQUFLLENBQUMsT0FBTyxHQUFHLEtBQUksQ0FBQztZQUNyQixLQUFJLENBQUMsT0FBTyxDQUFDLFFBQVEsQ0FBQyxDQUFDO1FBQzNCLENBQUMsQ0FBQztRQUNGLFNBQUksR0FBRyxVQUFDLGFBQW9CLEVBQUUsUUFBZ0I7WUFBdEMsOEJBQUEsRUFBQSxvQkFBb0I7WUFBRSx5QkFBQSxFQUFBLGdCQUFnQjtZQUMxQyxFQUFFLENBQUMsQ0FBQyxDQUFDLFFBQVEsQ0FBQyxDQUFDLENBQUM7Z0JBQ1osS0FBSSxDQUFDLE9BQU8sQ0FBQyxTQUFTLENBQUMsQ0FBQztZQUM1QixDQUFDO1lBQ0QsT0FBTyxDQUFDLE1BQU0sQ0FBQyxLQUFJLENBQUMsWUFBWSxFQUFFLFNBQVMsQ0FBQyxDQUFDO1lBQzdDLEVBQUUsQ0FBQyxDQUFDLGFBQWEsQ0FBQyxDQUFDLENBQUM7Z0JBQ2hCLE9BQU8sQ0FBQyxNQUFNLENBQUMsS0FBSyxDQUFDLElBQUksRUFBRSxTQUFTLENBQUMsQ0FBQztnQkFDdEMsVUFBVSxDQUFDO29CQUNQLEVBQUUsQ0FBQyxDQUFDLEtBQUssQ0FBQyxJQUFJLENBQUMsYUFBYSxDQUFDLGFBQWEsQ0FBQyxDQUFDLENBQUMsQ0FBQzt3QkFDMUMsSUFBSSxD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