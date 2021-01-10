use serde_derive::{Deserialize, Serialize};

use crate::models::edited::Edited;

#[derive(Serialize, Deserialize, Clone, PartialEq)]
#[serde(rename_all = "camelCase")]
pub struct SegmentPage {
    #[serde(skip_serializing)]
    #[serde(default)]
    pub created: Edited,
    #[serde(skip_serializing)]
    #[serde(default)]
    pub updated: Edited,
    #[serde(skip_serializing)]
    pub id: usize,
    pub name: String,
    #[serde(skip_serializing)]
    pub segment_count: usize,
}

impl SegmentPage {
    pub fn from_name(name: String) -> SegmentPage {
        SegmentPage {
            created: Edited::new(),
            updated: Edited::new(),
            id: 0,
            name,
            segment_count: 0,
        }
    }
}