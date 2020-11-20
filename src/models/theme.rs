use std::collections::BTreeMap;

use serde_derive::*;
use serde_json::{Map, Value};

#[derive(Deserialize, Serialize, Clone, PartialEq)]
#[serde(rename_all = "camelCase")]
pub struct Theme {
    pub id: usize,
    pub name: String,
    pub description: String,
    pub display_name: String,
    pub scss_variables: BTreeMap<String, String>,
    pub configuration: Map<String, Value>,
}