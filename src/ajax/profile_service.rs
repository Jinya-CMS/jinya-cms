use anyhow::Error;
use serde_derive::{Deserialize, Serialize};
use yew::Callback;
use yew::format::Json;
use yew::services::fetch::*;

use crate::ajax::{AjaxError, get_error_from_parts, get_host, get_request, put_request_with_binary_body, put_request_with_body};
use crate::models::artist::Artist;

pub struct ProfileService {}

impl ProfileService {
    pub fn new() -> ProfileService {
        ProfileService {}
    }

    pub fn get_profile(&self, callback: Callback<Result<Artist, AjaxError>>) -> FetchTask {
        let url = format!("{}/api/me", get_host());
        let request = get_request(url);
        let handler = move |response: Response<Json<Result<Artist, Error>>>| {
            let (meta, Json(data)) = response.into_parts();
            if meta.status.is_success() {
                callback.emit(Ok(data.unwrap()));
            } else {
                callback.emit(get_error_from_parts(meta));
            }
        };

        FetchService::fetch(request, handler.into()).unwrap()
    }

    pub fn put_profile(&self, artist_name: String, email: String, about_me: String, callback: Callback<Result<bool, AjaxError>>) -> FetchTask {
        let url = format!("{}/api/me", get_host());

        #[derive(Serialize, Deserialize, Clone, PartialEq)]
        #[serde(rename_all = "camelCase")]
        struct Body {
            pub artist_name: String,
            pub email: String,
            pub about_me: String,
        }

        let body = Body {
            artist_name,
            email,
            about_me,
        };

        let request = put_request_with_body(url, &body);
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

    pub fn upload_profile_picture(&self, data: Vec<u8>, callback: Callback<Result<bool, AjaxError>>) -> FetchTask {
        let url = format!("{}/api/me/profilepicture", get_host());
        let request = put_request_with_binary_body(url, Ok(data));
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
}