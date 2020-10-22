use anyhow::Error;
use http::Response;
use serde_derive::{Deserialize, Serialize};
use yew::Callback;
use yew::format::Json;
use yew::services::fetch::{FetchService, FetchTask};

use crate::ajax::{AjaxError, delete_request, get_error_from_parts, get_host, get_request, post_request_with_body, put_request, put_request_with_body};
use crate::models::artist::Artist;
use crate::models::list_model::ListModel;

pub struct ArtistService {}

impl ArtistService {
    pub fn new() -> ArtistService {
        ArtistService {}
    }

    pub fn get_list(&self, keyword: String, callback: Callback<Result<ListModel<Artist>, AjaxError>>) -> FetchTask {
        let url = format!("{}/api/user?keyword={}", get_host(), keyword);
        let request = get_request(url);
        let handler = move |response: Response<Json<Result<ListModel<Artist>, Error>>>| {
            let (meta, Json(data)) = response.into_parts();
            if meta.status.is_success() {
                callback.emit(Ok(data.unwrap()));
            } else {
                callback.emit(get_error_from_parts(meta));
            }
        };

        FetchService::fetch(request, handler.into()).unwrap()
    }

    pub fn add_artist(&self, artist_name: String, email: String, roles: Vec<String>, callback: Callback<Result<Artist, AjaxError>>) -> FetchTask {
        let url = format!("{}/api/user", get_host());
        let artist = Artist::from_name_email_roles(artist_name, email, roles);
        let request = post_request_with_body(url, &artist);
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

    pub fn delete_artist(&self, id: usize, callback: Callback<Result<bool, AjaxError>>) -> FetchTask {
        let url = format!("{}/api/user/{}", get_host(), id);
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

    pub fn deactivate_artist(&self, id: usize, callback: Callback<Result<bool, AjaxError>>) -> FetchTask {
        let url = format!("{}/api/user/{}/activation", get_host(), id);
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

    pub fn activate_artist(&self, id: usize, callback: Callback<Result<bool, AjaxError>>) -> FetchTask {
        let url = format!("{}/api/user/{}/activation", get_host(), id);
        let request = put_request(url);
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

    pub fn update_artist(&self, id: usize, artist_name: String, email: String, roles: Vec<String>, callback: Callback<Result<bool, AjaxError>>) -> FetchTask {
        let url = format!("{}/api/user/{}", get_host(), id);
        #[derive(Serialize, Deserialize, Clone, PartialEq)]
        #[serde(rename_all = "camelCase")]
        struct Body {
            pub artist_name: String,
            pub email: String,
            pub roles: Vec<String>,
        }

        let body = Body { artist_name, email, roles };
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