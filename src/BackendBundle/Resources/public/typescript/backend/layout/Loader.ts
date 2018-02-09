class Loader {
    public static display = ($target: JQuery) => {
        let spinnerHtml = $('#spinner').html();
        $target.html(spinnerHtml);
    }
}