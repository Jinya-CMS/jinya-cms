use anyhow::{anyhow, Error};
use http::{Request, Response, StatusCode};
use http::response::Parts;
use yew::Callback;
use yew::format::{Json, Nothing};
use yew::services::fetch::{FetchService, FetchTask};
use yew::services::storage::Area;
use yew::services::StorageService;

use crate::storage::AuthenticationStorage;

pub mod simple_page_service;
pub mod gallery_file_service;
pub mod gallery_service;
pub mod file_service;
pub mod picsum_service;
pub mod authentication_service;
pub mod segment_page_service;
pub mod segment_service;
pub mod profile_service;
pub mod artist_service;
pub mod menu_service;
pub mod menu_item_service;

pub fn get_host() -> String {
    let storage = StorageService::new(Area::Local).unwrap();
    if let Json(Ok(data)) = storage.restore::<Json<Result<String, Error>>>("/jinya/api/host") {
        data
    } else {
        "".to_string()
    }
}

fn get_api_key() -> String {
    let api_key_storage = AuthenticationStorage::get_api_key();
    if let Some(api_key) = api_key_storage {
        api_key
    } else {
        "".to_string()
    }
}

fn get_device_code() -> String {
    let device_code_storage = AuthenticationStorage::get_device_code();
    if let Some(device_code) = device_code_storage {
        device_code
    } else {
        "".to_string()
    }
}

fn post_request_with_body<T>(url: String, body: &T) -> Request<Json<&T>> {
    let api_key = get_api_key();
    let device_code = get_device_code();

    http::Request::post(url)
        .header("Content-Type", "application/json")
        .header("JinyaApiKey", api_key)
        .header("JinyaDeviceCode", device_code)
        .body(Json(body))
        .unwrap()
}

fn put_request_with_body<T>(url: String, body: &T) -> Request<Json<&T>> {
    let api_key = get_api_key();
    let device_code = get_device_code();

    http::Request::put(url)
        .header("Content-Type", "application/json")
        .header("JinyaApiKey", api_key)
        .header("JinyaDeviceCode", device_code)
        .body(Json(body))
        .unwrap()
}

fn head_request(url: String) -> Request<Nothing> {
    let api_key = get_api_key();
    let device_code = get_device_code();

    http::Request::head(url)
        .header("JinyaApiKey", api_key)
        .header("JinyaDeviceCode", device_code)
        .body(Nothing)
        .unwrap()
}

fn get_request(url: String) -> Request<Nothing> {
    let api_key = get_api_key();
    let device_code = get_device_code();

    http::Request::get(url)
        .header("JinyaApiKey", api_key)
        .header("JinyaDeviceCode", device_code)
        .body(Nothing)
        .unwrap()
}

fn delete_request(url: String) -> Request<Nothing> {
    let api_key = get_api_key();
    let device_code = get_device_code();

    http::Request::delete(url)
        .header("JinyaApiKey", api_key)
        .header("JinyaDeviceCode", device_code)
        .body(Nothing)
        .unwrap()
}

fn put_request(url: String) -> Request<Nothing> {
    let api_key = get_api_key();
    let device_code = get_device_code();

    http::Request::put(url)
        .header("JinyaApiKey", api_key)
        .header("JinyaDeviceCode", device_code)
        .body(Nothing)
        .unwrap()
}

fn put_request_with_binary_body<T>(url: String, body: T) -> Request<T> {
    let api_key = get_api_key();
    let device_code = get_device_code();

    http::Request::put(url)
        .header("JinyaApiKey", api_key)
        .header("JinyaDeviceCode", device_code)
        .body(body)
        .unwrap()
}

fn get_error_from_parts<T>(parts: Parts) -> Result<T, AjaxError> {
    let error = match parts.status {
        StatusCode::BAD_REQUEST => anyhow!("Fields are missing"),
        StatusCode::NOT_FOUND => anyhow!("Resource is not found"),
        StatusCode::CONFLICT => anyhow!("Resource is in conflict with existing data"),
        StatusCode::FORBIDDEN => anyhow!("The access is forbidden"),
        StatusCode::UNAUTHORIZED => anyhow!("The access is not authorized"),
        _ => anyhow!("Other unknown error"),
    };

    if parts.status != StatusCode::REQUEST_TIMEOUT {
        log::error!("Unexpected error: {}. Check server logs", parts.status);
        log::error!("{}", error.to_string());
    } else {
        log::error!("There is something wrong with the code, please contact developer@jinya.de");
    }

    Err(AjaxError::new(error, parts.status))
}

fn delete(url: String, callback: Callback<Result<bool, AjaxError>>) -> FetchTask {
    let request = delete_request(url);

    FetchService::fetch(request, bool_handler(callback).into()).unwrap()
}

fn bool_handler(callback: Callback<Result<bool, AjaxError>>) -> impl Fn(Response<Json<Result<(), Error>>>) {
    move |response: Response<Json<Result<(), Error>>>| {
        let (meta, Json(_)) = response.into_parts();
        if meta.status.is_success() {
            callback.emit(Ok(true));
        } else {
            callback.emit(get_error_from_parts(meta));
        }
    }
}

#[derive(Debug)]
pub struct AjaxError {
    pub error: Error,
    pub status_code: StatusCode,
}

impl AjaxError {
    pub fn new(error: Error, status_code: StatusCode) -> AjaxError {
        AjaxError {
            error,
            status_code,
        }
    }
}