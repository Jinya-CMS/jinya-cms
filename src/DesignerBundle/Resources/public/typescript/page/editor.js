var Editor = /** @class */ (function () {
    function Editor() {
    }
    Editor.init = (function () {
        var editors = document.querySelectorAll('[data-html-editor]');
        var toolbarOptions = [
            ['bold', 'italic', 'underline', 'strike'],
            ['blockquote', 'code-block'],
            [{ 'list': 'ordered' }, { 'list': 'bullet' }],
            [{ 'script': 'sub' }, { 'script': 'super' }],
            [{ 'indent': '-1' }, { 'indent': '+1' }],
            [{ 'direction': 'rtl' }],
            [{ 'size': ['small', false, 'large', 'huge'] }],
            [{ 'header': [1, 2, 3, 4, 5, 6, false] }],
            [{ 'color': [] }, { 'background': [] }],
            [{ 'align': [] }],
            ['video', 'image'],
            ['clean']
        ];
        var _loop_1 = function (i) {
            var item = editors[i];
            var quill = new Quill(item, {
                theme: 'snow',
                modules: {
                    toolbar: toolbarOptions
                }
            });
            var target = item.getAttribute('data-input-target');
            quill.on('text-change', function () {
                document.querySelector(target).value = quill.container.firstChild.innerHTML;
            });
        };
        for (var i = 0; i < editors.length; i++) {
            _loop_1(i);
        }
    })();
    return Editor;
}());
//# sourceMappingURL=data:application/json;base64,eyJ2ZXJzaW9uIjozLCJmaWxlIjoiZWRpdG9yLmpzIiwic291cmNlUm9vdCI6IiIsInNvdXJjZXMiOlsiZWRpdG9yLnRzIl0sIm5hbWVzIjpbXSwibWFwcGluZ3MiOiJBQUFBO0lBQUE7SUF1Q0EsQ0FBQztJQXRDa0IsV0FBSSxHQUFHLENBQUM7UUFDbkIsSUFBSSxPQUFPLEdBQUcsUUFBUSxDQUFDLGdCQUFnQixDQUFDLG9CQUFvQixDQUFDLENBQUM7UUFDOUQsSUFBTSxjQUFjLEdBQUc7WUFDbkIsQ0FBQyxNQUFNLEVBQUUsUUFBUSxFQUFFLFdBQVcsRUFBRSxRQUFRLENBQUM7WUFDekMsQ0FBQyxZQUFZLEVBQUUsWUFBWSxDQUFDO1lBRTVCLENBQUMsRUFBQyxNQUFNLEVBQUUsU0FBUyxFQUFDLEVBQUUsRUFBQyxNQUFNLEVBQUUsUUFBUSxFQUFDLENBQUM7WUFDekMsQ0FBQyxFQUFDLFFBQVEsRUFBRSxLQUFLLEVBQUMsRUFBRSxFQUFDLFFBQVEsRUFBRSxPQUFPLEVBQUMsQ0FBQztZQUN4QyxDQUFDLEVBQUMsUUFBUSxFQUFFLElBQUksRUFBQyxFQUFFLEVBQUMsUUFBUSxFQUFFLElBQUksRUFBQyxDQUFDO1lBQ3BDLENBQUMsRUFBQyxXQUFXLEVBQUUsS0FBSyxFQUFDLENBQUM7WUFFdEIsQ0FBQyxFQUFDLE1BQU0sRUFBRSxDQUFDLE9BQU8sRUFBRSxLQUFLLEVBQUUsT0FBTyxFQUFFLE1BQU0sQ0FBQyxFQUFDLENBQUM7WUFDN0MsQ0FBQyxFQUFDLFFBQVEsRUFBRSxDQUFDLENBQUMsRUFBRSxDQUFDLEVBQUUsQ0FBQyxFQUFFLENBQUMsRUFBRSxDQUFDLEVBQUUsQ0FBQyxFQUFFLEtBQUssQ0FBQyxFQUFDLENBQUM7WUFFdkMsQ0FBQyxFQUFDLE9BQU8sRUFBRSxFQUFFLEVBQUMsRUFBRSxFQUFDLFlBQVksRUFBRSxFQUFFLEVBQUMsQ0FBQztZQUNuQyxDQUFDLEVBQUMsT0FBTyxFQUFFLEVBQUUsRUFBQyxDQUFDO1lBRWYsQ0FBQyxPQUFPLEVBQUUsT0FBTyxDQUFDO1lBRWxCLENBQUMsT0FBTyxDQUFDO1NBQ1osQ0FBQztnQ0FFTyxDQUFDO1lBQ04sSUFBSSxJQUFJLEdBQUcsT0FBTyxDQUFDLENBQUMsQ0FBQyxDQUFDO1lBRXRCLElBQUksS0FBSyxHQUFHLElBQUksS0FBSyxDQUFDLElBQUksRUFBRTtnQkFDeEIsS0FBSyxFQUFFLE1BQU07Z0JBQ2IsT0FBTyxFQUFFO29CQUNMLE9BQU8sRUFBRSxjQUFjO2lCQUMxQjthQUNKLENBQUMsQ0FBQztZQUVILElBQUksTUFBTSxHQUFHLElBQUksQ0FBQyxZQUFZLENBQUMsbUJBQW1CLENBQUMsQ0FBQztZQUNwRCxLQUFLLENBQUMsRUFBRSxDQUFDLGFBQWEsRUFBRTtnQkFDcEIsUUFBUSxDQUFDLGFBQWEsQ0FBbUIsTUFBTSxDQUFDLENBQUMsS0FBSyxHQUFHLEtBQUssQ0FBQyxTQUFTLENBQUMsVUFBVSxDQUFDLFNBQVMsQ0FBQztZQUNsRyxDQUFDLENBQUMsQ0FBQztRQUNQLENBQUM7UUFkRCxHQUFHLENBQUMsQ0FBQyxJQUFJLENBQUMsR0FBRyxDQUFDLEVBQUUsQ0FBQyxHQUFHLE9BQU8sQ0FBQyxNQUFNLEVBQUUsQ0FBQyxFQUFFO29CQUE5QixDQUFDO1NBY1Q7SUFDTCxDQUFDLENBQUMsRUFBRSxDQUFDO0lBQ1QsYUFBQztDQUFBLEFBdkNELElBdUNDIn0=