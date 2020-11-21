use anyhow::Error;
use http::Response;
use serde_derive::*;
use serde_json::map::Map;
use serde_json::Value;
use wasm_bindgen::__rt::std::collections::BTreeMap;
use yew::Callback;
use yew::format::Json;
use yew::services::fetch::{FetchService, FetchTask};

use crate::ajax::{AjaxError, bool_handler, get_error_from_parts, get_host, get_request, put_request, put_request_with_body};
use crate::models::list_model::ListModel;
use crate::models::segment_page::SegmentPage;
use crate::models::theme::{Theme, ThemeConfigurationStructure};
use crate::models::simple_page::SimplePage;
use crate::models::file::File;
use crate::models::gallery::Gallery;
use crate::models::menu::Menu;

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

    pub fn get_variables(&self, id: usize, callback: Callback<Result<Map<String, Value>, AjaxError>>) -> FetchTask {
        let url = format!("{}/api/theme/{}/styling", get_host(), id);
        let handler = move |response: Response<Json<Result<Map<String, Value>, Error>>>| {
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

    pub fn save_variables(&self, id: usize, variables: Map<String, Value>, callback: Callback<Result<bool, AjaxError>>) -> FetchTask {
        let url = format!("{}/api/theme/{}/styling", get_host(), id);
        let handler = bool_handler(callback);
        #[derive(Deserialize, Serialize, Clone, PartialEq)]
        struct RequestData {
            pub variables: Map<String, Value>,
        }
        let data = RequestData { variables };
        let request = put_request_with_body(url, &data);

        FetchService::fetch(request, handler.into()).unwrap()
    }

    pub fn get_theme(&self, id: usize, callback: Callback<Result<Theme, AjaxError>>) -> FetchTask {
        let url = format!("{}/api/theme/{}", get_host(), id);
        let handler = move |response: Response<Json<Result<Theme, Error>>>| {
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

    pub fn get_configuration_structure(&self, id: usize, callback: Callback<Result<ThemeConfigurationStructure, AjaxError>>) -> FetchTask {
        let url = format!("{}/api/theme/{}/configuration/structure", get_host(), id);
        let handler = move |response: Response<Json<Result<ThemeConfigurationStructure, Error>>>| {
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

    pub fn save_segment_page_link(&self, id: usize, name: String, segment_page: usize, callback: Callback<Result<bool, AjaxError>>) -> FetchTask {
        let url = format!("{}/api/theme/{}/segment-page/{}", get_host(), id, name);
        let handler = bool_handler(callback);
        #[derive(Deserialize, Serialize, Clone, PartialEq)]
        #[serde(rename_all = "camelCase")]
        struct RequestData {
            pub segment_page: usize,
        }
        let data = RequestData { segment_page };
        let request = put_request_with_body(url, &data);

        FetchService::fetch(request, handler.into()).unwrap()
    }

    pub fn get_theme_segment_pages(&self, id: usize, callback: Callback<Result<BTreeMap<String, SegmentPage>, AjaxError>>) -> FetchTask {
        let url = format!("{}/api/theme/{}/segment-page", get_host(), id);
        let handler = move |response: Response<Json<Result<BTreeMap<String, SegmentPage>, Error>>>| {
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

    pub fn save_page_link(&self, id: usize, name: String, page: usize, callback: Callback<Result<bool, AjaxError>>) -> FetchTask {
        let url = format!("{}/api/theme/{}/page/{}", get_host(), id, name);
        let handler = bool_handler(callback);
        #[derive(Deserialize, Serialize, Clone, PartialEq)]
        #[serde(rename_all = "camelCase")]
        struct RequestData {
            pub page: usize,
        }
        let data = RequestData { page };
        let request = put_request_with_body(url, &data);

        FetchService::fetch(request, handler.into()).unwrap()
    }

    pub fn get_theme_pages(&self, id: usize, callback: Callback<Result<BTreeMap<String, SimplePage>, AjaxError>>) -> FetchTask {
        let url = format!("{}/api/theme/{}/page", get_host(), id);
        let handler = move |response: Response<Json<Result<BTreeMap<String, SimplePage>, Error>>>| {
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

    pub fn save_file_link(&self, id: usize, name: String, file: usize, callback: Callback<Result<bool, AjaxError>>) -> FetchTask {
        let url = format!("{}/api/theme/{}/file/{}", get_host(), id, name);
        let handler = bool_handler(callback);
        #[derive(Deserialize, Serialize, Clone, PartialEq)]
        #[serde(rename_all = "camelCase")]
        struct RequestData {
            pub file: usize,
        }
        let data = RequestData { file };
        let request = put_request_with_body(url, &data);

        FetchService::fetch(request, handler.into()).unwrap()
    }

    pub fn get_theme_files(&self, id: usize, callback: Callback<Result<BTreeMap<String, File>, AjaxError>>) -> FetchTask {
        let url = format!("{}/api/theme/{}/file", get_host(), id);
        let handler = move |response: Response<Json<Result<BTreeMap<String, File>, Error>>>| {
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

    pub fn save_menu_link(&self, id: usize, name: String, menu: usize, callback: Callback<Result<bool, AjaxError>>) -> FetchTask {
        let url = format!("{}/api/theme/{}/menu/{}", get_host(), id, name);
        let handler = bool_handler(callback);
        #[derive(Deserialize, Serialize, Clone, PartialEq)]
        #[serde(rename_all = "camelCase")]
        struct RequestData {
            pub menu: usize,
        }
        let data = RequestData { menu };
        let request = put_request_with_body(url, &data);

        FetchService::fetch(request, handler.into()).unwrap()
    }

    pub fn get_theme_menus(&self, id: usize, callback: Callback<Result<BTreeMap<String, Menu>, AjaxError>>) -> FetchTask {
        let url = format!("{}/api/theme/{}/menu", get_host(), id);
        let handler = move |response: Response<Json<Result<BTreeMap<String, Menu>, Error>>>| {
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

    pub fn save_gallery_link(&self, id: usize, name: String, gallery: usize, callback: Callback<Result<bool, AjaxError>>) -> FetchTask {
        let url = format!("{}/api/theme/{}/gallery/{}", get_host(), id, name);
        let handler = bool_handler(callback);
        #[derive(Deserialize, Serialize, Clone, PartialEq)]
        #[serde(rename_all = "camelCase")]
        struct RequestData {
            pub gallery: usize,
        }
        let data = RequestData { gallery };
        let request = put_request_with_body(url, &data);

        FetchService::fetch(request, handler.into()).unwrap()
    }

    pub fn get_theme_galleries(&self, id: usize, callback: Callback<Result<BTreeMap<String, Gallery>, AjaxError>>) -> FetchTask {
        let url = format!("{}/api/theme/{}/gallery", get_host(), id);
        let handler = move |response: Response<Json<Result<BTreeMap<String, Gallery>, Error>>>| {
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
}