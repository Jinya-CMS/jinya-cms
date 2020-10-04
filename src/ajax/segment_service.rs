use anyhow::Error;
use serde::*;
use yew::format::Json;
use yew::prelude::*;
use yew::services::fetch::*;

use crate::ajax::{AjaxError, delete_request, get_error_from_parts, get_host, get_request, put_request_with_body};
use crate::models::segment::Segment;

pub struct SegmentService {}

impl SegmentService {
    pub fn new() -> SegmentService {
        SegmentService {}
    }

    pub fn get_segments(&self, segment_page_id: usize, callback: Callback<Result<Vec<Segment>, AjaxError>>) -> FetchTask {
        let url = format!("{}/api/segment-page/{}/segment", get_host(), segment_page_id);
        let request = get_request(url);
        let handler = move |response: Response<Json<Result<Vec<Segment>, Error>>>| {
            let (meta, Json(data)) = response.into_parts();
            if meta.status.is_success() {
                callback.emit(Ok(data.unwrap()));
            } else {
                callback.emit(get_error_from_parts(meta));
            }
        };

        FetchService::fetch(request, handler.into()).unwrap()
    }

    pub fn delete_segment(&self, segment_page_id: usize, position: usize, callback: Callback<Result<bool, AjaxError>>) -> FetchTask {
        let url = format!("{}/api/segment-page/{}/segment/{}", get_host(), segment_page_id, position);
        let request = delete_request(url);
        let handler = move |response: Response<Json<Result<Vec<Segment>, Error>>>| {
            let (meta, Json(_)) = response.into_parts();
            if meta.status.is_success() {
                callback.emit(Ok(true));
            } else {
                callback.emit(get_error_from_parts(meta));
            }
        };

        FetchService::fetch(request, handler.into()).unwrap()
    }

    pub fn update_gallery_segment(&self, segment_page_id: usize, position: usize, gallery_id: usize, callback: Callback<Result<bool, AjaxError>>) -> FetchTask {
        let url = format!("{}/api/segment-page/{}/segment/{}", get_host(), segment_page_id, position);

        #[derive(Serialize, Deserialize, Clone, PartialEq)]
        struct Body {
            pub gallery: usize,
        }

        let request_body = Body { gallery: gallery_id };
        let request = put_request_with_body(url, &request_body);
        let handler = move |response: Response<Json<Result<Vec<Segment>, Error>>>| {
            let (meta, Json(_)) = response.into_parts();
            if meta.status.is_success() {
                callback.emit(Ok(true));
            } else {
                callback.emit(get_error_from_parts(meta));
            }
        };

        FetchService::fetch(request, handler.into()).unwrap()
    }

    pub fn update_file_segment(&self, segment_page_id: usize, position: usize, file_id: usize, action: bool, target: String, callback: Callback<Result<bool, AjaxError>>) -> FetchTask {
        let url = format!("{}/api/segment-page/{}/segment/{}", get_host(), segment_page_id, position);

        #[derive(Serialize, Deserialize, Clone, PartialEq)]
        struct Body {
            pub file: usize,
            pub action: String,
            pub target: String,
        }

        let request_body = if action {
            Body { file: file_id, action: "link".to_string(), target }
        } else {
            Body { file: file_id, action: "none".to_string(), target: "".to_string() }
        };
        let request = put_request_with_body(url, &request_body);
        let handler = move |response: Response<Json<Result<Vec<Segment>, Error>>>| {
            let (meta, Json(_)) = response.into_parts();
            if meta.status.is_success() {
                callback.emit(Ok(true));
            } else {
                callback.emit(get_error_from_parts(meta));
            }
        };

        FetchService::fetch(request, handler.into()).unwrap()
    }

    pub fn update_html_segment(&self, segment_page_id: usize, position: usize, content: String, callback: Callback<Result<bool, AjaxError>>) -> FetchTask {
        let url = format!("{}/api/segment-page/{}/segment/{}", get_host(), segment_page_id, position);

        #[derive(Serialize, Deserialize, Clone, PartialEq)]
        struct Body {
            pub html: String,
        }

        let request_body = Body { html: content };
        let request = put_request_with_body(url, &request_body);
        let handler = move |response: Response<Json<Result<Vec<Segment>, Error>>>| {
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