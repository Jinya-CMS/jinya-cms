use anyhow::Error;
use http::Response;
use yew::Callback;
use yew::format::Json;
use yew::services::fetch::FetchTask;
use yew::services::FetchService;

use crate::ajax::{AjaxError, get_error_from_parts, get_host, put_request};

pub struct UpdateService {}

impl UpdateService {
    pub fn new()->UpdateService{
        UpdateService{}
    }

    pub fn start_update(&self, callback: Callback<Result<bool, AjaxError>>) -> FetchTask {
        let url = format!("{}/api/update", get_host());
        let request = put_request(url);
        let handler = move |response: Response<Json<Result<bool, Error>>>| {
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