class BlockingLoader {
    static show = () => {
        classie.add(BlockingLoader.body, 'show-blocking-loader');
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
    })();
}