use anyhow::Error;
use http::Response;
use yew::Callback;
use yew::format::Json;
use yew::services::fetch::{FetchService, FetchTask};

use crate::ajax::{AjaxError, delete_request, get_error_from_parts, get_host, get_request};
use crate::models::gallery_file::GalleryFile;

pub struct GalleryFileService {}

impl GalleryFileService {
    pub fn new() -> GalleryFileService {
        GalleryFileService {}
    }

    pub fn get_positions(&self, gallery: i32, callback: Callback<Result<Vec<GalleryFile>, AjaxError>>) -> FetchTask {
        let url = format!("{}/api/media/gallery/{}/file", get_host(), gallery);
        let request = get_request(url);
        let handler = move |response: Response<Json<Result<Vec<GalleryFile>, Error>>>| {
            let (meta, Json(data)) = response.into_parts();
            if meta.status.is_success() {
                callback.emit(Ok(data.unwrap()));
            } else {
                callback.emit(get_error_from_parts(meta));
            }
        };

        FetchService::fetch(request, handler.into()).unwrap()
    }

    pub fn delete_position(&self, gallery: i32, position: usize, callback: Callback<Result<usize, AjaxError>>) -> FetchTask {
        let url = format!("{}/api/media/gallery/{}/file/{}", get_host(), gallery, position);
        let request = delete_request(url);
        let handler = move |response: Response<Json<Result<(), Error>>>| {
            let (meta, Json(_)) = response.into_parts();
            if meta.status.is_success() {
                callback.emit(Ok(position))
            } else {
                callback.emit(get_error_from_parts(meta));
            }
        };

        FetchService::fetch(request, handler.into()).unwrap()
    }
}