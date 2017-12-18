class Editor {
    static init = () => {
        let editors = document.querySelectorAll('[data-html-editor]');
        for (let i = 0; i < editors.length; i++) {
            let item = editors[i];
            let quill = new Quill(item, {
                theme: 'snow'
            });

            let target = item.getAttribute('data-input-target');
            quill.on('text-change', function () {
                (document.querySelector(target) as any).value = quill.container.firstChild.innerHTML;
            });
        }
    }
}

Editor.init();