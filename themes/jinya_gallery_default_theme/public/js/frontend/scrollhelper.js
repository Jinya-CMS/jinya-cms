(function () {
    const scrollItem = document.querySelector('.horizontal');
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

    const scrolls = document.querySelectorAll('[data-scroll]');
    scrolls.forEach((item) => {
        new PerfectScrollbar(item);
    });
}());