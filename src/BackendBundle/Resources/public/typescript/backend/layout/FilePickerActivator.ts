class FilePickerActivator {
    public static init() {
        let $fileInputs = $('input[type=file]');
        $fileInputs.each((index, element) => {
            let $element = $(element);
            let $parent = $element.parent();
            let placeholder = $element.attr('placeholder');
            let $inputGroup = $('<div/>', {
                'class': 'input-group'
            });
            let $input = $('<input/>', {
                'type': 'text',
                'class': 'form-control',
                'placeholder': placeholder,
                'aria-label': placeholder,
                'readonly': true
            });
            let $span = $('<span/>', {
                'class': 'input-group-btn'
            });
            let $button = $('<button/>', {
                'class': 'btn btn-secondary',
                'type': 'button',
                'text': Util.getText('generic.file.pick')
            });
            $element.hide();
            $parent.append(
                $inputGroup.append(
                    $input
                ).append(
                    $span.append(
                        $button
                    )
                )
            );
            $button.click(() => {
                $element.click();
            });
            $element.change(() => {
                $input.val($element.val());
            });
        })
    }
}

$(() => {
    FilePickerActivator.init();
});