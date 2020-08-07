use serde::*;
use serde::de::IntoDeserializer;

use crate::models::edited::Edited;
use yew::services::ConsoleService;

#[derive(Clone, PartialEq)]
pub enum Orientation {
    Horizontal,
    Vertical,
}

impl<'de> Deserialize<'de> for Orientation {
    fn deserialize<D>(deserializer: D) -> Result<Self, D::Error>
        where D: Deserializer<'de>
    {
        let s = String::deserialize(deserializer)?;
        if s.to_lowercase() == "horizontal" {
            Ok(Orientation::Horizontal)
        } else if s.to_lowercase() == "vertical" {
            Ok(Orientation::Vertical)
        } else {
            Orientation::deserialize(s.into_deserializer())
        }
    }
}

impl Serialize for Orientation {
    fn serialize<S>(&self, serializer: S) -> Result<<S as Serializer>::Ok, <S as Serializer>::Error> where
        S: Serializer {
        let value = match self {
            Orientation::Vertical => "vertical",
            Orientation::Horizontal => "horizontal",
        };

        serializer.collect_str(value)
    }
}

#[derive(Clone, PartialEq)]
pub enum GalleryType {
    Masonry,
    Sequence,
}

impl<'de> Deserialize<'de> for GalleryType {
    fn deserialize<D>(deserializer: D) -> Result<Self, D::Error>
        where D: Deserializer<'de>
    {
        let s = String::deserialize(deserializer)?;
        if s.to_lowercase() == "masonry" {
            Ok(GalleryType::Masonry)
        } else if s.to_lowercase() == "sequence" {
            Ok(GalleryType::Sequence)
        } else {
            GalleryType::deserialize(s.into_deserializer())
        }
    }
}

impl Serialize for GalleryType {
    fn serialize<S>(&self, serializer: S) -> Result<<S as Serializer>::Ok, <S as Serializer>::Error> where
        S: Serializer {
        let value = match self {
            GalleryType::Masonry => "masonry",
            GalleryType::Sequence => "sequence",
        };

        serializer.collect_str(value)
    }
}

#[derive(Serialize, Deserialize, Clone, PartialEq)]
#[serde(rename_all = "camelCase")]
pub struct Gallery {
    #[serde(skip_serializing)]
    pub id: i32,
    pub name: String,
    #[serde(alias = "type")]
    #[serde(rename = "type")]
    pub gallery_type: GalleryType,
    pub orientation: Orientation,
    pub description: String,
    #[serde(skip_serializing)]
    pub created: Edited,
    #[serde(skip_serializing)]
    pub updated: Edited,
}

impl Gallery {
    pub fn from_all_fields(name: String, description: String, orientation: Orientation, gallery_type: GalleryType) -> Gallery {
        Gallery {
            id: -1,
            name,
            gallery_type,
            orientation,
            description,
            created: Edited::new(),
            updated: Edited::new(),
        }
    }
}