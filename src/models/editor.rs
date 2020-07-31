use serde_derive::{Deserialize, Serialize};

#[derive(Serialize, Deserialize, Clone, PartialEq)]
#[serde(rename_all = "camelCase")]
pub struct Editor {
    pub artist_name: String,
    pub email: String,
    pub profile_picture: String,
}