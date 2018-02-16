var scrollhelper = /** @class */ (function () {
    function scrollhelper() {
    }
    scrollhelper.init = function () {
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
    };
    return scrollhelper;
}());
scrollhelper.init();
//# sourceMappingURL=data:application/json;base64,eyJ2ZXJzaW9uIjozLCJmaWxlIjoic2Nyb2xsaGVscGVyLmpzIiwic291cmNlUm9vdCI6IiIsInNvdXJjZXMiOlsic2Nyb2xsaGVscGVyLnRzIl0sIm5hbWVzIjpbXSwibWFwcGluZ3MiOiJBQUFBO0lBQUE7SUFtQkEsQ0FBQztJQWxCVSxpQkFBSSxHQUFHO1FBQ1YsSUFBSSxVQUFVLEdBQUcsUUFBUSxDQUFDLGFBQWEsQ0FBQyxhQUFhLENBQUMsQ0FBQztRQUN2RCxFQUFFLENBQUMsQ0FBQyxVQUFVLENBQUMsQ0FBQyxDQUFDO1lBQ2IsVUFBVSxDQUFDLGdCQUFnQixDQUFDLE9BQU8sRUFBRSxVQUFDLENBQUM7Z0JBQ25DLEVBQUUsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLE1BQU0sQ0FBQyxDQUFDLENBQUM7b0JBQ1osVUFBVSxDQUFDLFFBQVEsQ0FBQzt3QkFDaEIsUUFBUSxFQUFFLE1BQU07d0JBQ2hCLElBQUksRUFBRSxDQUFDLENBQUMsTUFBTSxHQUFHLENBQUMsQ0FBQyxDQUFDLENBQUMsR0FBRyxDQUFDLENBQUMsQ0FBQyxDQUFDLEdBQUc7cUJBQ2xDLENBQUMsQ0FBQztnQkFDUCxDQUFDO1lBQ0wsQ0FBQyxDQUFDLENBQUM7UUFDUCxDQUFDO1FBRUQsSUFBSSxPQUFPLEdBQUcsUUFBUSxDQUFDLGdCQUFnQixDQUFDLGVBQWUsQ0FBQyxDQUFDO1FBQ3pELEdBQUcsQ0FBQyxDQUFDLElBQUksQ0FBQyxHQUFHLENBQUMsRUFBRSxDQUFDLEdBQUcsT0FBTyxDQUFDLE1BQU0sRUFBRSxDQUFDLEVBQUUsRUFBRSxDQUFDO1lBQ3RDLElBQUksZ0JBQWdCLENBQUMsT0FBTyxDQUFDLENBQUMsQ0FBZ0IsQ0FBQyxDQUFDO1FBQ3BELENBQUM7SUFDTCxDQUFDLENBQUE7SUFDTCxtQkFBQztDQUFBLEFBbkJELElBbUJDO0FBRUQsWUFBWSxDQUFDLElBQUksRUFBRSxDQUFDIn0=