use anyhow::Error;
use yew::format::Json;
use yew::services::storage::Area;
use yew::services::StorageService;

pub struct AuthenticationStorage {}

impl AuthenticationStorage {
    fn get_jinya_api_key_storage_key() -> &'static str {
        "/jinya/api/key"
    }

    pub fn get_jinya_api_key() -> Option<String> {
        let storage = StorageService::new(Area::Local).unwrap();
        if let Json(Ok(data)) = storage.restore::<Json<Result<String, Error>>>(AuthenticationStorage::get_jinya_api_key_storage_key()) {
            Some(data)
        } else {
            None
        }
    }
}