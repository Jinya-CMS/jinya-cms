use serde_derive::{Deserialize, Serialize};

use crate::models::edited::Edited;

#[derive(Serialize, Deserialize, Clone, PartialEq)]
pub struct File {
    #[serde(skip_serializing)]
    #[serde(default)]
    pub created: Edited,
    #[serde(skip_serializing)]
    #[serde(default)]
    pub updated: Edited,
    #[serde(skip_serializing)]
    pub id: i32,
    pub name: String,
    #[serde(skip_serializing)]
    pub path: String,
    #[serde(alias = "type")]
    pub file_type: String,
}

impl File {
    pub fn from_name(name: String) -> File {
        File {
            created: Edited::new(),
            updated: Edited::new(),
            id: 0,
            name,
            path: "".to_string(),
            file_type: "".to_string(),
        }
    }
}