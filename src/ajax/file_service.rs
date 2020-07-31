use anyhow::Error;
use http::Response;
use yew::Callback;
use yew::format::Json;
use yew::services::fetch::{FetchService, FetchTask};
use yew::services::reader::{FileData};

use crate::ajax::{delete_request, get_error_from_parts, get_host, get_request, put_request, put_request_with_binary_body, put_request_with_body, AjaxError};
use crate::models::file::File;
use crate::models::list_model::ListModel;

#[derive(Default)]
pub struct FileService {}

impl FileService {
    pub fn new() -> FileService { FileService {} }

    pub fn get_list(&self, keyword: String, callback: Callback<Result<ListModel<File>, AjaxError>>) -> FetchTask {
        let url = format!("{}/api/media/file?keyword={}", get_host(), keyword);
        let request = get_request(url);
        let handler = move |response: Response<Json<Result<ListModel<File>, Error>>>| {
            let (meta, Json(data)) = response.into_parts();
            if meta.status.is_success() {
                callback.emit(Ok(data.unwrap()));
            } else {
                callback.emit(get_error_from_parts(meta));
            }
        };

        FetchService::fetch(request, handler.into()).unwrap()
    }

    pub fn update_file(&self, file: &File, callback: Callback<Result<bool, AjaxError>>) -> FetchTask {
        let url = format!("{}/api/media/file/{}", get_host(), file.id);
        let request = put_request_with_body(url, file);
        let handler = move |response: Response<Json<Result<(), Error>>>| {
            let (meta, Json(_)) = response.into_parts();
            if meta.status.is_success() {
                callback.emit(Ok(true));
            } else {
                callback.emit(get_error_from_parts(meta));
            }
        };

        FetchService::fetch(request, handler.into()).unwrap()
    }

    pub fn start_file_upload(&self, file: &File, callback: Callback<Result<bool, AjaxError>>) -> FetchTask {
        let url = format!("{}/api/media/file/{}/content", get_host(), file.id);
        let request = put_request(url);
        let handler = move |response: Response<Json<Result<(), Error>>>| {
            let (meta, Json(_)) = response.into_parts();
            if meta.status.is_success() {
                callback.emit(Ok(true));
            } else {
                callback.emit(get_error_from_parts(meta));
            }
        };

        FetchService::fetch(request, handler.into()).unwrap()
    }

    pub fn finish_file_upload(&self, file: &File, callback: Callback<Result<bool, AjaxError>>) -> FetchTask {
        let url = format!("{}/api/media/file/{}/content/finish", get_host(), file.id);
        let request = put_request(url);
        let handler = move |response: Response<Json<Result<(), Error>>>| {
            let (meta, Json(_)) = response.into_parts();
            if meta.status.is_success() {
                callback.emit(Ok(true));
            } else {
                callback.emit(get_error_from_parts(meta));
            }
        };

        FetchService::fetch(request, handler.into()).unwrap()
    }

    pub fn upload_chunk(&self, file: &File, file_data: FileData, callback: Callback<Result<bool, AjaxError>>) -> FetchTask {
        let url = format!("{}/api/media/file/{}/content/0", get_host(), file.id);
        let request = put_request_with_binary_body(url, Ok(file_data.clone().content));
        let handler = move |response: Response<Json<Result<(), Error>>>| {
            let (meta, Json(_)) = response.into_parts();
            if meta.status.is_success() {
                callback.emit(Ok(true));
            } else {
                callback.emit(get_error_from_parts(meta));
            }
        };

        FetchService::fetch_binary(request, handler.into()).unwrap()
    }

    pub fn delete_file(&self, file: &File, callback: Callback<Result<bool, AjaxError>>) -> FetchTask {
        let url = format!("{}/api/media/file/{}", get_host(), file.id);
        let request = delete_request(url);
        let handler = move |response: Response<Json<Result<(), Error>>>| {
            let (meta, Json(_)) = response.into_parts();
            if meta.status.is_success() {
                callback.emit(Ok(true));
            } else {
                callback.emit(get_error_from_parts(meta));
            }
        };

        FetchService::fetch(request, handler.into()).unwrap()
    }
}