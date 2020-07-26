use anyhow::Error;
use http::Response;
use yew::Callback;
use yew::format::Json;
use yew::services::fetch::{FetchService, FetchTask};

use crate::ajax::{get_error_from_parts, get_host, get_request};
use crate::models::file::File;
use crate::models::list_model::ListModel;

#[derive(Default)]
pub struct FileService {}

impl FileService {
    pub fn new() -> FileService { FileService {} }

    pub fn get_list(&self, keyword: String, callback: Callback<Result<ListModel<File>, Error>>) -> FetchTask {
        let url = format!("{}/api/media/file?keyword={}", get_host(), keyword);
        let request = get_request(url);
        let handler = move |response: Response<Json<Result<ListModel<File>, Error>>>| {
            let (meta, Json(data)) = response.into_parts();
            if meta.status.is_success() {
                callback.emit(data);
            } else {
                callback.emit(get_error_from_parts(meta));
            }
        };

        FetchService::fetch(request, handler.into()).unwrap()
    }
}