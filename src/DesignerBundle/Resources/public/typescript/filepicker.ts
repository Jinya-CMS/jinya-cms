class FilePicker {
    private static init = (() => {
        let buttons = document.querySelectorAll<HTMLButtonElement>('[data-toggle=file]');
        for (let i = 0; i < buttons.length; i++) {
            let button = buttons.item(i);
            let target = button.getAttribute('data-target');
            let fileInput = document.querySelector<HTMLInputElement>(`input[type=file]#${target}`);
            let textInput = document.querySelector<HTMLInputElement>(`input[type=text][data-id=${target}]`);

            fileInput.addEventListener('change', evt => {
                textInput.value = fileInput.value;
            });

            button.addEventListener('click', evt => fileInput.click());
        }
    })();
}