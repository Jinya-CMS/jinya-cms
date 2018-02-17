/**
 * main.js
 * http://www.codrops.com
 *
 * Licensed under the MIT license.
 * http://www.opensource.org/licenses/mit-license.php
 *
 * Copyright 2014, Codrops
 * http://www.codrops.com
 */
var DesignerMenu = /** @class */ (function () {
    function DesignerMenu() {
        var _this = this;
        this.isOpen = false;
        this.initEvents = function () {
            _this.openbtn.addEventListener('click', _this.toggleMenu);
            // close the menu element if the target it´s not the menu element or one of its descendants..
            _this.content.addEventListener('click', function (evt) {
                var target = evt.target;
                if (_this.isOpen && target !== _this.openbtn) {
                    _this.toggleMenu();
                }
            });
        };
        this.toggleMenu = function () {
            if (_this.isOpen) {
                classie.remove(_this.body, 'show-menu');
            }
            else {
                classie.add(_this.body, 'show-menu');
            }
            _this.isOpen = !_this.isOpen;
        };
        this.content = document.querySelector('.content-wrap');
        this.openbtn = document.querySelector('[data-toggle=designer-menu]');
        this.body = document.querySelector('body');
    }
    DesignerMenu.init = (function () {
        var designerMenu = new DesignerMenu();
        designerMenu.initEvents();
    })();
    return DesignerMenu;
}());
//# sourceMappingURL=data:application/json;base64,eyJ2ZXJzaW9uIjozLCJmaWxlIjoiZGVzaWduZXItbWVudS5qcyIsInNvdXJjZVJvb3QiOiIiLCJzb3VyY2VzIjpbImRlc2lnbmVyLW1lbnUudHMiXSwibmFtZXMiOltdLCJtYXBwaW5ncyI6IkFBQUE7Ozs7Ozs7OztHQVNHO0FBQ0g7SUE4Qkk7UUFBQSxpQkFJQztRQTdCTyxXQUFNLEdBQUcsS0FBSyxDQUFDO1FBSWYsZUFBVSxHQUFHO1lBQ2pCLEtBQUksQ0FBQyxPQUFPLENBQUMsZ0JBQWdCLENBQUMsT0FBTyxFQUFFLEtBQUksQ0FBQyxVQUFVLENBQUMsQ0FBQztZQUV4RCw2RkFBNkY7WUFDN0YsS0FBSSxDQUFDLE9BQU8sQ0FBQyxnQkFBZ0IsQ0FBQyxPQUFPLEVBQUUsVUFBQyxHQUFHO2dCQUN2QyxJQUFJLE1BQU0sR0FBRyxHQUFHLENBQUMsTUFBTSxDQUFDO2dCQUN4QixFQUFFLENBQUMsQ0FBQyxLQUFJLENBQUMsTUFBTSxJQUFJLE1BQU0sS0FBSyxLQUFJLENBQUMsT0FBTyxDQUFDLENBQUMsQ0FBQztvQkFDekMsS0FBSSxDQUFDLFVBQVUsRUFBRSxDQUFDO2dCQUN0QixDQUFDO1lBQ0wsQ0FBQyxDQUFDLENBQUM7UUFDUCxDQUFDLENBQUM7UUFDTSxlQUFVLEdBQUc7WUFDakIsRUFBRSxDQUFDLENBQUMsS0FBSSxDQUFDLE1BQU0sQ0FBQyxDQUFDLENBQUM7Z0JBQ2QsT0FBTyxDQUFDLE1BQU0sQ0FBQyxLQUFJLENBQUMsSUFBSSxFQUFFLFdBQVcsQ0FBQyxDQUFDO1lBQzNDLENBQUM7WUFDRCxJQUFJLENBQUMsQ0FBQztnQkFDRixPQUFPLENBQUMsR0FBRyxDQUFDLEtBQUksQ0FBQyxJQUFJLEVBQUUsV0FBVyxDQUFDLENBQUM7WUFDeEMsQ0FBQztZQUNELEtBQUksQ0FBQyxNQUFNLEdBQUcsQ0FBQyxLQUFJLENBQUMsTUFBTSxDQUFDO1FBQy9CLENBQUMsQ0FBQztRQUdFLElBQUksQ0FBQyxPQUFPLEdBQUcsUUFBUSxDQUFDLGFBQWEsQ0FBaUIsZUFBZSxDQUFDLENBQUM7UUFDdkUsSUFBSSxDQUFDLE9BQU8sR0FBRyxRQUFRLENBQUMsYUFBYSxDQUFvQiw2QkFBNkIsQ0FBQyxDQUFDO1FBQ3hGLElBQUksQ0FBQyxJQUFJLEdBQUcsUUFBUSxDQUFDLGFBQWEsQ0FBa0IsTUFBTSxDQUFDLENBQUM7SUFDaEUsQ0FBQztJQWpDYyxpQkFBSSxHQUFHLENBQUM7UUFDbkIsSUFBSSxZQUFZLEdBQUcsSUFBSSxZQUFZLEVBQUUsQ0FBQztRQUN0QyxZQUFZLENBQUMsVUFBVSxFQUFFLENBQUM7SUFDOUIsQ0FBQyxDQUFDLEVBQUUsQ0FBQztJQStCVCxtQkFBQztDQUFBLEFBbkNELElBbUNDIn0=