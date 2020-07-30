use anyhow::{anyhow, Error};
use http::{Request, StatusCode};
use http::response::Parts;
use yew::format::{Json, Nothing};
use yew::services::{ConsoleService, StorageService};
use yew::services::storage::Area;

use crate::storage::AuthenticationStorage;

pub mod file_service;
pub mod picsum_service;
pub mod authentication_service;

pub fn get_host() -> String {
    let storage = StorageService::new(Area::Local).unwrap();
    if let Json(Ok(data)) = storage.restore::<Json<Result<String, Error>>>("/jinya/api/host") {
        data
    } else {
        "".to_string()
    }
}

fn post_request<T>(url: String, body: &T) -> Request<Json<&T>> {
    let api_key_storage = AuthenticationStorage::get_api_key();
    let device_code_storage = AuthenticationStorage::get_device_code();
    let api_key = if api_key_storage.is_some() {
        api_key_storage.unwrap()
    } else {
        "".to_string()
    };
    let device_code = if device_code_storage.is_some() {
        device_code_storage.unwrap()
    } else {
        "".to_string()
    };

    http::Request::post(url)
        .header("Content-Type", "application/json")
        .header("JinyaApiKey", api_key)
        .header("JinyaDeviceCode", device_code)
        .body(Json(body))
        .unwrap()
}

fn head_request(url: String) -> Request<Nothing> {
    let api_key_storage = AuthenticationStorage::get_api_key();
    let device_code_storage = AuthenticationStorage::get_device_code();
    let api_key = if api_key_storage.is_some() {
        api_key_storage.unwrap()
    } else {
        "".to_string()
    };
    let device_code = if device_code_storage.is_some() {
        device_code_storage.unwrap()
    } else {
        "".to_string()
    };

    http::Request::head(url)
        .header("JinyaApiKey", api_key)
        .header("JinyaDeviceCode", device_code)
        .body(Nothing)
        .unwrap()
}

fn get_request(url: String) -> Request<Nothing> {
    let api_key_storage = AuthenticationStorage::get_api_key();
    let device_code_storage = AuthenticationStorage::get_device_code();
    let api_key = if api_key_storage.is_some() {
        api_key_storage.unwrap()
    } else {
        "".to_string()
    };
    let device_code = if device_code_storage.is_some() {
        device_code_storage.unwrap()
    } else {
        "".to_string()
    };

    http::Request::get(url)
        .header("JinyaApiKey", api_key)
        .header("JinyaDeviceCode", device_code)
        .body(Nothing)
        .unwrap()
}

fn delete_request(url: String) -> Request<Nothing> {
    let api_key_storage = AuthenticationStorage::get_api_key();
    let device_code_storage = AuthenticationStorage::get_device_code();
    let api_key = if api_key_storage.is_some() {
        api_key_storage.unwrap()
    } else {
        "".to_string()
    };
    let device_code = if device_code_storage.is_some() {
        device_code_storage.unwrap()
    } else {
        "".to_string()
    };

    http::Request::delete(url)
        .header("JinyaApiKey", api_key)
        .header("JinyaDeviceCode", device_code)
        .body(Nothing)
        .unwrap()
}

fn get_error_from_parts<T>(parts: Parts) -> Result<T, Error> {
    let error = match parts.status {
        StatusCode::BAD_REQUEST => anyhow!("Fields are missing"),
        StatusCode::NOT_FOUND => anyhow!("Resource is not found"),
        StatusCode::CONFLICT => anyhow!("Resource is in conflict with existing data"),
        StatusCode::FORBIDDEN => anyhow!("The access is forbidden"),
        StatusCode::UNAUTHORIZED => anyhow!("The access is not authorized"),
        _ => anyhow!("Other unknown error"),
    };

    if parts.status==StatusCode::REQUEST_TIMEOUT {
        ConsoleService::error(format!("Unexpected error: {}. Check server logs", parts.status).as_str());

        ConsoleService::error(error.to_string().as_str());
    }

    Err(error)
}
