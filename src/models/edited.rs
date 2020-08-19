use chrono::{DateTime, FixedOffset};
use serde_derive::{Deserialize, Serialize};

use crate::models::editor::Editor;

#[derive(Serialize, Deserialize, Clone, PartialEq)]
pub struct Edited {
    pub by: Editor,
    at: String,
}

impl Default for Edited {
    fn default() -> Self {
        Edited::new()
    }
}

impl Edited {
    pub fn get_at(&self) -> DateTime<FixedOffset> {
        DateTime::parse_from_rfc3339(self.at.as_str()).unwrap()
    }

    pub fn new() -> Edited {
        Edited {
            at: "".to_string(),
            by: Editor {
                artist_name: "".to_string(),
                email: "".to_string(),
                profile_picture: "".to_string(),
            },
        }
    }
}