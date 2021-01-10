use serde_derive::{Deserialize, Serialize};

#[derive(Serialize, Deserialize, Clone, PartialEq)]
#[serde(rename_all = "camelCase")]
pub struct MenuItemArtist {
    pub id: usize,
    pub artist_name: String,
    pub email: String,
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
    pub route: Option<String>,
    pub items: Vec<MenuItem>,
    pub artist: Option<MenuItemArtist>,
    pub gallery: Option<MenuItemGallery>,
    pub page: Option<MenuItemPage>,
    pub segment_page: Option<MenuItemSegmentPage>,
}

impl MenuItem {
    pub fn empty() -> MenuItem {
        MenuItem {
            id: 0,
            position: 0,
            highlighted: false,
            title: "".to_string(),
            route: None,
            items: vec![],
            artist: None,
            gallery: None,
            page: None,
            segment_page: None,
        }
    }
}

#[derive(Serialize, Deserialize, Clone, PartialEq)]
#[serde(rename_all = "camelCase")]
pub struct SaveMenuItem {
    pub highlighted: bool,
    pub title: String,
    pub route: Option<String>,
    pub artist: Option<usize>,
    pub gallery: Option<usize>,
    pub page: Option<usize>,
    pub segment_page: Option<usize>,
    pub position: Option<usize>,
}

impl SaveMenuItem {
    pub fn new_link(title: String, highlighted: bool, route: String) -> SaveMenuItem {
        SaveMenuItem {
            highlighted,
            title,
            route: Some(route),
            artist: None,
            gallery: None,
            page: None,
            segment_page: None,
            position: None,
        }
    }

    pub fn new_group(title: String, highlighted: bool) -> SaveMenuItem {
        SaveMenuItem {
            highlighted,
            title,
            route: None,
            artist: None,
            gallery: None,
            page: None,
            segment_page: None,
            position: None,
        }
    }

    pub fn new_page(title: String, highlighted: bool, route: String, page: usize) -> SaveMenuItem {
        SaveMenuItem {
            highlighted,
            title,
            route: Some(route),
            artist: None,
            gallery: None,
            page: Some(page),
            segment_page: None,
            position: None,
        }
    }

    pub fn new_segment_page(title: String, highlighted: bool, route: String, page: usize) -> SaveMenuItem {
        SaveMenuItem {
            highlighted,
            title,
            route: Some(route),
            artist: None,
            gallery: None,
            page: None,
            segment_page: Some(page),
            position: None,
        }
    }

    pub fn new_gallery(title: String, highlighted: bool, route: String, gallery: usize) -> SaveMenuItem {
        SaveMenuItem {
            highlighted,
            title,
            route: Some(route),
            artist: None,
            gallery: Some(gallery),
            page: None,
            segment_page: None,
            position: None,
        }
    }

    pub fn new_profile(title: String, highlighted: bool, route: String, profile: usize) -> SaveMenuItem {
        SaveMenuItem {
            highlighted,
            title,
            route: Some(route),
            artist: Some(profile),
            gallery: None,
            page: None,
            segment_page: None,
            position: None,
        }
    }
}