var scrollhelper = /** @class */ (function () {
    function scrollhelper() {
    }
    scrollhelper.init = (function () {
        var scrollItem = document.querySelector('.horizontal-scroll');
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
    })();
    return scrollhelper;
}());
//# sourceMappingURL=data:application/json;base64,eyJ2ZXJzaW9uIjozLCJmaWxlIjoiaG9yaXpvbnRhbFNjcm9sbC5qcyIsInNvdXJjZVJvb3QiOiIiLCJzb3VyY2VzIjpbImhvcml6b250YWxTY3JvbGwudHMiXSwibmFtZXMiOltdLCJtYXBwaW5ncyI6IkFBQUE7SUFBQTtJQWNBLENBQUM7SUFia0IsaUJBQUksR0FBRyxDQUFDO1FBQ25CLElBQUksVUFBVSxHQUFHLFFBQVEsQ0FBQyxhQUFhLENBQUMsb0JBQW9CLENBQUMsQ0FBQztRQUM5RCxFQUFFLENBQUMsQ0FBQyxVQUFVLENBQUMsQ0FBQyxDQUFDO1lBQ2IsVUFBVSxDQUFDLGdCQUFnQixDQUFDLE9BQU8sRUFBRSxVQUFDLENBQUM7Z0JBQ25DLEVBQUUsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLE1BQU0sQ0FBQyxDQUFDLENBQUM7b0JBQ1osVUFBVSxDQUFDLFFBQVEsQ0FBQzt3QkFDaEIsUUFBUSxFQUFFLE1BQU07d0JBQ2hCLElBQUksRUFBRSxDQUFDLENBQUMsTUFBTSxHQUFHLENBQUMsQ0FBQyxDQUFDLENBQUMsR0FBRyxDQUFDLENBQUMsQ0FBQyxDQUFDLEdBQUc7cUJBQ2xDLENBQUMsQ0FBQztnQkFDUCxDQUFDO1lBQ0wsQ0FBQyxDQUFDLENBQUM7UUFDUCxDQUFDO0lBQ0wsQ0FBQyxDQUFDLEVBQUUsQ0FBQztJQUNULG1CQUFDO0NBQUEsQUFkRCxJQWNDIn0=