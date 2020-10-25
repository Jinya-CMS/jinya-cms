// TODO: Change to new ajax handler helpers
use anyhow::Error;
use http::Response;
use yew::Callback;
use yew::format::Json;
use yew::services::fetch::{FetchService, FetchTask};

use crate::ajax::{AjaxError, delete_request, get_error_from_parts, get_host, get_request, post_request_with_body, put_request_with_body};
use crate::models::gallery::{Gallery, GalleryType, Orientation};
use crate::models::list_model::ListModel;

pub struct GalleryService {}

impl GalleryService {
    pub fn new() -> GalleryService {
        GalleryService {}
    }

    pub fn get_list(&self, keyword: String, callback: Callback<Result<ListModel<Gallery>, AjaxError>>) -> FetchTask {
        let url = format!("{}/api/media/gallery?keyword={}", get_host(), keyword);
        let request = get_request(url);
        let handler = move |response: Response<Json<Result<ListModel<Gallery>, Error>>>| {
            let (meta, Json(data)) = response.into_parts();
            if meta.status.is_success() {
                callback.emit(Ok(data.unwrap()));
            } else {
                callback.emit(get_error_from_parts(meta));
            }
        };

        FetchService::fetch(request, handler.into()).unwrap()
    }

    pub fn add_gallery(&self, name: String, description: String, orientation: Orientation, gallery_type: GalleryType, callback: Callback<Result<Gallery, AjaxError>>) -> FetchTask {
        let url = format!("{}/api/media/gallery", get_host());
        let gallery = Gallery::from_all_fields(name, description, orientation, gallery_type);
        let request = post_request_with_body(url, &gallery);
        let handler = move |response: Response<Json<Result<Gallery, Error>>>| {
            let (meta, Json(data)) = response.into_parts();
            if meta.status.is_success() {
                callback.emit(Ok(data.unwrap()));
            } else {
                callback.emit(get_error_from_parts(meta));
            }
        };

        FetchService::fetch(request, handler.into()).unwrap()
    }

    pub fn delete_gallery(&self, gallery: Gallery, callback: Callback<Result<bool, AjaxError>>) -> FetchTask {
        let url = format!("{}/api/media/gallery/{}", get_host(), gallery.id);
        let request = delete_request(url);
        let handler = move |response: Response<Json<Result<(), Error>>>| {
            let (meta, Json(_)) = response.into_parts();
            if meta.status.is_success() {
                callback.emit(Ok(true))
            } else {
                callback.emit(get_error_from_parts(meta));
            }
        };

        FetchService::fetch(request, handler.into()).unwrap()
    }

    pub fn update_gallery(&self, gallery: Gallery, callback: Callback<Result<bool, AjaxError>>) -> FetchTask {
        let url = format!("{}/api/media/gallery/{}", get_host(), gallery.id);
        let request = put_request_with_body(url, &gallery);
        let handler = move |response: Response<Json<Result<(), Error>>>| {
            let (meta, Json(_)) = response.into_parts();
            if meta.status.is_success() {
                callback.emit(Ok(true))
            } else {
                callback.emit(get_error_from_parts(meta));
            }
        };

        FetchService::fetch(request, handler.into()).unwrap()
    }
}