class scrollhelper {
    private static init = (() => {
        let scrollItem = document.querySelector('.horizontal-scroll');
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
    })();
}