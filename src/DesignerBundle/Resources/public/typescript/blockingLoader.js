var BlockingLoader = /** @class */ (function () {
    function BlockingLoader() {
    }
    BlockingLoader.show = function () {
        classie.add(BlockingLoader.body, 'show-blocking-loader');
    };
    BlockingLoader.hide = function () {
        classie.remove(BlockingLoader.body, 'show-blocking-loader');
    };
    BlockingLoader.blockingLoaderElement = Util.htmlToElement("\n<div class=\"blocking-loader\">\n    <div class=\"spinner\">\n        <div class=\"double-bounce1\"></div>\n        <div class=\"double-bounce2\"></div>\n    </div>\n</div>");
    BlockingLoader.body = document.querySelector('body');
    BlockingLoader.init = (function () {
        BlockingLoader.body.appendChild(BlockingLoader.blockingLoaderElement);
    })();
    return BlockingLoader;
}());
//# sourceMappingURL=data:application/json;base64,eyJ2ZXJzaW9uIjozLCJmaWxlIjoiYmxvY2tpbmdMb2FkZXIuanMiLCJzb3VyY2VSb290IjoiIiwic291cmNlcyI6WyJibG9ja2luZ0xvYWRlci50cyJdLCJuYW1lcyI6W10sIm1hcHBpbmdzIjoiQUFBQTtJQUFBO0lBa0JBLENBQUM7SUFqQlUsbUJBQUksR0FBRztRQUNWLE9BQU8sQ0FBQyxHQUFHLENBQUMsY0FBYyxDQUFDLElBQUksRUFBRSxzQkFBc0IsQ0FBQyxDQUFDO0lBQzdELENBQUMsQ0FBQztJQUNLLG1CQUFJLEdBQUc7UUFDVixPQUFPLENBQUMsTUFBTSxDQUFDLGNBQWMsQ0FBQyxJQUFJLEVBQUUsc0JBQXNCLENBQUMsQ0FBQztJQUNoRSxDQUFDLENBQUM7SUFDYSxvQ0FBcUIsR0FBRyxJQUFJLENBQUMsYUFBYSxDQUFDLGdMQU12RCxDQUFDLENBQUM7SUFDVSxtQkFBSSxHQUFHLFFBQVEsQ0FBQyxhQUFhLENBQUMsTUFBTSxDQUFDLENBQUM7SUFDdEMsbUJBQUksR0FBRyxDQUFDO1FBQ25CLGNBQWMsQ0FBQyxJQUFJLENBQUMsV0FBVyxDQUFDLGNBQWMsQ0FBQyxxQkFBcUIsQ0FBQyxDQUFDO0lBQzFFLENBQUMsQ0FBQyxFQUFFLENBQUM7SUFDVCxxQkFBQztDQUFBLEFBbEJELElBa0JDIn0=