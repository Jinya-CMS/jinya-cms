use core::cmp::Ordering;

use jinya_ui::layout::page::Page;
use jinya_ui::widgets::toast::Toast;
use serde::Serialize;
use serde_derive::*;
use wasm_bindgen::JsValue;
use yew::prelude::*;
use yew::services::fetch::FetchTask;

use crate::ajax::{AjaxError, get_host};
use crate::ajax::file_service::FileService;
use crate::ajax::gallery_file_service::GalleryFileService;
use crate::i18n::Translator;
use crate::models::file::File;
use crate::models::gallery_file::GalleryFile;
use crate::models::list_model::ListModel;

#[derive(Serialize, Deserialize, Debug, PartialEq)]
pub enum PositionOrFile {
    Position,
    File,
}

#[derive(Serialize, Deserialize, Debug)]
pub struct GalleryDesignerDragData {
    pub position: Option<usize>,
    pub new_position: Option<usize>,
    pub r#type: PositionOrFile,
}

#[derive(PartialEq, Clone, Properties)]
pub struct GalleryDesignerPageProps {
    pub id: i32,
}

pub struct GalleryDesignerPage {
    link: ComponentLink<Self>,
    files: Vec<File>,
    gallery_files: Vec<GalleryFile>,
    id: i32,
    file_service: FileService,
    file_loader_task: Option<FetchTask>,
    drag_over_position: Option<usize>,
    selected_position: Option<usize>,
    previous_drag_over_position: Option<usize>,
    gallery_file_service: GalleryFileService,
    gallery_file_loader_task: Option<FetchTask>,
    gallery_file_delete_task: Option<FetchTask>,
    translator: Translator,
}

pub enum GalleryDesignerMsg {
    OnFilesLoaded(Result<ListModel<File>, AjaxError>),
    OnGalleryFilesLoaded(Result<Vec<GalleryFile>, AjaxError>),
    OnFileDrag(DragEvent, usize),
    OnPositionDrop(DragEvent),
    OnPositionDropOnFiles(DragEvent),
    OnPositionDragOver(DragEvent, usize),
    OnPositionDragEnter(DragEvent, usize),
    OnNewPositionDragOver(DragEvent),
    OnNewPositionDragEnter(DragEvent),
    OnPositionsDragOver(DragEvent),
    OnPositionsDragEnter(DragEvent),
    OnPositionDragExit,
    OnPositionDragStart(DragEvent, usize),
    OnFilesDragEnter(DragEvent),
    OnFilesDragOver(DragEvent),
    OnPositionDeleted(Result<usize, AjaxError>),
}

impl Component for GalleryDesignerPage {
    type Message = GalleryDesignerMsg;
    type Properties = GalleryDesignerPageProps;

    fn create(props: Self::Properties, link: ComponentLink<Self>) -> Self {
        GalleryDesignerPage {
            id: props.id,
            link,
            files: vec![],
            file_service: FileService::new(),
            file_loader_task: None,
            gallery_files: vec![],
            drag_over_position: None,
            selected_position: None,
            previous_drag_over_position: None,
            gallery_file_service: GalleryFileService::new(),
            gallery_file_loader_task: None,
            gallery_file_delete_task: None,
            translator: Translator::new(),
        }
    }

    fn update(&mut self, msg: Self::Message) -> bool {
        match msg {
            GalleryDesignerMsg::OnFilesLoaded(items) => self.files = items.unwrap().items,
            GalleryDesignerMsg::OnFileDrag(event, idx) => {
                let data_transfer = event.data_transfer();
                if data_transfer.is_some() {
                    let data_transfer_unwrapped = data_transfer.unwrap();
                    data_transfer_unwrapped.set_drop_effect("copy");
                    data_transfer_unwrapped.set_effect_allowed("copy");
                    let data = GalleryDesignerDragData {
                        position: Some(idx),
                        new_position: None,
                        r#type: PositionOrFile::File,
                    };
                    let serialize_result = serde_json::to_string_pretty(&data);
                    if serialize_result.is_ok() {
                        data_transfer_unwrapped.set_data("text/json", serialize_result.unwrap().as_str());
                    }
                }
            }
            GalleryDesignerMsg::OnPositionDragStart(event, idx) => {
                let data_transfer = event.data_transfer();
                if data_transfer.is_some() {
                    let data_transfer_unwrapped = data_transfer.unwrap();
                    data_transfer_unwrapped.set_drop_effect("move");
                    data_transfer_unwrapped.set_effect_allowed("move");
                    let data = GalleryDesignerDragData {
                        position: Some(idx),
                        new_position: None,
                        r#type: PositionOrFile::Position,
                    };
                    self.selected_position = Some(idx);
                    let serialize_result = serde_json::to_string_pretty(&data);
                    if serialize_result.is_ok() {
                        data_transfer_unwrapped.set_data("text/json", serialize_result.unwrap().as_str());
                    }
                }
            }
            GalleryDesignerMsg::OnPositionDrop(event) => {
                event.prevent_default();
                event.stop_propagation();
                let data_transfer = event.data_transfer();
                if data_transfer.is_some() {
                    let data_transfer_unwrapped = data_transfer.unwrap();
                    let item: GalleryDesignerDragData = serde_json::from_str(data_transfer_unwrapped.get_data("text/json").unwrap().as_str()).unwrap();
                    match item.r#type {
                        PositionOrFile::Position => {
                            if self.drag_over_position.is_some() {
                                let gallery_file = self.gallery_files[self.selected_position.unwrap()].clone();
                                let old_position_after_insert = if self.drag_over_position.unwrap() > self.selected_position.unwrap() {
                                    self.selected_position.unwrap()
                                } else {
                                    self.selected_position.unwrap() + 1
                                };
                                if self.previous_drag_over_position.is_some() {
                                    if self.drag_over_position.unwrap() > self.previous_drag_over_position.unwrap() {
                                        self.gallery_files.insert(self.drag_over_position.unwrap() + 1, gallery_file);
                                        self.gallery_files.remove(old_position_after_insert);
                                    } else {
                                        self.gallery_files.insert(self.drag_over_position.unwrap(), gallery_file);
                                        self.gallery_files.remove(old_position_after_insert);
                                    }
                                } else {
                                    if self.drag_over_position.unwrap() > self.selected_position.unwrap() {
                                        self.gallery_files.insert(self.drag_over_position.unwrap() + 1, gallery_file);
                                        self.gallery_files.remove(old_position_after_insert);
                                    } else {
                                        self.gallery_files.insert(self.drag_over_position.unwrap(), gallery_file);
                                        self.gallery_files.remove(old_position_after_insert);
                                    }
                                }
                            }
                        }
                        PositionOrFile::File => {
                            let file = self.files[item.position.unwrap()].clone();
                            let mut gallery_file = GalleryFile {
                                id: 0,
                                file,
                                position: 0,
                            };
                            if self.drag_over_position.is_some() {
                                if self.drag_over_position.unwrap() == self.gallery_files.len() {
                                    self.gallery_files.push(gallery_file);
                                } else if self.previous_drag_over_position.is_some() {
                                    if self.drag_over_position.unwrap() > self.previous_drag_over_position.unwrap() {
                                        self.gallery_files.insert(self.drag_over_position.unwrap() + 1, gallery_file);
                                    } else {
                                        self.gallery_files.insert(self.drag_over_position.unwrap(), gallery_file);
                                    }
                                } else {
                                    self.gallery_files.insert(self.drag_over_position.unwrap(), gallery_file);
                                }
                            } else if self.previous_drag_over_position.is_some() {
                                self.gallery_files.insert(self.previous_drag_over_position.unwrap(), gallery_file);
                            }
                        }
                    };

                    self.reorder_positions();

                    self.drag_over_position = None;
                    self.previous_drag_over_position = None;
                    self.selected_position = None;
                }
            }
            GalleryDesignerMsg::OnPositionDragOver(event, idx) => {
                let data_transfer = event.data_transfer();
                if data_transfer.is_some() {
                    let data_transfer_unwrapped = data_transfer.unwrap();
                    if data_transfer_unwrapped.types().includes(&JsValue::from_str("text/json"), 0) {
                        event.prevent_default();
                        event.stop_propagation();
                    }
                }
            }
            GalleryDesignerMsg::OnPositionDragEnter(event, idx) => {
                let data_transfer = event.data_transfer();
                if data_transfer.is_some() {
                    let data_transfer_unwrapped = data_transfer.unwrap();
                    if data_transfer_unwrapped.types().includes(&JsValue::from_str("text/json"), 0) {
                        event.prevent_default();
                        let item: GalleryDesignerDragData = serde_json::from_str(data_transfer_unwrapped.get_data("text/json").unwrap().as_str()).unwrap();
                        let data = GalleryDesignerDragData {
                            position: item.position,
                            new_position: Some(idx),
                            r#type: item.r#type,
                        };
                        self.drag_over_position = Some(idx);
                        let serialize_result = serde_json::to_string_pretty(&data);
                        if serialize_result.is_ok() {
                            data_transfer_unwrapped.set_data("text/json", serialize_result.unwrap().as_str());
                        }
                    }
                }
            }
            GalleryDesignerMsg::OnNewPositionDragOver(event) => {
                let data_transfer = event.data_transfer();
                if data_transfer.is_some() {
                    let data_transfer_unwrapped = data_transfer.unwrap();
                    if data_transfer_unwrapped.types().includes(&JsValue::from_str("text/json"), 0) {
                        event.prevent_default();
                        event.stop_propagation();
                    }
                }
            }
            GalleryDesignerMsg::OnNewPositionDragEnter(event) => {
                let data_transfer = event.data_transfer();
                if data_transfer.is_some() {
                    let data_transfer_unwrapped = data_transfer.unwrap();
                    if data_transfer_unwrapped.types().includes(&JsValue::from_str("text/json"), 0) {
                        event.prevent_default();
                        let idx = self.gallery_files.len();
                        let item: GalleryDesignerDragData = serde_json::from_str(data_transfer_unwrapped.get_data("text/json").unwrap().as_str()).unwrap();
                        let data = GalleryDesignerDragData {
                            position: item.position,
                            new_position: Some(idx),
                            r#type: item.r#type,
                        };
                        self.drag_over_position = Some(idx);
                        let serialize_result = serde_json::to_string_pretty(&data);
                        if serialize_result.is_ok() {
                            data_transfer_unwrapped.set_data("text/json", serialize_result.unwrap().as_str());
                        }
                    }
                }
            }
            GalleryDesignerMsg::OnPositionDragExit => self.drag_over_position = None,
            GalleryDesignerMsg::OnPositionsDragOver(event) => {
                let data_transfer = event.data_transfer();
                if data_transfer.is_some() {
                    let data_transfer_unwrapped = data_transfer.unwrap();
                    if data_transfer_unwrapped.types().includes(&JsValue::from_str("text/json"), 0) {
                        event.prevent_default();
                        if self.drag_over_position.is_some() {
                            self.previous_drag_over_position = Some(self.drag_over_position.unwrap());
                        }
                        self.drag_over_position = None;
                    }
                }
            }
            GalleryDesignerMsg::OnPositionsDragEnter(event) => {
                let data_transfer = event.data_transfer();
                if data_transfer.is_some() {
                    let data_transfer_unwrapped = data_transfer.unwrap();
                    if data_transfer_unwrapped.types().includes(&JsValue::from_str("text/json"), 0) {
                        event.prevent_default();
                    }
                }
            }
            GalleryDesignerMsg::OnPositionDropOnFiles(event) => {
                event.prevent_default();
                event.stop_propagation();
                let data_transfer = event.data_transfer();
                if data_transfer.is_some() {
                    let data_transfer_unwrapped = data_transfer.unwrap();
                    let item: GalleryDesignerDragData = serde_json::from_str(data_transfer_unwrapped.get_data("text/json").unwrap().as_str()).unwrap();
                    if item.r#type == PositionOrFile::Position {
                        self.gallery_file_delete_task = Some(self.gallery_file_service.delete_position(self.id, self.selected_position.unwrap(), self.link.callback(|result| GalleryDesignerMsg::OnPositionDeleted(result))));
                    }
                }
            }
            GalleryDesignerMsg::OnFilesDragEnter(event) => {
                event.prevent_default();
                event.stop_propagation();
            }
            GalleryDesignerMsg::OnFilesDragOver(event) => {
                event.prevent_default();
                event.stop_propagation();
            }
            GalleryDesignerMsg::OnGalleryFilesLoaded(data) => self.gallery_files = data.unwrap(),
            GalleryDesignerMsg::OnPositionDeleted(result) => {
                if result.is_ok() {
                    self.gallery_files.remove(result.unwrap());
                    self.reorder_positions();
                } else {
                    Toast::negative_toast(self.translator.translate("galleries.designer.delete_error"));
                }
            }
        }

        true
    }

    fn change(&mut self, props: Self::Properties) -> bool {
        self.id = props.id;

        true
    }

    fn view(&self) -> Html {
        let mut gallery_files = self.gallery_files.clone();
        gallery_files.sort_by(|a, b| if a.position > b.position { Ordering::Greater } else if b.position > a.position { Ordering::Less } else { Ordering::Equal });
        let new_position_active_class = if self.drag_over_position.is_some() && self.gallery_files.len() == self.drag_over_position.unwrap() { "jinya-designer-gallery-designer__item--drag-over" } else { "" };
        html! {
            <Page>
                <div class="jinya-designer-gallery-designer__container">
                    <div ondragenter=self.link.callback(|event| GalleryDesignerMsg::OnFilesDragEnter(event)) ondragover=self.link.callback(|event| GalleryDesignerMsg::OnFilesDragOver(event)) ondrop=self.link.callback(|event| GalleryDesignerMsg::OnPositionDropOnFiles(event)) class="jinya-designer-gallery-designer__list jinya-designer-gallery-designer__list--file">
                        {for self.files.iter().enumerate().map(|(idx, item)| {
                            html! {
                                <img ondragstart=self.link.callback(move |event| GalleryDesignerMsg::OnFileDrag(event, idx)) src=format!("{}{}", get_host(), &item.path) class="jinya-designer-gallery-designer__image jinya-designer-gallery-designer__item jinya-designer-gallery-designer__item--file" />
                            }
                        })}
                    </div>
                    <div class="jinya-designer-gallery-designer__middle-bar"></div>
                    <div ondragover=self.link.callback(|event| GalleryDesignerMsg::OnPositionsDragOver(event)) ondragenter=self.link.callback(|event| GalleryDesignerMsg::OnPositionsDragEnter(event)) ondrop=self.link.callback(move |event| GalleryDesignerMsg::OnPositionDrop(event)) class="jinya-designer-gallery-designer__list jinya-designer-gallery-designer__list--positions">
                        {for gallery_files.iter().enumerate().map(|(idx, item)| {
                            html! {
                                <img ondragover=self.link.callback(move |event| GalleryDesignerMsg::OnPositionDragOver(event, idx)) ondragenter=self.link.callback(move |event| GalleryDesignerMsg::OnPositionDragEnter(event, idx)) ondragstart=self.link.callback(move |event| GalleryDesignerMsg::OnPositionDragStart(event, idx)) src=format!("{}{}", get_host(), &item.file.path) class=("jinya-designer-gallery-designer__image jinya-designer-gallery-designer__item jinya-designer-gallery-designer__item--position", self.get_active_item_class_if_selected_item_is_active(idx)) />
                            }
                        })}
                        <div ondragover=self.link.callback(|event| GalleryDesignerMsg::OnNewPositionDragOver(event)) ondragenter=self.link.callback(|event| GalleryDesignerMsg::OnNewPositionDragEnter(event)) class=("jinya-designer-gallery-designer__item jinya-designer-gallery-designer__item--position jinya-designer-gallery-designer__item--next-item", new_position_active_class)></div>
                    </div>
                </div>
            </Page>
        }
    }

    fn rendered(&mut self, first_render: bool) {
        if first_render {
            self.gallery_file_loader_task = Some(self.gallery_file_service.get_positions(self.id, self.link.callback(|result| GalleryDesignerMsg::OnGalleryFilesLoaded(result))));
            self.file_loader_task = Some(self.file_service.get_list("".to_string(), self.link.callback(|result| GalleryDesignerMsg::OnFilesLoaded(result))));
        }
    }
}


impl GalleryDesignerPage {
    fn get_active_item_class_if_selected_item_is_active(&self, hover_item: usize) -> String {
        if self.drag_over_position.is_some() && self.drag_over_position.unwrap() == hover_item {
            "jinya-designer-gallery-designer__item--drag-over".to_string()
        } else {
            "".to_string()
        }
    }

    fn reorder_positions(&mut self) {
        for i in 0..self.gallery_files.len() {
            self.gallery_files[i].position = i;
        }
    }
}
