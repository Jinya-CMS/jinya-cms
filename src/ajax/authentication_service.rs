use anyhow::Error;
use http::{Response, StatusCode};
use serde_derive::{Deserialize, Serialize};
use yew::Callback;
use yew::format::Json;
use yew::services::fetch::{FetchService, FetchTask};

use crate::ajax::{head_request, post_request_with_body};
use crate::storage::AuthenticationStorage;

#[derive(Default)]
pub struct AuthenticationService {}

#[derive(Deserialize, Serialize)]
pub struct TwoFactorRequest {
    pub username: String,
    pub password: String,
}

#[derive(Deserialize, Serialize)]
#[serde(rename_all = "camelCase")]
pub struct LoginRequest {
    pub two_factor_code: String,
    pub username: String,
    pub password: String,
}

#[derive(Deserialize, Serialize)]
#[serde(rename_all = "camelCase")]
pub struct LoginResponse {
    pub api_key: String,
    pub device_code: String,
    pub roles: Vec<String>,
}

impl AuthenticationService {
    pub fn new() -> AuthenticationService {
        AuthenticationService {}
    }

    pub fn check_api_key(&mut self, callback: Callback<bool>) -> FetchTask {
        let url = format!("{}/api/login", super::get_host());
        let handler = move |response: Response<Json<Result<(), Error>>>| {
            callback.emit(response.status() == StatusCode::NO_CONTENT)
        };

        let request = head_request(url);
        FetchService::fetch(request, handler.into()).unwrap()
    }

    pub fn request_second_factor(&mut self, username: String, password: String, callback: Callback<bool>) -> FetchTask {
        let url = format!("{}/api/2fa", super::get_host());
        let handler = move |response: Response<Json<Result<(), Error>>>| {
            callback.emit(response.status() == StatusCode::NO_CONTENT)
        };

        let body = TwoFactorRequest { password, username };
        let request = post_request_with_body(url, &body);
        FetchService::fetch(request, handler.into()).unwrap()
    }

    pub fn login(&self, username: String, password: String, second_factor: Option<String>, callback: Callback<bool>) -> FetchTask {
        let url = format!("{}/api/login", super::get_host());
        let handler = move |response: Response<Json<Result<LoginResponse, Error>>>| {
            let (_, Json(data)) = response.into_parts();
            if data.is_ok() {
                let response = data.unwrap();
                AuthenticationStorage::set_api_key(&response.api_key);
                AuthenticationStorage::set_device_code(&response.device_code);
                AuthenticationStorage::set_roles(response.roles);
                callback.emit(true);
            } else {
                callback.emit(false);
            }
        };

        let body = LoginRequest {
            username,
            password,
            two_factor_code: second_factor.unwrap_or("".to_string()),
        };
        let request = post_request_with_body(url, &body);
        FetchService::fetch(request, handler.into()).unwrap()
    }
}
