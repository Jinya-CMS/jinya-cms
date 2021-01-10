use serde_derive::{Deserialize, Serialize};

use crate::models::file::File;
use crate::models::gallery::Gallery;

#[derive(Serialize, Deserialize, Clone, PartialEq)]
pub struct FormSegment {
    pub id: usize,
    pub title: String,
}

#[derive(Serialize, Deserialize, Clone, PartialEq)]
pub struct GallerySegment {
    pub id: usize,
    pub name: String,
}

#[derive(Serialize, Deserialize, Clone, PartialEq)]
pub struct FileSegment {
    pub id: usize,
    pub name: String,
    pub path: String,
}

#[derive(Serialize, Deserialize, Clone, PartialEq)]
pub struct Segment {
    #[serde(skip_serializing)]
    pub id: usize,
    pub position: usize,
    pub html: Option<String>,
    pub action: Option<String>,
    pub script: Option<String>,
    pub target: Option<String>,
    #[serde(default)]
    pub form: Option<FormSegment>,
    #[serde(default)]
    pub gallery: Option<GallerySegment>,
    #[serde(default)]
    pub file: Option<FileSegment>,
}

impl Segment {
    pub fn gallery_segment(position: usize, gallery: Gallery) -> Segment {
        Segment {
            id: 0,
            position,
            html: None,
            action: None,
            script: None,
            target: None,
            form: None,
            gallery: Some(GallerySegment {
                id: gallery.id,
                name: gallery.name,
            }),
            file: None,
        }
    }

    pub fn html_segment(position: usize, html: String) -> Segment {
        Segment {
            id: 0,
            position,
            html: Some(html),
            action: None,
            script: None,
            target: None,
            form: None,
            gallery: None,
            file: None,
        }
    }

    pub fn file_segment(position: usize, file: File, action: Option<String>, script: Option<String>, target: Option<String>) -> Segment {
        Segment {
            id: 0,
            position,
            html: None,
            action,
            script,
            target,
            form: None,
            gallery: None,
            file: Some(FileSegment {
                name: file.name,
                path: file.path,
                id: file.id,
            }),
        }
    }
}