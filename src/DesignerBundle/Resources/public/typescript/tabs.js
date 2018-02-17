var Tabs = /** @class */ (function () {
    function Tabs() {
    }
    Tabs.init = (function () {
        var tabs = document.querySelectorAll('[data-toggle=tab]');
        var parents = [];
        var _loop_1 = function (i) {
            var tab_1 = tabs[i];
            parents.push(tab_1.parentElement);
            tab_1.addEventListener('click', function (evt) {
                var target = tab_1.getAttribute('href');
                classiex.remove(document.querySelectorAll('.page.active'), 'active');
                classiex.remove(parents, 'active');
                classie.add(document.querySelector(target), 'active');
                classie.add(tab_1.parentElement, 'active');
            });
        };
        for (var i = 0; i < tabs.length; i++) {
            _loop_1(i);
        }
        var currentHash = document.location.hash;
        var tab;
        if (currentHash) {
            tab = document.querySelector("[href='" + currentHash + "']");
        }
        else {
            if (tabs.length > 0) {
                tab = tabs[0];
            }
        }
        if (tab) {
            tab.click();
        }
    })();
    return Tabs;
}());
//# sourceMappingURL=data:application/json;base64,eyJ2ZXJzaW9uIjozLCJmaWxlIjoidGFicy5qcyIsInNvdXJjZVJvb3QiOiIiLCJzb3VyY2VzIjpbInRhYnMudHMiXSwibmFtZXMiOltdLCJtYXBwaW5ncyI6IkFBQUE7SUFBQTtJQWdDQSxDQUFDO0lBL0JrQixTQUFJLEdBQUcsQ0FBQztRQUNuQixJQUFJLElBQUksR0FBRyxRQUFRLENBQUMsZ0JBQWdCLENBQW9CLG1CQUFtQixDQUFDLENBQUM7UUFDN0UsSUFBSSxPQUFPLEdBQUcsRUFBRSxDQUFDO2dDQUNSLENBQUM7WUFDTixJQUFJLEtBQUcsR0FBRyxJQUFJLENBQUMsQ0FBQyxDQUFDLENBQUM7WUFDbEIsT0FBTyxDQUFDLElBQUksQ0FBQyxLQUFHLENBQUMsYUFBYSxDQUFDLENBQUM7WUFFaEMsS0FBRyxDQUFDLGdCQUFnQixDQUFDLE9BQU8sRUFBRSxVQUFBLEdBQUc7Z0JBQzdCLElBQUksTUFBTSxHQUFHLEtBQUcsQ0FBQyxZQUFZLENBQUMsTUFBTSxDQUFDLENBQUM7Z0JBRXRDLFFBQVEsQ0FBQyxNQUFNLENBQUMsUUFBUSxDQUFDLGdCQUFnQixDQUFDLGNBQWMsQ0FBQyxFQUFFLFFBQVEsQ0FBQyxDQUFDO2dCQUNyRSxRQUFRLENBQUMsTUFBTSxDQUFDLE9BQU8sRUFBRSxRQUFRLENBQUMsQ0FBQztnQkFFbkMsT0FBTyxDQUFDLEdBQUcsQ0FBQyxRQUFRLENBQUMsYUFBYSxDQUFDLE1BQU0sQ0FBQyxFQUFFLFFBQVEsQ0FBQyxDQUFDO2dCQUN0RCxPQUFPLENBQUMsR0FBRyxDQUFDLEtBQUcsQ0FBQyxhQUFhLEVBQUUsUUFBUSxDQUFDLENBQUM7WUFDN0MsQ0FBQyxDQUFDLENBQUM7UUFDUCxDQUFDO1FBYkQsR0FBRyxDQUFDLENBQUMsSUFBSSxDQUFDLEdBQUcsQ0FBQyxFQUFFLENBQUMsR0FBRyxJQUFJLENBQUMsTUFBTSxFQUFFLENBQUMsRUFBRTtvQkFBM0IsQ0FBQztTQWFUO1FBRUQsSUFBSSxXQUFXLEdBQUcsUUFBUSxDQUFDLFFBQVEsQ0FBQyxJQUFJLENBQUM7UUFDekMsSUFBSSxHQUFzQixDQUFDO1FBQzNCLEVBQUUsQ0FBQyxDQUFDLFdBQVcsQ0FBQyxDQUFDLENBQUM7WUFDZCxHQUFHLEdBQUcsUUFBUSxDQUFDLGFBQWEsQ0FBb0IsWUFBVSxXQUFXLE9BQUksQ0FBQyxDQUFDO1FBQy9FLENBQUM7UUFBQyxJQUFJLENBQUMsQ0FBQztZQUNKLEVBQUUsQ0FBQyxDQUFDLElBQUksQ0FBQyxNQUFNLEdBQUcsQ0FBQyxDQUFDLENBQUMsQ0FBQztnQkFDbEIsR0FBRyxHQUFHLElBQUksQ0FBQyxDQUFDLENBQUMsQ0FBQztZQUNsQixDQUFDO1FBQ0wsQ0FBQztRQUNELEVBQUUsQ0FBQyxDQUFDLEdBQUcsQ0FBQyxDQUFDLENBQUM7WUFDTixHQUFHLENBQUMsS0FBSyxFQUFFLENBQUM7UUFDaEIsQ0FBQztJQUNMLENBQUMsQ0FBQyxFQUFFLENBQUM7SUFDVCxXQUFDO0NBQUEsQUFoQ0QsSUFnQ0MifQ==