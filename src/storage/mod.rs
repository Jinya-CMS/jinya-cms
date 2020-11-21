use anyhow::Error;
use yew::format::Json;
use yew::services::storage::Area;
use yew::services::StorageService;

pub struct AuthenticationStorage {}

impl AuthenticationStorage {
    fn get_api_key_storage_key() -> &'static str {
        "/jinya/api/key"
    }
    fn get_device_code_storage_key() -> &'static str {
        "/jinya/device/code"
    }
    fn get_username_storage_key() -> &'static str {
        "/jinya/login/username"
    }
    fn get_password_storage_key() -> &'static str {
        "/jinya/login/password"
    }
    fn get_roles_storage_key() -> &'static str {
        "/jinya/user/roles"
    }

    pub fn get_api_key() -> Option<String> {
        let storage = StorageService::new(Area::Local).unwrap();
        if let Json(Ok(data)) = storage.restore::<Json<Result<String, Error>>>(AuthenticationStorage::get_api_key_storage_key()) {
            Some(data)
        } else {
            None
        }
    }

    pub fn set_api_key(api_key: &str) {
        let mut storage = StorageService::new(Area::Local).unwrap();
        storage.store(AuthenticationStorage::get_api_key_storage_key(), Json(&api_key.to_string()));
    }

    pub fn get_device_code() -> Option<String> {
        let storage = StorageService::new(Area::Local).unwrap();
        if let Json(Ok(data)) = storage.restore::<Json<Result<String, Error>>>(AuthenticationStorage::get_device_code_storage_key()) {
            Some(data)
        } else {
            None
        }
    }

    pub fn set_device_code(device_code: &str) {
        let mut storage = StorageService::new(Area::Local).unwrap();
        storage.store(AuthenticationStorage::get_device_code_storage_key(), Json(&device_code.to_string()));
    }

    pub fn set_username(username: &str) {
        let mut storage = StorageService::new(Area::Session).unwrap();
        storage.store(AuthenticationStorage::get_username_storage_key(), Json(&username.to_string()));
    }

    pub fn set_password(password: &str) {
        let mut storage = StorageService::new(Area::Session).unwrap();
        storage.store(AuthenticationStorage::get_password_storage_key(), Json(&password.to_string()));
    }

    pub fn clear_user_auth() {
        let mut storage = StorageService::new(Area::Session).unwrap();
        storage.remove(AuthenticationStorage::get_username_storage_key());
        storage.remove(AuthenticationStorage::get_password_storage_key());
    }

    pub fn get_username() -> Option<String> {
        let storage = StorageService::new(Area::Session).unwrap();
        if let Json(Ok(data)) = storage.restore::<Json<Result<String, Error>>>(AuthenticationStorage::get_username_storage_key()) {
            Some(data)
        } else {
            None
        }
    }

    pub fn get_password() -> Option<String> {
        let storage = StorageService::new(Area::Session).unwrap();
        if let Json(Ok(data)) = storage.restore::<Json<Result<String, Error>>>(AuthenticationStorage::get_password_storage_key()) {
            Some(data)
        } else {
            None
        }
    }

    pub fn set_roles(roles: Vec<String>) {
        let mut storage = StorageService::new(Area::Local).unwrap();
        storage.store(AuthenticationStorage::get_roles_storage_key(), Json(&roles));
    }

    #[allow(dead_code)]
    pub fn get_roles() -> Option<Vec<String>> {
        let storage = StorageService::new(Area::Local).unwrap();
        if let Json(Ok(data)) = storage.restore::<Json<Result<Vec<String>, Error>>>(AuthenticationStorage::get_roles_storage_key()) {
            Some(data)
        } else {
            None
        }
    }

    pub fn clear_device_code() {
        let mut storage = StorageService::new(Area::Local).unwrap();
        storage.remove(AuthenticationStorage::get_device_code_storage_key())
    }

    pub fn clear_api_key() {
        let mut storage = StorageService::new(Area::Local).unwrap();
        storage.remove(AuthenticationStorage::get_api_key_storage_key())
    }

    pub fn clear_roles() {
        let mut storage = StorageService::new(Area::Local).unwrap();
        storage.remove(AuthenticationStorage::get_roles_storage_key())
    }
}