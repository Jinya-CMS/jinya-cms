class NavigationSwitcher {

    public init = () => {
        let $sidebar = $('.sidebar');
        let $sidebarItems = $sidebar.find('[data-action=nav]');

        $sidebarItems.click(this.loadContent);
    };

    loadContent() {
        event.preventDefault();
        let $this = $(this);
        let $body = $('body');
        let target = $this.data('target');
        let href = $this.attr('href');
        let $container = $('[data-role=content]');
        let spinnerHtml = $('#spinner').html();
        let $sidebar = $('.sidebar');
        let actionType = $this.data('action-type');
        let responseHasId = $this.data('response-has-id');
        let bodyId = $body.data(TableElementSelector.selectedIdAttribute);
        let id = bodyId ? bodyId : $this.data('id');

        $container.html(spinnerHtml);
        href = href.replace(encodeURIComponent('#temp#'), id);

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
            $this.addClass('active');
            if (responseHasId as boolean) {
                $sidebar.removeClass('no-edit');
            } else {
                $sidebar.addClass('no-edit');
            }

            TableElementSelector.init();
        });
    }
}

let navSwitcher = new NavigationSwitcher();
navSwitcher.init();