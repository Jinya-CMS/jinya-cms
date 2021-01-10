use std::collections::BTreeMap;

use serde_json::{Map, Value};
use serde::{Serialize, Deserialize, Deserializer, Serializer};
use serde::de::IntoDeserializer;

#[derive(Clone, PartialEq)]
pub enum InputType {
    String,
    Boolean,
}

impl<'de> Deserialize<'de> for InputType {
    fn deserialize<D>(deserializer: D) -> Result<Self, D::Error>
        where D: Deserializer<'de>
    {
        let s = String::deserialize(deserializer)?;
        if s.to_lowercase() == "string" {
            Ok(InputType::String)
        } else if s.to_lowercase() == "boolean" {
            Ok(InputType::Boolean)
        } else {
            InputType::deserialize(s.into_deserializer())
        }
    }
}

impl Serialize for InputType {
    fn serialize<S>(&self, serializer: S) -> Result<<S as Serializer>::Ok, <S as Serializer>::Error> where
        S: Serializer {
        let value = match self {
            InputType::String => "string",
            InputType::Boolean => "boolean",
        };

        serializer.collect_str(value)
    }
}

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
    pub input_type: InputType,
}

#[derive(Deserialize, Serialize, Clone, PartialEq)]
pub struct ThemeGroup {
    pub name: String,
    pub title: String,
    pub fields: Option<Vec<ThemeField>>,
}

#[derive(Deserialize, Serialize, Clone, PartialEq)]
pub struct ThemeConfigurationStructure {
    pub title: String,
    pub links: ThemeLinks,
    pub groups: Vec<ThemeGroup>,
}