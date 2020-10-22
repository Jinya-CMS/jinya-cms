use core::cmp::Ordering;

use jinya_ui::layout::page::Page;
use jinya_ui::widgets::toast::Toast;
use serde::Serialize;
use serde_derive::*;
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
    pub id: usize,
}

pub struct GalleryDesignerPage {
    link: ComponentLink<Self>,
    files: Vec<File>,
    gallery_files: Vec<GalleryFile>,
    id: usize,
    file_service: FileService,
    file_loader_task: Option<FetchTask>,
    drag_over_position: Option<usize>,
    selected_position: Option<usize>,
    previous_drag_over_position: Option<usize>,
    gallery_file_service: GalleryFileService,
    gallery_file_loader_task: Option<FetchTask>,
    gallery_file_delete_task: Option<FetchTask>,
    gallery_file_create_task: Option<FetchTask>,
    gallery_file_update_task: Option<FetchTask>,
    translator: Translator,
    current_position: Option<usize>,
    selected_position_type: Option<PositionOrFile>,
    selected_file: Option<usize>,
}

pub enum GalleryDesignerMsg {
    OnFilesLoaded(Result<ListModel<File>, AjaxError>),
    OnGalleryFilesLoaded(Result<Vec<GalleryFile>, AjaxError>),
    OnFileDrag(DragEvent, usize),
    OnPositionDrop(DragEvent),
    OnPositionDropOnFiles(DragEvent),
    OnNewPositionDragOver(usize),
    OnPositionsDragOver(DragEvent),
    OnPositionsDragEnter(DragEvent),
    OnPositionDragStart(DragEvent, usize),
    OnFilesDragEnter(DragEvent),
    OnFilesDragOver(DragEvent),
    OnPositionDeleted(Result<usize, AjaxError>),
    OnPositionAdded(Result<GalleryFile, AjaxError>),
    OnPositionUpdated(Result<bool, AjaxError>),
    OnDragEnd,
    OnInvalidDrop(DragEvent),
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
            gallery_file_create_task: None,
            gallery_file_update_task: None,
            translator: Translator::new(),
            current_position: None,
            selected_position_type: None,
            selected_file: None,
        }
    }

    fn update(&mut self, msg: Self::Message) -> bool {
        match msg {
            GalleryDesignerMsg::OnFilesLoaded(items) => self.files = items.unwrap().items,
            GalleryDesignerMsg::OnFileDrag(event, idx) => {
                let data_transfer = event.data_transfer();
                self.selected_position_type = Some(PositionOrFile::File);
                self.selected_file = Some(idx);
                if data_transfer.is_some() {
                    let data_transfer_unwrapped = data_transfer.unwrap();
                    data_transfer_unwrapped.set_drop_effect("copy");
                    data_transfer_unwrapped.set_effect_allowed("copy");
                }
            }
            GalleryDesignerMsg::OnPositionDragStart(event, idx) => {
                let data_transfer = event.data_transfer();
                self.selected_position_type = Some(PositionOrFile::Position);
                self.selected_position = Some(idx);
                if data_transfer.is_some() {
                    let data_transfer_unwrapped = data_transfer.unwrap();
                    data_transfer_unwrapped.set_drop_effect("copy");
                    data_transfer_unwrapped.set_effect_allowed("copy");
                }
            }
            GalleryDesignerMsg::OnPositionDrop(event) => {
                if self.selected_position_type.is_some() {
                    event.prevent_default();
                    event.stop_propagation();
                    let new_position = if self.current_position.is_some() {
                        if self.current_position.unwrap() >= self.gallery_files.len() {
                            self.get_last_position()
                        } else {
                            self.gallery_files[self.current_position.unwrap()].position
                        }
                    } else {
                        1
                    };
                    match self.selected_position_type.as_ref().unwrap() {
                        PositionOrFile::Position => {
                            let old_position = self.gallery_files[self.selected_position.unwrap()].position;
                            self.gallery_file_update_task = Some(self.gallery_file_service.update_position(self.id, old_position, new_position, self.link.callback(move |result| GalleryDesignerMsg::OnPositionUpdated(result))));
                        }
                        PositionOrFile::File => {
                            let file = self.files[self.selected_file.unwrap()].clone();
                            self.gallery_file_create_task = Some(self.gallery_file_service.create_position(self.id, file.id, new_position, self.link.callback(move |result| GalleryDesignerMsg::OnPositionAdded(result))));
                        }
                    }

                    self.selected_position = None;
                    self.current_position = None;
                    self.selected_position_type = None;
                }
            }
            GalleryDesignerMsg::OnNewPositionDragOver(idx) => self.current_position = Some(idx),
            GalleryDesignerMsg::OnPositionsDragOver(event) => {
                event.prevent_default();
                event.stop_propagation();
            }
            GalleryDesignerMsg::OnPositionsDragEnter(event) => {
                event.prevent_default();
                event.stop_propagation();
            }
            GalleryDesignerMsg::OnPositionDropOnFiles(event) => {
                if self.selected_position_type.is_some() && self.selected_position_type.as_ref().unwrap().eq(&PositionOrFile::Position) {
                    event.prevent_default();
                    event.stop_propagation();
                    self.gallery_file_delete_task = Some(self.gallery_file_service.delete_position(self.id, self.selected_position.unwrap(), self.link.callback(|result| GalleryDesignerMsg::OnPositionDeleted(result))));

                    self.selected_position = None;
                    self.current_position = None;
                    self.selected_position_type = None;
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
                    self.gallery_file_loader_task = Some(self.gallery_file_service.get_positions(self.id, self.link.callback(|result| GalleryDesignerMsg::OnGalleryFilesLoaded(result))));
                } else {
                    Toast::negative_toast(self.translator.translate("galleries.designer.error_delete"));
                }
            }
            GalleryDesignerMsg::OnPositionAdded(result) => {
                if result.is_ok() {
                    self.gallery_file_loader_task = Some(self.gallery_file_service.get_positions(self.id, self.link.callback(|result| GalleryDesignerMsg::OnGalleryFilesLoaded(result))));
                } else {
                    Toast::negative_toast(self.translator.translate("galleries.designer.error_add"))
                }
            }
            GalleryDesignerMsg::OnPositionUpdated(result) => {
                if result.is_ok() {
                    self.gallery_file_loader_task = Some(self.gallery_file_service.get_positions(self.id, self.link.callback(|result| GalleryDesignerMsg::OnGalleryFilesLoaded(result))));
                } else {
                    Toast::negative_toast(self.translator.translate("galleries.designer.error_update"));
                }
            }
            GalleryDesignerMsg::OnDragEnd => {
                self.selected_position = None;
                self.current_position = None;
                self.selected_position_type = None;
            }
            GalleryDesignerMsg::OnInvalidDrop(event) => {
                event.prevent_default();
                event.stop_propagation();
                self.selected_position = None;
                self.current_position = None;
                self.selected_position_type = None;
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
        html! {
            <Page>
                <div class="jinya-designer-gallery-designer__container">
                    <div ondragenter=self.link.callback(|event| GalleryDesignerMsg::OnFilesDragEnter(event)) ondragover=self.link.callback(|event| GalleryDesignerMsg::OnFilesDragOver(event)) ondrop=self.link.callback(|event| GalleryDesignerMsg::OnPositionDropOnFiles(event)) class="jinya-designer-gallery-designer__list jinya-designer-gallery-designer__list--file">
                        {for self.files.iter().enumerate().map(|(idx, item)| {
                            html! {
                                <img ondragend=self.link.callback(|_| GalleryDesignerMsg::OnDragEnd) ondragstart=self.link.callback(move |event| GalleryDesignerMsg::OnFileDrag(event, idx)) src=format!("{}{}", get_host(), &item.path) class="jinya-designer-gallery-designer__image jinya-designer-gallery-designer__item jinya-designer-gallery-designer__item--file" />
                            }
                        })}
                    </div>
                    <div ondrop=self.link.callback(|event| GalleryDesignerMsg::OnInvalidDrop(event)) ondragover=self.link.callback(|event| GalleryDesignerMsg::OnPositionsDragOver(event)) ondragenter=self.link.callback(|event| GalleryDesignerMsg::OnPositionsDragEnter(event)) class="jinya-designer-gallery-designer__list jinya-designer-gallery-designer__list--positions">
                        {for gallery_files.iter().enumerate().map(|(idx, item)| {
                            let drop_target_class = if self.current_position.is_some() {
                                if self.current_position.unwrap() == idx {
                                    "jinya-designer-gallery-position__drop-target jinya-designer-gallery-position__drop-target--drag-over"
                                } else {
                                    "jinya-designer-gallery-position__drop-target"
                                }
                            } else {
                                "jinya-designer-gallery-position__drop-target"
                            };
                            html! {
                                <div ondragover=self.link.callback(move |_| GalleryDesignerMsg::OnNewPositionDragOver(idx)) class="jinya-designer-gallery-position-container">
                                    <div class=drop_target_class ondrop=self.link.callback(|event| GalleryDesignerMsg::OnPositionDrop(event))>
                                        <span class="mdi mdi-image-outline jinya-designer-position__drop-target-icon"></span>
                                    </div>
                                    <img ondragend=self.link.callback(|_| GalleryDesignerMsg::OnDragEnd) ondragstart=self.link.callback(move |event| GalleryDesignerMsg::OnPositionDragStart(event, idx)) src=format!("{}{}", get_host(), &item.file.path) class=("jinya-designer-gallery-designer__image jinya-designer-gallery-designer__item jinya-designer-gallery-designer__item--position", self.get_active_item_class_if_selected_item_is_active(idx)) />
                                </div>
                            }
                        })}
                        {if self.current_position.is_some() {
                            let idx = self.gallery_files.len() + 1;
                            if self.current_position.unwrap() == self.gallery_files.len() + 1 {
                                html! {
                                    <div ondragover=self.link.callback(move |_| GalleryDesignerMsg::OnNewPositionDragOver(idx)) class="jinya-designer-gallery-position-container">
                                        <div class="jinya-designer-gallery-position__drop-target jinya-designer-gallery-position__drop-target--drag-over" ondrop=self.link.callback(|event| GalleryDesignerMsg::OnPositionDrop(event))>
                                            <span class="mdi mdi-image-outline jinya-designer-position__drop-target-icon"></span>
                                        </div>
                                    </div>
                                }
                            } else {
                                html! {
                                    <div ondragover=self.link.callback(move |_| GalleryDesignerMsg::OnNewPositionDragOver(idx)) class="jinya-designer-gallery-position-container">
                                        <div class="jinya-designer-gallery-position__drop-target" ondrop=self.link.callback(|event| GalleryDesignerMsg::OnPositionDrop(event))>
                                            <span class="mdi mdi-image-outline jinya-designer-position__drop-target-icon"></span>
                                        </div>
                                    </div>
                                }
                            }
                        } else {
                            let idx = self.gallery_files.len() + 1;
                            html! {
                                <div ondragover=self.link.callback(move |_| GalleryDesignerMsg::OnNewPositionDragOver(idx)) class="jinya-designer-gallery-position-container">
                                    <div class="jinya-designer-gallery-position__drop-target" ondrop=self.link.callback(|event| GalleryDesignerMsg::OnPositionDrop(event))>
                                        <span class="mdi mdi-image-outline jinya-designer-gallery-position__drop-target-icon"></span>
                                    </div>
                                </div>
                            }
                        }}
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

    fn get_last_position(&self) -> usize {
        let mut items = self.gallery_files.clone();
        items.sort_by(|a, b| a.position.cmp(&b.position));

        if items.last().is_some() {
            items.last().unwrap().position + 1
        } else {
            1
        }
    }
}
