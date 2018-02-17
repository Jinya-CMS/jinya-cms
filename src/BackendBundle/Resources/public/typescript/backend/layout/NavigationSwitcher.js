var NavigationSwitcher = /** @class */ (function () {
    function NavigationSwitcher() {
    }
    NavigationSwitcher.navigate = function (url, $source, responseHasId, id) {
        var $body = $('body');
        var bodyId = $body.data(TableElementSelector.selectedIdAttribute);
        var targetId = bodyId ? bodyId : id;
        var $container = $('[data-role=content]');
        var $sidebar = $('.sidebar');
        var href = url.replace(encodeURIComponent('#temp#'), targetId);
        var afterSuccessAction = $source.data('after-success');
        var afterFailureAction = $source.data('after-failure');
        Loader.display($container);
        $.get(href).then(function (response, status, xhr) {
            if (xhr.getResponseHeader('login') === '1') {
                location.href = href;
            }
            else if (xhr.status === 200) {
                $container.html(response).after(function () {
                    history.pushState('', document.title, href);
                    $('.sidebar.bg-danger')
                        .removeClass('bg-danger')
                        .addClass('bg-primary');
                    $('.navbar.bg-danger')
                        .removeClass('bg-danger')
                        .addClass('bg-primary');
                    DatatablesActivator.init();
                    TableElementSelector.init();
                    Searcher.init();
                    AjaxExecutor.init();
                    NavigationSwitcher.init();
                    FilePickerActivator.init();
                    eval(afterSuccessAction);
                    return $container;
                });
            }
            else {
                $('.sidebar.bg-primary')
                    .removeClass('bg-primary')
                    .addClass('bg-danger');
                $('.navbar.bg-primary')
                    .removeClass('bg-primary')
                    .addClass('bg-danger');
                eval(afterFailureAction);
            }
            $('.sidebar a.active').removeClass('active');
            $source.addClass('active');
            if (responseHasId) {
                $sidebar.removeClass('no-edit');
            }
            else {
                $sidebar.addClass('no-edit');
            }
            currentTable = null;
        });
    };
    NavigationSwitcher.loadContent = function (event) {
        event.preventDefault();
        var $this = $(this);
        var href = $this.attr('href');
        var responseHasId = $this.data('response-has-id');
        var $source = $this.data('source') ? $($this.data('source')) : $this;
        NavigationSwitcher.navigate(href, $source, responseHasId, $this.data('id'));
    };
    NavigationSwitcher.init = function () {
        var $sidebar = $('.sidebar');
        var $sidebarItems = $sidebar.find('[data-action=nav]');
        $sidebarItems.off('click', NavigationSwitcher.loadContent);
        $sidebarItems.click(NavigationSwitcher.loadContent);
    };
    return NavigationSwitcher;
}());
NavigationSwitcher.init();
//# sourceMappingURL=data:application/json;base64,eyJ2ZXJzaW9uIjozLCJmaWxlIjoiTmF2aWdhdGlvblN3aXRjaGVyLmpzIiwic291cmNlUm9vdCI6IiIsInNvdXJjZXMiOlsiTmF2aWdhdGlvblN3aXRjaGVyLnRzIl0sIm5hbWVzIjpbXSwibWFwcGluZ3MiOiJBQUVBO0lBQUE7SUEyRUEsQ0FBQztJQWpFaUIsMkJBQVEsR0FBdEIsVUFBdUIsR0FBVyxFQUFFLE9BQWUsRUFBRSxhQUF1QixFQUFFLEVBQVc7UUFDckYsSUFBSSxLQUFLLEdBQUcsQ0FBQyxDQUFDLE1BQU0sQ0FBQyxDQUFDO1FBQ3RCLElBQUksTUFBTSxHQUFHLEtBQUssQ0FBQyxJQUFJLENBQUMsb0JBQW9CLENBQUMsbUJBQW1CLENBQUMsQ0FBQztRQUNsRSxJQUFJLFFBQVEsR0FBRyxNQUFNLENBQUMsQ0FBQyxDQUFDLE1BQU0sQ0FBQyxDQUFDLENBQUMsRUFBRSxDQUFDO1FBQ3BDLElBQUksVUFBVSxHQUFHLENBQUMsQ0FBQyxxQkFBcUIsQ0FBQyxDQUFDO1FBQzFDLElBQUksUUFBUSxHQUFHLENBQUMsQ0FBQyxVQUFVLENBQUMsQ0FBQztRQUM3QixJQUFJLElBQUksR0FBRyxHQUFHLENBQUMsT0FBTyxDQUFDLGtCQUFrQixDQUFDLFFBQVEsQ0FBQyxFQUFFLFFBQVEsQ0FBQyxDQUFDO1FBQy9ELElBQUksa0JBQWtCLEdBQUcsT0FBTyxDQUFDLElBQUksQ0FBQyxlQUFlLENBQUMsQ0FBQztRQUN2RCxJQUFJLGtCQUFrQixHQUFHLE9BQU8sQ0FBQyxJQUFJLENBQUMsZUFBZSxDQUFDLENBQUM7UUFFdkQsTUFBTSxDQUFDLE9BQU8sQ0FBQyxVQUFVLENBQUMsQ0FBQztRQUMzQixDQUFDLENBQUMsR0FBRyxDQUFDLElBQUksQ0FBQyxDQUFDLElBQUksQ0FBQyxVQUFDLFFBQVEsRUFBRSxNQUFNLEVBQUUsR0FBRztZQUNuQyxFQUFFLENBQUMsQ0FBQyxHQUFHLENBQUMsaUJBQWlCLENBQUMsT0FBTyxDQUFDLEtBQUssR0FBRyxDQUFDLENBQUMsQ0FBQztnQkFDekMsUUFBUSxDQUFDLElB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