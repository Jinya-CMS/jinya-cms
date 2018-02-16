class scrollhelper {
    static init = () => {
        let scrollItem = document.querySelector('.horizontal');
        if (scrollItem) {
            scrollItem.addEventListener('wheel', (e) => {
                if (!e.deltaX) {
                    scrollItem.scrollBy({
                        behavior: 'auto',
                        left: e.deltaY > 0 ? 100 : -100
                    });
                }
            });
        }

        let scrolls = document.querySelectorAll('[data-scroll]');
        for (let i = 0; i < scrolls.length; i++) {
            new PerfectScrollbar(scrolls[i] as HTMLElement);
        }
    }
}

scrollhelper.init();