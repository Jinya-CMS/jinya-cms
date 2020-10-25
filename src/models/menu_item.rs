use serde_derive::{Deserialize, Serialize};

#[derive(Serialize, Deserialize, Clone, PartialEq)]
#[serde(rename_all = "camelCase")]
pub struct MenuItemArtist {
    pub id: usize,
    pub artist_name: String,
}

#[derive(Serialize, Deserialize, Clone, PartialEq)]
pub struct MenuItemPage {
    pub id: usize,
    pub title: String,
}

#[derive(Serialize, Deserialize, Clone, PartialEq)]
pub struct MenuItemSegmentPage {
    pub id: usize,
    pub name: String,
}

#[derive(Serialize, Deserialize, Clone, PartialEq)]
pub struct MenuItemGallery {
    pub id: usize,
    pub name: String,
}

#[derive(Serialize, Deserialize, Clone, PartialEq)]
#[serde(rename_all = "camelCase")]
pub struct MenuItem {
    pub id: usize,
    pub position: usize,
    pub highlighted: bool,
    pub title: String,
    pub route: String,
    pub items: Vec<MenuItem>,
    pub artist: Option<MenuItemArtist>,
    pub gallery: Option<MenuItemGallery>,
    pub page: Option<MenuItemPage>,
    pub segment_page: Option<MenuItemSegmentPage>,
}
