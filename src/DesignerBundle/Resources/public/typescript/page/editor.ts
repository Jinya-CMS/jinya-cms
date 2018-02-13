class Editor {
    private static init = (() => {
        let editors = document.querySelectorAll('[data-html-editor]');
        const toolbarOptions = [
            ['bold', 'italic', 'underline', 'strike'],
            ['blockquote', 'code-block'],

            [{'list': 'ordered'}, {'list': 'bullet'}],
            [{'script': 'sub'}, {'script': 'super'}],
            [{'indent': '-1'}, {'indent': '+1'}],
            [{'direction': 'rtl'}],

            [{'size': ['small', false, 'large', 'huge']}],
            [{'header': [1, 2, 3, 4, 5, 6, false]}],

            [{'color': []}, {'background': []}],
            [{'align': []}],

            ['video', 'image'],

            ['clean']
        ];

        for (let i = 0; i < editors.length; i++) {
            let item = editors[i];

            let quill = new Quill(item, {
                theme: 'snow',
                modules: {
                    toolbar: toolbarOptions
                }
            });

            let target = item.getAttribute('data-input-target');
            quill.on('text-change', function () {
                document.querySelector<HTMLInputElement>(target).value = quill.container.firstChild.innerHTML;
            });
        }
    })();
}