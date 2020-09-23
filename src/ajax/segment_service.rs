use anyhow::Error;
use yew::format::Json;
use yew::prelude::*;
use yew::services::fetch::*;

use crate::ajax::{AjaxError, get_error_from_parts, get_host, get_request};
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
}