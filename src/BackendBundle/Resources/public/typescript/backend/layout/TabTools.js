var TabTools = /** @class */ (function () {
    function TabTools() {
    }
    TabTools.init = function () {
        var $tabs = $('.nav[role=tablist]');
        var hash = location.hash;
        if (hash) {
            $("[href=\"" + hash + "\"]").tab('show');
        }
        else {
            $('.nav[role=tablist] a:first').tab('show');
        }
        $('a[data-toggle=tab]').on('shown.bs.tab', function (e) {
            location.hash = $(e.target).attr('href');
        });
    };
    return TabTools;
}());
$(function () {
    TabTools.init();
});
//# sourceMappingURL=data:application/json;base64,eyJ2ZXJzaW9uIjozLCJmaWxlIjoiVGFiVG9vbHMuanMiLCJzb3VyY2VSb290IjoiIiwic291cmNlcyI6WyJUYWJUb29scy50cyJdLCJuYW1lcyI6W10sIm1hcHBpbmdzIjoiQUFBQTtJQUFBO0lBYUEsQ0FBQztJQVppQixhQUFJLEdBQWxCO1FBQ0ksSUFBSSxLQUFLLEdBQUcsQ0FBQyxDQUFDLG9CQUFvQixDQUFDLENBQUM7UUFDcEMsSUFBSSxJQUFJLEdBQUcsUUFBUSxDQUFDLElBQUksQ0FBQztRQUN6QixFQUFFLENBQUMsQ0FBQyxJQUFJLENBQUMsQ0FBQyxDQUFDO1lBQ1AsQ0FBQyxDQUFDLGFBQVUsSUFBSSxRQUFJLENBQUMsQ0FBQyxHQUFHLENBQUMsTUFBTSxDQUFDLENBQUM7UUFDdEMsQ0FBQztRQUFDLElBQUksQ0FBQyxDQUFDO1lBQ0osQ0FBQyxDQUFDLDRCQUE0QixDQUFDLENBQUMsR0FBRyxDQUFDLE1BQU0sQ0FBQyxDQUFDO1FBQ2hELENBQUM7UUFDRCxDQUFDLENBQUMsb0JBQW9CLENBQUMsQ0FBQyxFQUFFLENBQUMsY0FBYyxFQUFFLFVBQUMsQ0FBQztZQUN6QyxRQUFRLENBQUMsSUFBSSxHQUFHLENBQUMsQ0FBQyxDQUFDLENBQUMsTUFBTSxDQUFDLENBQUMsSUFBSSxDQUFDLE1BQU0sQ0FBQyxDQUFDO1FBQzdDLENBQUMsQ0FBQyxDQUFDO0lBQ1AsQ0FBQztJQUNMLGVBQUM7QUFBRCxDQUFDLEFBYkQsSUFhQztBQUVELENBQUMsQ0FBQztJQUNFLFFBQVEsQ0FBQyxJQUFJLEVBQUUsQ0FBQztBQUNwQixDQUFDLENBQUMsQ0FBQyJ9