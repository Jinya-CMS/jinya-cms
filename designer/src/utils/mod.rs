use wasm_bindgen::prelude::wasm_bindgen;

pub struct TinyMce {
    id: String,
}

#[wasm_bindgen]
extern "C" {
    fn initTinyMce(id: String, content: String);
    fn setContent(id: String, content: String);
    fn getContent(id: String) -> String;
    fn destroyEditor(id: String);
}

impl TinyMce {
    pub fn init_tiny_mce(&self, content: String) {
        initTinyMce(self.id.clone(), content)
    }

    pub fn get_content(&self) -> String {
        getContent(self.id.clone())
    }

    pub fn set_content(&self, content: String) {
        setContent(self.id.clone(), content)
    }

    pub fn destroy_editor(&self) {
        destroyEditor(self.id.clone())
    }

    pub fn new(id: String) -> Self {
        TinyMce { id }
    }
}