use serde_derive::{Deserialize, Serialize};

#[derive(Deserialize, Serialize)]
pub struct PicsumResponse {
    pub id: String,
    pub author: String,
    pub width: i32,
    pub height: i32,
    pub url: String,
    pub download_url: String,
}