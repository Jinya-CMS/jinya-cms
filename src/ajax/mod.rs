use anyhow::Error;
use yew::format::Json;
use yew::services::storage::Area;
use yew::services::StorageService;

pub mod authentication_service;

fn get_host() -> String {
    let storage = StorageService::new(Area::Local).unwrap();
    if let Json(Ok(data)) = storage.restore::<Json<Result<String, Error>>>("/jinya/api/host") {
        data
    } else {
        "".to_string()
    }
}