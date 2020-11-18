use anyhow::Error;
use http::{Request, Response};
use yew::Callback;
use yew::format::Json;
use yew::services::fetch::{FetchService, FetchTask};

use crate::ajax::{AjaxError, bool_handler, get_error_from_parts, get_host, get_request, put_request};
use crate::models::list_model::ListModel;
use crate::models::theme::Theme;

pub struct ThemeService {}

impl ThemeService {
    pub fn new() -> ThemeService {
        ThemeService {}
    }

    pub fn get_list(&self, keyword: String, callback: Callback<Result<ListModel<Theme>, AjaxError>>) -> FetchTask {
        let url = format!("{}/api/theme?keyword={}", get_host(), keyword);
        let handler = move |response: Response<Json<Result<ListModel<Theme>, Error>>>| {
            let (meta, Json(data)) = response.into_parts();
            if meta.status.is_success() {
                callback.emit(Ok(data.unwrap()));
            } else {
                callback.emit(get_error_from_parts(meta));
            }
        };
        let request = get_request(url);

        FetchService::fetch(request, handler.into()).unwrap()
    }

    pub fn activate_theme(&self, id: usize, callback: Callback<Result<bool, AjaxError>>) -> FetchTask {
        let url = format!("{}/api/theme/{}/active", get_host(), id);
        let request = put_request(url);
        let handler = bool_handler(callback);

        FetchService::fetch(request, handler.into()).unwrap()
    }

    pub fn build_theme(&self, id: usize, callback: Callback<Result<bool, AjaxError>>) -> FetchTask {
        let url = format!("{}/api/theme/{}/assets", get_host(), id);
        let request = put_request(url);
        let handler = bool_handler(callback);

        FetchService::fetch(request, handler.into()).unwrap()
    }
}