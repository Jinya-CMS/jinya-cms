var Ajax;
(function (Ajax) {
    var ErrorDetails = /** @class */ (function () {
        function ErrorDetails() {
        }
        Object.defineProperty(ErrorDetails.prototype, "message", {
            get: function () {
                return this._message;
            },
            set: function (value) {
                this._message = value;
            },
            enumerable: true,
            configurable: true
        });
        Object.defineProperty(ErrorDetails.prototype, "success", {
            get: function () {
                return this._success;
            },
            set: function (value) {
                this._success = value;
            },
            enumerable: true,
            configurable: true
        });
        return ErrorDetails;
    }());
    Ajax.ErrorDetails = ErrorDetails;
    var Error = /** @class */ (function () {
        function Error() {
        }
        Object.defineProperty(Error.prototype, "statusCode", {
            get: function () {
                return this._statusCode;
            },
            set: function (value) {
                this._statusCode = value;
            },
            enumerable: true,
            configurable: true
        });
        Object.defineProperty(Error.prototype, "details", {
            get: function () {
                return this._details;
            },
            set: function (value) {
                this._details = value;
            },
            enumerable: true,
            configurable: true
        });
        Object.defineProperty(Error.prototype, "message", {
            get: function () {
                return this._message;
            },
            set: function (value) {
                this._message = value;
            },
            enumerable: true,
            configurable: true
        });
        return Error;
    }());
    Ajax.Error = Error;
    var Request = /** @class */ (function () {
        function Request(url) {
            var _this = this;
            this.post = function (data) {
                return _this.send('POST', data);
            };
            this.put = function (data) {
                return _this.send('PUT', data);
            };
            this.get = function () {
                return _this.send('GET');
            };
            this.delete = function () {
                return _this.send('DELETE');
            };
            this.send = function (method, data) {
                return new Promise(function (resolve, reject) {
                    BlockingLoader.show();
                    _this.xhr.onreadystatechange = function () {
                        if (_this.xhr.readyState === 4) {
                            BlockingLoader.hide();
                            if (_this.xhr.status === 200) {
                                resolve(JSON.parse(_this.xhr.responseText));
                            }
                            else {
                                var error = new Ajax.Error();
                                error.statusCode = _this.xhr.status;
                                error.message = _this.xhr.statusText;
                                error.details = JSON.parse(_this.xhr.responseText);
                                reject(error);
                            }
                        }
                    };
                    _this.xhr.open(method, _this.url, true);
                    if (!(data instanceof FormData)) {
                        _this.xhr.setRequestHeader('Content-Type', 'application/json');
                        data = JSON.stringify(data);
                    }
                    _this.xhr.send(data);
                });
            };
            this.xhr = new XMLHttpRequest();
            this.url = url;
        }
        return Request;
    }());
    Ajax.Request = Request;
})(Ajax || (Ajax = {}));
//# sourceMappingURL=data:application/json;base64,eyJ2ZXJzaW9uIjozLCJmaWxlIjoiYWpheC5qcyIsInNvdXJjZVJvb3QiOiIiLCJzb3VyY2VzIjpbImFqYXgudHMiXSwibmFtZXMiOltdLCJtYXBwaW5ncyI6IkFBQUEsSUFBVSxJQUFJLENBcUdiO0FBckdELFdBQVUsSUFBSTtJQUNWO1FBQUE7UUFtQkEsQ0FBQztRQWpCRyxzQkFBSSxpQ0FBTztpQkFBWDtnQkFDSSxNQUFNLENBQUMsSUFBSSxDQUFDLFFBQVEsQ0FBQztZQUN6QixDQUFDO2lCQUVELFVBQVksS0FBYTtnQkFDckIsSUFBSSxDQUFDLFFBQVEsR0FBRyxLQUFLLENBQUM7WUFDMUIsQ0FBQzs7O1dBSkE7UUFRRCxzQkFBSSxpQ0FBTztpQkFBWDtnQkFDSSxNQUFNLENBQUMsSUFBSSxDQUFDLFFBQVEsQ0FBQztZQUN6QixDQUFDO2lCQUVELFVBQVksS0FBYztnQkFDdEIsSUFBSSxDQUFDLFFBQVEsR0FBRyxLQUFLLENBQUM7WUFDMUIsQ0FBQzs7O1dBSkE7UUFLTCxtQkFBQztJQUFELENBQUMsQUFuQkQsSUFtQkM7SUFuQlksaUJBQVksZUFtQnhCLENBQUE7SUFFRDtRQUFBO1FBNkJBLENBQUM7UUEzQkcsc0JBQUksNkJBQVU7aUJBQWQ7Z0JBQ0ksTUFBTSxDQUFDLElBQUksQ0FBQyxXQUFXLENBQUM7WUFDNUIsQ0FBQztpQkFFRCxVQUFlLEtBQWE7Z0JBQ3hCLElBQUksQ0FBQyxXQUFXLEdBQUcsS0FBSyxDQUFDO1lBQzdCLENBQUM7OztXQUpBO1FBUUQsc0JBQUksMEJBQU87aUJBQVg7Z0JBQ0ksTUFBTSxDQUFDLElBQUksQ0FBQyxRQUFRLENBQUM7WUFDekIsQ0FBQztpQkFFRCxVQUFZLEtBQXdCO2dCQUNoQyxJQUFJLENBQUMsUUFBUSxHQUFHLEtBQUssQ0FBQztZQUMxQixDQUFDOzs7V0FKQTtRQVFELHNCQUFJLDBCQUFPO2lCQUFYO2dCQUNJLE1BQU0sQ0FBQyxJQUFJLENBQUMsUUFBUSxDQUFDO1lBQ3pCLENBQUM7aUJBRUQsVUFBWSxLQUFhO2dCQUNyQixJQUFJLENBQUMsUUFB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