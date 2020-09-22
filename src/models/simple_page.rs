use serde_derive::{Deserialize, Serialize};

use crate::models::edited::Edited;

#[derive(Serialize, Deserialize, Clone, PartialEq)]
pub struct SimplePage {
    #[serde(skip_serializing)]
    #[serde(default)]
    pub created: Edited,
    #[serde(skip_serializing)]
    #[serde(default)]
    pub updated: Edited,
    #[serde(skip_serializing)]
    pub id: usize,
    pub title: String,
    pub content: String,
}

impl SimplePage {
    pub fn from_title_and_content(title: String, content: String) -> SimplePage{
        SimplePage {
            created: Edited::new(),
            updated: Edited::new(),
            id: 0,
            title,
            content,
        }
    }
}