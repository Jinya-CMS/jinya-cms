(() => {
    document.addEventListener('DOMContentLoaded', () => {
        const scrollItem = document.querySelector('.is--horizontal');

        function scrollHandler(e)
        {
            if (!e.deltaX) {
                scrollItem.scrollBy({
                    behavior: 'auto',
                    left: e.deltaY > 0 ? 100 : -100,
                });
            }
        }

        if (scrollItem) {
            if (window.innerWidth >= 1280) {
                scrollItem.addEventListener('wheel', scrollHandler);
            }
            document.addEventListener('resize', () => {
                if (window.innerWidth < 1280) {
                    scrollItem.removeEventListener('wheel', scrollHandler);
                } else {
                    scrollItem.addEventListener('wheel', scrollHandler);
                }
            });
        }
    });
})();
