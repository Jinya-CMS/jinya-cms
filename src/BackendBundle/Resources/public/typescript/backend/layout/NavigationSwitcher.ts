declare function initSelectize(): void;

class NavigationSwitcher {

    public static init = () => {
        let $sidebar = $('.sidebar');
        let $sidebarItems = $sidebar.find('[data-action=nav]');

        $sidebarItems.off('click', NavigationSwitcher.loadContent);
        $sidebarItems.click(NavigationSwitcher.loadContent);
    };

    public static navigate(url: string, $source: JQuery, responseHasId?: boolean, id?: string) {
        let $body = $('body');
        let bodyId = $body.data(TableElementSelector.selectedIdAttribute);
        let targetId = bodyId ? bodyId : id;
        let $container = $('[data-role=content]');
        let $sidebar = $('.sidebar');
        let href = url.replace(encodeURIComponent('#temp#'), targetId);
        let afterSuccessAction = $source.data('after-success');
        let afterFailureAction = $source.data('after-failure');

        Loader.display($container);
        $.get(href).then((response, status, xhr) => {
            if (xhr.getResponseHeader('login') === '1') {
                location.href = href;
            } else if (xhr.status === 200) {
                $container.html(response).after(() => {
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
            } else {
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
            if (responseHasId as boolean) {
                $sidebar.removeClass('no-edit');
            } else {
                $sidebar.addClass('no-edit');
            }

            currentTable = null;
        });
    }

    private static loadContent(event) {
        event.preventDefault();
        let $this = $(this);
        let href = $this.attr('href');
        let responseHasId = $this.data('response-has-id');
        let $source = $this.data('source') ? $($this.data('source')) : $this;

        NavigationSwitcher.navigate(href, $source, responseHasId, $this.data('id'));
    }
}

NavigationSwitcher.init();