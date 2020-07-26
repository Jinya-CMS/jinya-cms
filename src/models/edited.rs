use chrono::{DateTime, FixedOffset};
use serde_derive::{Deserialize, Serialize};

use crate::models::editor::Editor;

#[derive(Serialize, Deserialize, Clone)]
pub struct Edited {
    pub by: Editor,
    at: String,
}

impl Edited {
    pub fn get_at(&self) -> DateTime<FixedOffset> {
        DateTime::parse_from_rfc3339(self.at.as_str()).unwrap()
    }
}