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

#[derive(Deserialize, Serialize, Clone, PartialEq)]
pub struct ThemeLinks {
    pub pages: Option<Map<String, Value>>,
    pub segment_pages: Option<Map<String, Value>>,
    pub galleries: Option<Map<String, Value>>,
    pub menus: Option<Map<String, Value>>,
    pub files: Option<Map<String, Value>>,
}

#[derive(Deserialize, Serialize, Clone, PartialEq)]
pub struct ThemeField {
    pub name: String,
    pub label: String,
    #[serde(alias = "type")]
    #[serde(rename = "type")]
    pub input_type: String,
}

#[derive(Deserialize, Serialize, Clone, PartialEq)]
pub struct ThemeGroup {
    pub name: String,
    pub title: String,
    pub fields: Option<Vec<ThemeField>>,
    pub groups: Option<Vec<ThemeGroup>>,
}

#[derive(Deserialize, Serialize, Clone, PartialEq)]
pub struct ThemeConfigurationStructure {
    pub title: String,
    pub links: ThemeLinks,
    pub groups: Vec<ThemeGroup>,
}