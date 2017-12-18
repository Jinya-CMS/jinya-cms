class Delete {
    static init = () => {
        let buttons = document.querySelectorAll('[data-page-delete]');
        for (let i = 0; i < buttons.length; i++) {
            let item = buttons[i];
            let title = item.getAttribute('data-delete-title');
            let message = item.getAttribute('data-delete-content');
            let positiveButton = item.getAttribute('data-delete-positive-button');
            let negativeButton = item.getAttribute('data-delete-negative-button');
            item.addEventListener('click', () => {
                Modal.confirm(title, message, positiveButton, negativeButton).then(() => {
                    let call = new Ajax.Request(item.getAttribute('data-delete-url'));
                    call.delete().then(() => {
                        window.location.href = item.getAttribute('data-redirect-target');
                    }, (data: Ajax.Error) => {
                        Modal.alert(data.message, data.details.message);
                    });
                });
            });
        }
    }
}

Delete.init();