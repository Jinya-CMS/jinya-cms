use anyhow::Error;
use http::Response;
use yew::Callback;
use yew::format::Json;
use yew::services::fetch::FetchTask;
use yew::services::FetchService;

use crate::ajax::{AjaxError, delete_request, get_error_from_parts, get_host, get_request, put_request_with_body, post_request_with_body};
use crate::models::list_model::ListModel;
use crate::models::simple_page::SimplePage;

pub struct SimplePageService {}

impl SimplePageService {
    pub fn new() -> SimplePageService {
        SimplePageService {}
    }

    pub fn get_list(&self, keyword: String, callback: Callback<Result<ListModel<SimplePage>, AjaxError>>) -> FetchTask {
        let url = format!("{}/api/page?keyword={}", get_host(), keyword);
        let request = get_request(url);
        let handler = move |response: Response<Json<Result<ListModel<SimplePage>, Error>>>| {
            let (meta, Json(data)) = response.into_parts();
            if meta.status.is_success() {
                callback.emit(Ok(data.unwrap()));
            } else {
                callback.emit(get_error_from_parts(meta));
            }
        };

        FetchService::fetch(request, handler.into()).unwrap()
    }

    pub fn delete_page(&self, page: SimplePage, callback: Callback<Result<bool, AjaxError>>) -> FetchTask {
        let url = format!("{}/api/page/{}", get_host(), page.id);
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

    pub fn get_page(&self, id: usize, callback: Callback<Result<SimplePage, AjaxError>>) -> FetchTask {
        let url = format!("{}/api/page/{}", get_host(), id);
        let request = get_request(url);
        let handler = move |response: Response<Json<Result<SimplePage, Error>>>| {
            let (meta, Json(page)) = response.into_parts();
            if meta.status.is_success() {
                callback.emit(Ok(page.unwrap()));
            } else {
                callback.emit(get_error_from_parts(meta));
            }
        };

        FetchService::fetch(request, handler.into()).unwrap()
    }

    pub fn update_page(&self, id: usize, page: SimplePage, callback: Callback<Result<bool, AjaxError>>) -> FetchTask {
        let url = format!("{}/api/page/{}", get_host(), id);
        let request = put_request_with_body(url, &page);
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

    pub fn create_page(&self, page: SimplePage, callback: Callback<Result<bool, AjaxError>>) -> FetchTask {
        let url = format!("{}/api/page", get_host());
        let request = post_request_with_body(url, &page);
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
