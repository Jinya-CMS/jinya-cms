use serde_derive::{Deserialize, Serialize};

#[derive(Serialize, Deserialize, Clone, PartialEq)]
#[serde(rename_all = "camelCase")]
pub struct Artist {
    pub artist_name: String,
    pub email: String,
    pub profile_picture: String,
    pub roles: Vec<String>,
    pub enabled: bool,
    pub id: usize,
    pub about_me: Option<String>,
}

impl Artist {
    pub fn from_name_email_roles(artist_name: String, email: String, roles: Vec<String>) -> Artist {
        Artist {
            artist_name,
            email,
            profile_picture: "".to_string(),
            roles,
            enabled: false,
            id: 0,
            about_me: None,
        }
    }
}