namespace Ajax {
    export class ErrorDetails {
        private _message: string;
        get message(): string {
            return this._message;
        }

        set message(value: string) {
            this._message = value;
        }

        private _success: boolean;

        get success(): boolean {
            return this._success;
        }

        set success(value: boolean) {
            this._success = value;
        }
    }

    export class Error {
        private _details: Ajax.ErrorDetails;

        get details(): Ajax.ErrorDetails {
            return this._details;
        }

        set details(value: Ajax.ErrorDetails) {
            this._details = value;
        }

        private _message: string;

        get message(): string {
            return this._message;
        }

        set message(value: string) {
            this._message = value;
        }
    }

    export class Request {
        post = (data: any) => {
            return this.send('POST', data);
        };
        put = (data: any) => {
            return this.send('PUT', data);
        };
        get = () => {
            return this.send('GET');
        };
        delete = () => {
            return this.send('DELETE');
        };
        send = (method: string, data?: any) => {
            return new Promise((resolve: (data) => void, reject: (data: Ajax.Error) => void) => {
                this.xhr.open(method, this.url, true);
                this.xhr.setRequestHeader('Content-Type', 'application/json');
                this.xhr.onreadystatechange = () => {
                    if (this.xhr.readyState === 4 && this.xhr.status === 200) {
                        resolve(JSON.parse(this.xhr.responseText));
                    } else if (this.xhr.readyState === 4) {
                        let error = new Ajax.Error();
                        error.message = this.xhr.statusText;
                        error.details = JSON.parse(this.xhr.responseText);
                        reject(error);
                    }
                };

                this.xhr.send(JSON.stringify(data));
            });
        };
        private xhr: XMLHttpRequest;
        private url: string;

        constructor(url: string) {
            this.xhr = new XMLHttpRequest();
            this.url = url;
        }
    }
}