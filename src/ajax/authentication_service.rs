use anyhow::Error;
use http::{Response, StatusCode};
use yew::Callback;
use yew::format::{Json, Nothing};
use yew::services::fetch::FetchTask;
use yew::services::FetchService;

#[derive(Default)]
pub struct AuthenticationService {}

impl AuthenticationService {
    pub fn new() -> AuthenticationService {
        AuthenticationService {}
    }

    pub fn check_api_key(&mut self, callback: Callback<bool>) -> FetchTask {
        let url = format!("{}/api/login", super::get_host());
        let handler = move |response: Response<Json<Result<(), Error>>>| {
            callback.emit(response.status() == StatusCode::NO_CONTENT)
        };

        let request = http::Request::head(url).body(Nothing).unwrap();
        FetchService::fetch(request, handler.into()).unwrap()
    }
}