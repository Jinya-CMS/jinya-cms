class Ajax {
    post = (data: any) => {
        return this.send('POST', data);
    };
    put = (data: any) => {
        return this.send('POST', data);
    };
    get = () => {
        return this.send('GET');
    };
    delete = () => {
        return this.send('GET');
    };
    send = (method: string, data?: any) => {
        return new Promise((resolve: (any) => void, reject: (any, message) => void) => {
            this.xhr.open(method, this.url, true);
            this.xhr.setRequestHeader('Content-Type', 'application/json');
            this.xhr.onreadystatechange = () => {
                if (this.xhr.readyState === 4 && this.xhr.status === 200) {
                    resolve(JSON.parse(this.xhr.responseText));
                } else if (this.xhr.readyState === 4) {
                    reject(JSON.parse(this.xhr.responseText), this.xhr.statusText);
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