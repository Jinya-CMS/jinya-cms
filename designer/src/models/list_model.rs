use serde::{Deserialize, Serialize};

#[derive(Deserialize, Serialize)]
#[serde(rename_all = "camelCase")]
pub struct ListModel<T> {
    pub items: Vec<T>,
    pub total_count: i32,
    pub items_count: i32,
    pub offset: i32,
}