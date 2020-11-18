use serde_derive::*;
use serde_json::{Map, Value};
use std::collections::HashMap;

#[derive(Deserialize, Serialize, Clone, PartialEq)]
#[serde(rename_all="camelCase")]
pub struct Theme {
   pub id: usize,
   pub name: String,
   pub description: String,
   pub display_name: String,
   pub scss_variables: HashMap<String, String>,
   pub configuration: Map<String, Value>,
}