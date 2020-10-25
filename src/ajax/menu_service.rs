use anyhow::Error;
use http::Response;
use serde_derive::*;
use yew::Callback;
use yew::format::Json;
use yew::services::fetch::{FetchService, FetchTask};

use crate::ajax::{AjaxError, bool_handler, delete_request, get_error_from_parts, get_host, get_request, post_request_with_body, put_request_with_body, delete};
use crate::models::list_model::ListModel;
use crate::models::menu::Menu;

pub struct MenuService {}

impl MenuService {
    pub fn new() -> MenuService {
        MenuService {}
    }

    pub fn get_list(&self, keyword: String, callback: Callback<Result<ListModel<Menu>, AjaxError>>) -> FetchTask {
        let url = format!("{}/api/menu?keyword={}", get_host(), keyword);
        let request = get_request(url);
        let handler = move |response: Response<Json<Result<ListModel<Menu>, Error>>>| {
            let (meta, Json(data)) = response.into_parts();
            if meta.status.is_success() {
                callback.emit(Ok(data.unwrap()));
            } else {
                callback.emit(get_error_from_parts(meta));
            }
        };

        FetchService::fetch(request, handler.into()).unwrap()
    }

    pub fn add_menu(&self, name: String, logo: Option<usize>, callback: Callback<Result<bool, AjaxError>>) -> FetchTask {
        let url = format!("{}/api/menu", get_host());
        #[derive(Serialize, Deserialize, Clone, PartialEq)]
        struct Body {
            pub logo: Option<usize>,
            pub name: String,
        }
        let menu = Body {
            logo,
            name,
        };
        let request = post_request_with_body(url, &menu);

        FetchService::fetch(request, bool_handler(callback).into()).unwrap()
    }

    pub fn delete_menu(&self, id: usize, callback: Callback<Result<bool, AjaxError>>) -> FetchTask {
        let url = format!("{}/api/menu/{}", get_host(), id);

        delete(url, callback)
    }

    pub fn update_menu(&self, id: usize, name: String, logo: Option<usize>, callback: Callback<Result<bool, AjaxError>>) -> FetchTask {
        let url = format!("{}/api/menu/{}", get_host(), id);
        #[derive(Serialize, Deserialize, Clone, PartialEq)]
        struct Body {
            pub logo: Option<usize>,
            pub name: String,
        }
        let menu = Body {
            logo,
            name,
        };
        let request = put_request_with_body(url, &menu);

        FetchService::fetch(request, bool_handler(callback).into()).unwrap()
    }
}