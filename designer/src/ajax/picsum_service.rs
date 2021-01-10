// TODO: Change to new ajax handler helpers
use anyhow::Error;
use http::Response;
use serde_derive::{Deserialize, Serialize};
use yew::Callback;
use yew::format::{Json, Nothing};
use yew::services::fetch::FetchTask;
use yew::services::FetchService;

#[derive(Deserialize, Serialize)]
pub struct PicsumMetaData {
    pub id: String,
    pub author: String,
    pub width: i32,
    pub height: i32,
    pub url: String,
    pub download_url: String,
}

pub struct PicsumService {}

impl PicsumService {
    pub fn new() -> PicsumService {
        PicsumService {}
    }

    pub fn get_picsum_id(&self, seed: &str, callback: Callback<String>) -> FetchTask {
        let url = format!("https://picsum.photos/seed/{}/2560/1440", seed);
        let request = http::Request::get(url).body(Nothing).unwrap();
        let handler = move |response: Response<Json<Result<String, Error>>>| {
            if let Some(picsum_id) = response.headers().get("picsum-id") {
                callback.emit(picsum_id.to_str().unwrap_or_default().to_string());
            }
        };

        FetchService::fetch(request, handler.into()).unwrap()
    }

    pub fn fetch_picsum_metadata(&self, picsum_id: String, callback: Callback<Result<PicsumMetaData, Error>>) -> FetchTask {
        let url = format!("https://picsum.photos/id/{}/info", picsum_id);
        let request = http::Request::get(url).body(Nothing).unwrap();
        let handler = move |response: Response<Json<Result<PicsumMetaData, Error>>>| {
            let (_, Json(data)) = response.into_parts();
            callback.emit(data)
        };

        FetchService::fetch(request, handler.into()).unwrap()
    }
}