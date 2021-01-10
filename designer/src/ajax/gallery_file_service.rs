// TODO: Change to new ajax handler helpers
use anyhow::Error;
use http::Response;
use serde_derive::*;
use yew::Callback;
use yew::format::Json;
use yew::services::fetch::{FetchService, FetchTask};

use crate::ajax::{AjaxError, delete_request, get_error_from_parts, get_host, get_request, post_request_with_body, put_request_with_body};
use crate::models::gallery_file::GalleryFile;

pub struct GalleryFileService {}

impl GalleryFileService {
    pub fn new() -> GalleryFileService {
        GalleryFileService {}
    }

    pub fn get_positions(&self, gallery: usize, callback: Callback<Result<Vec<GalleryFile>, AjaxError>>) -> FetchTask {
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

    pub fn delete_position(&self, gallery: usize, position: usize, callback: Callback<Result<usize, AjaxError>>) -> FetchTask {
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

    pub fn create_position(&self, gallery: usize, file: usize, position: usize, callback: Callback<Result<GalleryFile, AjaxError>>) -> FetchTask {
        let url = format!("{}/api/media/gallery/{}/file", get_host(), gallery);

        #[derive(Deserialize, Serialize)]
        struct RequestData {
            position: usize,
            file: usize,
        }

        let body = RequestData {
            file,
            position,
        };

        let request = post_request_with_body(url, &body);
        let handler = move |response: Response<Json<Result<GalleryFile, Error>>>| {
            let (meta, Json(gallery_file)) = response.into_parts();
            if meta.status.is_success() {
                callback.emit(Ok(gallery_file.unwrap()))
            } else {
                callback.emit(get_error_from_parts(meta));
            }
        };

        FetchService::fetch(request, handler.into()).unwrap()
    }

    pub fn update_position(&self, gallery: usize, old_position: usize, new_position: usize, callback: Callback<Result<bool, AjaxError>>) -> FetchTask {
        let url = format!("{}/api/media/gallery/{}/file/{}", get_host(), gallery, old_position);

        #[derive(Deserialize, Serialize)]
        #[serde(rename_all = "camelCase")]
        struct RequestData {
            new_position: usize,
        }

        let body = RequestData {
            new_position,
        };

        let request = put_request_with_body(url, &body);
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