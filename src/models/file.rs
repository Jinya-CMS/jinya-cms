use serde_derive::{Deserialize, Serialize};

use crate::models::edited::Edited;

#[derive(Serialize, Deserialize, Clone)]
pub struct File {
    #[serde(skip_serializing)]
    pub created: Edited,
    #[serde(skip_serializing)]
    pub updated: Edited,
    #[serde(skip_serializing)]
    pub id: i32,
    pub name: String,
    #[serde(skip_serializing)]
    pub path: String,
    #[serde(alias = "type")]
    pub file_type: String,
}
