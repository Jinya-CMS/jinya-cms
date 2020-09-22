use serde_derive::{Deserialize, Serialize};

use crate::models::segment_page::SegmentPage;

#[derive(Serialize, Deserialize, Clone, PartialEq)]
pub struct Segment {
    #[serde(skip_serializing)]
    pub id: usize,
    #[serde(skip_serializing)]
    pub page_id: usize,
    pub position: usize,
    pub html: Option<String>,
    pub action: Option<String>,
    pub script: Option<String>,
    pub target: Option<String>,
    pub gallery_id: Option<usize>,
    pub file_id: Option<usize>,
}

impl Segment {
    pub fn gallery_segment(page: SegmentPage, position: usize, gallery_id: usize) -> Segment {
        Segment {
            page_id: page.id,
            id: 0,
            position,
            html: None,
            action: None,
            script: None,
            target: None,
            gallery_id: Some(gallery_id),
            file_id: None,
        }
    }

    pub fn html_segment(page: SegmentPage, position: usize, html: String) -> Segment {
        Segment {
            page_id: page.id,
            id: 0,
            position,
            html: Some(html),
            action: None,
            script: None,
            target: None,
            gallery_id: None,
            file_id: None,
        }
    }

    pub fn file_segment(page: SegmentPage, position: usize, file_id: usize, action: Option<String>, script: Option<String>, target: Option<String>) -> Segment {
        Segment {
            page_id: page.id,
            id: 0,
            position,
            html: None,
            action,
            script,
            target,
            gallery_id: None,
            file_id: Some(file_id),
        }
    }
}