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
        let spinnerHtml = $('#spinner').html();
        let $sidebar = $('.sidebar');
        let href = url.replace(encodeURIComponent('#temp#'), targetId);

        $container.html(spinnerHtml);
        $container.load(href, (response, status, xhr) => {
            if (xhr.getResponseHeader('login') === '1') {
                location.href = href;
            } else if (xhr.status === 200) {
                history.pushState('', document.title, href);
                $('.sidebar.bg-danger')
                    .removeClass('bg-danger')
                    .addClass('bg-primary');
                $('.navbar.bg-danger')
                    .removeClass('bg-danger')
                    .addClass('bg-primary');
            } else {
                $('.sidebar.bg-primary')
                    .removeClass('bg-primary')
                    .addClass('bg-danger');
                $('.navbar.bg-primary')
                    .removeClass('bg-primary')
                    .addClass('bg-danger');
            }
            $('.sidebar a.active').removeClass('active');
            $source.addClass('active');
            if (responseHasId as boolean) {
                $sidebar.removeClass('no-edit');
            } else {
                $sidebar.addClass('no-edit');
            }

            currentTable = null;
            DatatablesActivator.init();
            TableElementSelector.init();
            Searcher.init();
            AjaxExecutor.init();
            NavigationSwitcher.init();
        });
    }

    private static loadContent() {
        event.preventDefault();
        let $this = $(this);
        let href = $this.attr('href');
        let responseHasId = $this.data('response-has-id');
        let $source = $this.data('source') ? $($this.data('source')) : $this;

        NavigationSwitcher.navigate(href, $source, responseHasId, $this.data('id'));
    }
}

NavigationSwitcher.init();