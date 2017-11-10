class DeleteBackgroundImage {
    public static init() {
        let $galleryThumbnailContainer = $('.gallery-thumbnail-container');
        $galleryThumbnailContainer.click(() => {
            let url = $galleryThumbnailContainer.data('delete-url');
            $.ajax(url, {
                method: 'DELETE'
            }).then(() => {
                $galleryThumbnailContainer.parent('.row').hide();
            })
        });
    }
}

$(() => {
    DeleteBackgroundImage.init();
});