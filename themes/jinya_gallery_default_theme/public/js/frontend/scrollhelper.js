(function () {
    var scrollItem = document.querySelector('.horizontal');
    if (scrollItem) {
        scrollItem.addEventListener('wheel', function (e) {
            if (!e.deltaX) {
                scrollItem.scrollBy({
                    behavior: 'auto',
                    left: e.deltaY > 0 ? 100 : -100
                });
            }
        });
    }
    var scrolls = document.querySelectorAll('[data-scroll]');
    for (var i = 0; i < scrolls.length; i++) {
        new PerfectScrollbar(scrolls[i]);
    }
}());