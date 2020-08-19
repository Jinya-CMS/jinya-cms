use serde_derive::{Deserialize, Serialize};

use crate::models::file::File;

#[derive(Serialize, Deserialize, Clone, PartialEq)]
pub struct GalleryFile {
    pub id: i32,
    pub file: File,
    pub position: usize,
}