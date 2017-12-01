class BlockingLoader {
    static show = () => {
        classie.add(BlockingLoader.body, 'show-blocking-loader');
    };
    static initOnXhr = () => {
        let monitorXhr = (req: XMLHttpRequest) => {
            let _open = req.open;
            req.open = function (type, url, async) {
                BlockingLoader.show();

                _open.apply(req, arguments);
                let _onreadystatechange = req.onreadystatechange;
                req.onreadystatechange = function (event) {
                    if (req.readyState == 4) {
                        BlockingLoader.hide();
                    }
                    _onreadystatechange.apply(req, arguments);
                };
            };
            return req;
        };

        let _XMLHttpRequest = (window as any).XMLHttpRequest;

        (window as any).XMLHttpRequest = (flags) => {
            let req = new _XMLHttpRequest(flags);
            return monitorXhr(req);
        }
    };
    static hide = () => {
        classie.remove(BlockingLoader.body, 'show-blocking-loader');
    };
    private static blockingLoaderElement = Util.htmlToElement(`
<div class="blocking-loader">
    <div class="spinner">
        <div class="double-bounce1"></div>
        <div class="double-bounce2"></div>
    </div>
</div>`);
    private static body = document.querySelector('body');
    private static init = (() => {
        BlockingLoader.body.appendChild(BlockingLoader.blockingLoaderElement);
        BlockingLoader.initOnXhr();
    })();
}