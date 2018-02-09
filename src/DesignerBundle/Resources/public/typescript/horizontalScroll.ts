class HorizontalScroll {
    static init = () => {
        let scrollItem = document.querySelector('.horizontal-scroll');
        if (scrollItem) {
            scrollItem.addEventListener('wheel', (e) => {
                if (!e.deltaX) {
                    scrollItem.scrollTo({
                        behavior: 'auto',
                        left: scrollItem.scrollLeft + e.deltaY
                    });
                }
            });
        }
    }
}

HorizontalScroll.init();