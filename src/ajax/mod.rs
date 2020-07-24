use anyhow::Error;
use http::Request;
use yew::format::Json;
use yew::services::storage::Area;
use yew::services::StorageService;

use crate::storage::AuthenticationStorage;

pub mod picsum_service;
pub mod authentication_service;

fn get_host() -> String {
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