use jinya_ui::layout::button_row::ButtonRow;
use jinya_ui::layout::page::Page;
use jinya_ui::layout::row::Row;
use jinya_ui::widgets::button::{Button, ButtonType};
use jinya_ui::widgets::dialog::confirmation::{ConfirmationDialog, DialogType};
use jinya_ui::widgets::form::checkbox::Checkbox;
use jinya_ui::widgets::form::dropdown::{Dropdown, DropdownItem};
use jinya_ui::widgets::form::input::Input;
use jinya_ui::widgets::toast::Toast;
use web_sys::Node;
use yew::agent::Dispatcher;
use yew::prelude::*;
use yew::services::fetch::*;
use yew::virtual_dom::VNode;

use crate::agents::menu_agent::{MenuAgent, MenuAgentRequest};
use crate::ajax::{AjaxError, get_host};
use crate::ajax::file_service::FileService;
use crate::ajax::gallery_service::GalleryService;
use crate::ajax::segment_service::SegmentService;
use crate::i18n::Translator;
use crate::models::file::File;
use crate::models::gallery::Gallery;
use crate::models::list_model::ListModel;
use crate::models::segment::Segment;
use crate::utils::TinyMce;

#[derive(PartialEq, Clone, Properties)]
pub struct SegmentPageDesignerPageProps {
    pub id: usize,
}

enum NewSegmentType {
    Gallery,
    File,
    Html,
}

pub enum Msg {
    OnSegmentsLoaded(Result<Vec<Segment>, AjaxError>),
    OnFilesLoaded(Result<ListModel<File>, AjaxError>),
    OnGalleriesLoaded(Result<ListModel<Gallery>, AjaxError>),
    OnEditSegment(MouseEvent, usize),
    OnDeleteSegment(MouseEvent, usize),
    OnDeleteApprove,
    OnDeleteDecline,
    OnDeleteRequestCompleted(Result<bool, AjaxError>),
    OnUpdateRequestCompleted(Result<bool, AjaxError>),
    OnGallerySelect(String),
    OnUpdateGallerySegment,
    OnDiscardUpdateGallerySegment,
    OnUpdateHtmlSegment,
    OnDiscardUpdateHtmlSegment,
    OnUpdateFileSegment,
    OnDiscardUpdateFileSegment,
    OnFileSelect(String),
    OnTargetChange(String),
    OnActionCheck,
    OnNewGalleryDragStart(DragEvent),
    OnNewFileDragStart(DragEvent),
    OnNewHtmlDragStart(DragEvent),
    OnSegmentsDragEnter(DragEvent),
    OnSegmentsDragOver(DragEvent),
    OnDragOverSegment(usize),
    OnNewSegmentDrop,
    OnAddRequestCompleted(Result<bool, AjaxError>),
}

pub struct SegmentPageDesignerPage {
    link: ComponentLink<Self>,
    id: usize,
    segments: Vec<Segment>,
    delete_segment_task: Option<FetchTask>,
    load_segments_task: Option<FetchTask>,
    load_files_task: Option<FetchTask>,
    load_galleries_task: Option<FetchTask>,
    update_gallery_segment_task: Option<FetchTask>,
    update_html_segment_task: Option<FetchTask>,
    update_file_segment_task: Option<FetchTask>,
    create_gallery_segment_task: Option<FetchTask>,
    create_html_segment_task: Option<FetchTask>,
    create_file_segment_task: Option<FetchTask>,
    segment_service: SegmentService,
    file_service: FileService,
    gallery_service: GalleryService,
    translator: Translator,
    files: Vec<DropdownItem>,
    galleries: Vec<DropdownItem>,
    segment_to_delete: Option<Segment>,
    segment_to_edit: Option<Segment>,
    edit_segment_gallery_name: Option<String>,
    edit_segment_file_name: Option<String>,
    fetched_galleries: Vec<Gallery>,
    fetched_files: Vec<File>,
    tiny_mce: Option<TinyMce>,
    menu_dispatcher: Dispatcher<MenuAgent>,
    edit_segment_file_action: bool,
    edit_segment_file_target: Option<String>,
    new_segment_type: Option<NewSegmentType>,
    current_segment: Option<usize>,
    is_new: bool,
}

impl Component for SegmentPageDesignerPage {
    type Message = Msg;
    type Properties = SegmentPageDesignerPageProps;

    fn create(props: Self::Properties, link: ComponentLink<Self>) -> Self {
        let menu_dispatcher = MenuAgent::dispatcher();
        let translator = Translator::new();

        SegmentPageDesignerPage {
            link,
            id: props.id,
            segments: vec![],
            delete_segment_task: None,
            load_segments_task: None,
            load_files_task: None,
            load_galleries_task: None,
            update_gallery_segment_task: None,
            update_html_segment_task: None,
            update_file_segment_task: None,
            create_gallery_segment_task: None,
            create_html_segment_task: None,
            create_file_segment_task: None,
            segment_service: SegmentService::new(),
            file_service: FileService::new(),
            gallery_service: GalleryService::new(),
            translator,
            files: vec![],
            galleries: vec![],
            segment_to_delete: None,
            segment_to_edit: None,
            edit_segment_gallery_name: None,
            edit_segment_file_name: None,
            fetched_galleries: vec![],
            fetched_files: vec![],
            tiny_mce: None,
            menu_dispatcher,
            edit_segment_file_action: false,
            edit_segment_file_target: None,
            new_segment_type: None,
            current_segment: None,
            is_new: false,
        }
    }

    fn update(&mut self, msg: Self::Message) -> bool {
        match msg {
            Msg::OnSegmentsLoaded(data) => {
                if data.is_ok() {
                    self.segments = data.unwrap()
                } else {
                    Toast::negative_toast(self.translator.translate("segment_pages.designer.error_load_segments"))
                }
            }
            Msg::OnFilesLoaded(data) => {
                if data.is_ok() {
                    let items = data.unwrap().items;
                    self.files = items.iter().map(|item| {
                        DropdownItem {
                            value: item.name.to_string(),
                            text: item.name.to_string(),
                        }
                    }).collect();
                    self.fetched_files = items;
                    if self.is_new {
                        self.edit_segment_file_name = Some(self.files.first().unwrap().value.to_string());
                    }
                } else {
                    Toast::negative_toast(self.translator.translate("segment_pages.designer.error_load_files"))
                }
            }
            Msg::OnGalleriesLoaded(data) => {
                if data.is_ok() {
                    let items = data.unwrap().items;
                    self.galleries = items.iter().map(|item| {
                        DropdownItem {
                            value: item.name.to_string(),
                            text: item.name.to_string(),
                        }
                    }).collect();
                    self.fetched_galleries = items;
                    if self.is_new {
                        self.edit_segment_gallery_name = Some(self.galleries.first().unwrap().value.to_string());
                    }
                } else {
                    Toast::negative_toast(self.translator.translate("segment_pages.designer.error_load_galleries"))
                }
            }
            Msg::OnDeleteSegment(event, idx) => {
                event.prevent_default();
                self.segment_to_delete = Some(self.segments[idx].clone());
            }
            Msg::OnDeleteApprove => self.delete_segment_task = Some(self.segment_service.delete_segment(self.id, self.segment_to_delete.as_ref().unwrap().position, self.link.callback(|result| Msg::OnDeleteRequestCompleted(result)))),
            Msg::OnDeleteDecline => self.segment_to_delete = None,
            Msg::OnDeleteRequestCompleted(result) => {
                self.segment_to_delete = None;
                if result.is_err() {
                    Toast::negative_toast(self.translator.translate("segment_pages.designer.delete.failed"))
                } else {
                    self.load_segments_task = Some(self.segment_service.get_segments(self.id, self.link.callback(|data| Msg::OnSegmentsLoaded(data))));
                }
            }
            Msg::OnEditSegment(event, idx) => {
                event.prevent_default();
                self.segment_to_edit = Some(self.segments[idx].clone());
                let segment = self.segment_to_edit.as_ref().unwrap();
                if self.tiny_mce.is_some() {
                    self.tiny_mce.as_ref().unwrap().destroy_editor();
                }
                if segment.gallery.is_some() {
                    let gallery = segment.gallery.as_ref().unwrap();
                    self.edit_segment_gallery_name = Some(gallery.name.to_string());
                    self.load_galleries_task = Some(self.gallery_service.get_list("".to_string(), self.link.callback(|result| Msg::OnGalleriesLoaded(result))));
                } else if segment.file.is_some() {
                    let file = segment.file.as_ref().unwrap();
                    self.edit_segment_file_name = Some(file.name.to_string());
                    self.edit_segment_file_target = segment.target.clone();
                    self.edit_segment_file_action = segment.action.is_some() && segment.action.as_ref().unwrap().eq("link");
                    self.load_files_task = Some(self.file_service.get_list("".to_string(), self.link.callback(|result| Msg::OnFilesLoaded(result))));
                }
            }
            Msg::OnGallerySelect(item) => self.edit_segment_gallery_name = Some(item),
            Msg::OnUpdateGallerySegment => {
                let segment_to_edit = self.segment_to_edit.as_ref().unwrap();
                let name = self.edit_segment_gallery_name.as_ref().unwrap();
                let gallery = self.fetched_galleries.iter().find(move |item| item.name.eq(name));
                if gallery.is_some() {
                    if self.is_new {
                        self.create_gallery_segment_task = Some(self.segment_service.create_gallery_segment(self.id, self.current_segment.unwrap(), gallery.unwrap().id, self.link.callback(|result| Msg::OnAddRequestCompleted(result))));
                    } else {
                        self.update_gallery_segment_task = Some(self.segment_service.update_gallery_segment(self.id, segment_to_edit.position, gallery.unwrap().id, self.link.callback(|result| Msg::OnUpdateRequestCompleted(result))));
                    }
                }
            }
            Msg::OnDiscardUpdateGallerySegment => {
                if self.is_new {
                    let segment_position = self.segment_to_edit.as_ref().unwrap().position.clone();
                    let position = self.segments.iter().position(|item| item.position == segment_position);
                    if position.is_some() {
                        self.segments.remove(position.unwrap());
                    }
                }
                self.segment_to_edit = None;
                self.is_new = false;
                self.current_segment = None;
                self.new_segment_type = None;
            }
            Msg::OnUpdateRequestCompleted(result) => {
                if result.is_ok() {
                    self.segment_to_edit = None;
                    self.edit_segment_gallery_name = None;
                    self.load_segments_task = Some(self.segment_service.get_segments(self.id, self.link.callback(|data| Msg::OnSegmentsLoaded(data))));
                    if self.tiny_mce.is_some() {
                        self.tiny_mce.as_ref().unwrap().destroy_editor();
                        self.tiny_mce = None;
                    }
                } else {
                    Toast::negative_toast(self.translator.translate("segment_pages.designer.error_change_segment_failed"))
                }
            }
            Msg::OnUpdateHtmlSegment => {
                let segment_to_edit = self.segment_to_edit.as_ref().unwrap();
                let content = self.tiny_mce.as_ref().unwrap().get_content();
                if self.is_new {
                    self.create_html_segment_task = Some(self.segment_service.create_html_segment(self.id, self.current_segment.unwrap(), content, self.link.callback(|result| Msg::OnAddRequestCompleted(result))));
                } else {
                    self.update_html_segment_task = Some(self.segment_service.update_html_segment(self.id, segment_to_edit.position, content, self.link.callback(|result| Msg::OnUpdateRequestCompleted(result))));
                }
            }
            Msg::OnDiscardUpdateHtmlSegment => {
                if self.is_new {
                    let segment_position = self.segment_to_edit.as_ref().unwrap().position.clone();
                    let position = self.segments.iter().position(|item| item.position == segment_position);
                    if position.is_some() {
                        self.segments.remove(position.unwrap());
                    }
                }
                self.segment_to_edit = None;
                if self.tiny_mce.is_some() {
                    self.tiny_mce.as_ref().unwrap().destroy_editor();
                    self.tiny_mce = None;
                }
                self.is_new = false;
                self.current_segment = None;
                self.new_segment_type = None;
            }
            Msg::OnUpdateFileSegment => {
                let segment_to_edit = self.segment_to_edit.as_ref().unwrap();
                let name = self.edit_segment_file_name.as_ref().unwrap();
                let target = if self.edit_segment_file_action {
                    self.edit_segment_file_target.as_ref().unwrap().to_string()
                } else {
                    "".to_string()
                };
                let file = self.fetched_files.iter().find(move |item| item.name.eq(name));
                if file.is_some() {
                    if self.is_new {
                        self.create_file_segment_task = Some(self.segment_service.create_file_segment(self.id, self.current_segment.unwrap(), file.unwrap().id, !target.is_empty(), target, self.link.callback(|result| Msg::OnAddRequestCompleted(result))));
                    } else {
                        self.update_file_segment_task = Some(self.segment_service.update_file_segment(self.id, segment_to_edit.position, file.unwrap().id, self.edit_segment_file_action, target, self.link.callback(|result| Msg::OnUpdateRequestCompleted(result))));
                    }
                }
            }
            Msg::OnDiscardUpdateFileSegment => {
                if self.is_new {
                    let segment_position = self.segment_to_edit.as_ref().unwrap().position.clone();
                    let position = self.segments.iter().position(|item| item.position == segment_position);
                    if position.is_some() {
                        self.segments.remove(position.unwrap());
                    }
                }
                self.segment_to_edit = None;
                self.is_new = false;
                self.current_segment = None;
                self.new_segment_type = None;
            }
            Msg::OnFileSelect(value) => self.edit_segment_file_name = Some(value),
            Msg::OnTargetChange(value) => self.edit_segment_file_target = Some(value),
            Msg::OnActionCheck => {
                self.edit_segment_file_action = !self.edit_segment_file_action;
                if self.edit_segment_file_action {
                    self.edit_segment_file_target = Some("".to_string());
                } else {
                    self.edit_segment_file_target = None;
                }
            }
            Msg::OnNewGalleryDragStart(event) => {
                let data_transfer = event.data_transfer();
                if data_transfer.is_some() {
                    let data_transfer_unwrapped = data_transfer.unwrap();
                    data_transfer_unwrapped.set_drop_effect("copy");
                    data_transfer_unwrapped.set_effect_allowed("copy");
                    self.new_segment_type = Some(NewSegmentType::Gallery);
                }
            }
            Msg::OnNewFileDragStart(event) => {
                let data_transfer = event.data_transfer();
                if data_transfer.is_some() {
                    let data_transfer_unwrapped = data_transfer.unwrap();
                    data_transfer_unwrapped.set_drop_effect("copy");
                    data_transfer_unwrapped.set_effect_allowed("copy");
                    self.new_segment_type = Some(NewSegmentType::File);
                }
            }
            Msg::OnNewHtmlDragStart(event) => {
                let data_transfer = event.data_transfer();
                if data_transfer.is_some() {
                    let data_transfer_unwrapped = data_transfer.unwrap();
                    data_transfer_unwrapped.set_drop_effect("copy");
                    data_transfer_unwrapped.set_effect_allowed("copy");
                    self.new_segment_type = Some(NewSegmentType::Html);
                }
            }
            Msg::OnSegmentsDragEnter(event) => {
                event.prevent_default();
                event.stop_propagation();
            }
            Msg::OnSegmentsDragOver(event) => {
                event.prevent_default();
                event.stop_propagation();
            }
            Msg::OnDragOverSegment(idx) => self.current_segment = Some(idx),
            Msg::OnNewSegmentDrop => {
                let current_segment = self.current_segment.unwrap();
                let new_segment = match self.new_segment_type.as_ref().unwrap() {
                    NewSegmentType::Gallery => {
                        self.edit_segment_gallery_name = None;
                        Segment::gallery_segment(current_segment, Gallery::empty_gallery())
                    }
                    NewSegmentType::File => {
                        self.edit_segment_file_name = None;
                        Segment::file_segment(current_segment, File::from_name("".to_string()), None, None, None)
                    }
                    NewSegmentType::Html => {
                        Segment::html_segment(current_segment, "".to_string())
                    }
                };
                if current_segment > self.segments.len() {
                    self.segments.push(new_segment.clone());
                } else {
                    self.segments.insert(current_segment, new_segment.clone());
                }
                self.segment_to_edit = Some(new_segment.clone());

                match self.new_segment_type.as_ref().unwrap() {
                    NewSegmentType::Gallery => {
                        self.load_galleries_task = Some(self.gallery_service.get_list("".to_string(), self.link.callback(|result| Msg::OnGalleriesLoaded(result))));
                        self.edit_segment_gallery_name = Some("".to_string());
                    }
                    NewSegmentType::File => {
                        self.load_files_task = Some(self.file_service.get_list("".to_string(), self.link.callback(|result| Msg::OnFilesLoaded(result))));
                        self.edit_segment_file_target = Some("".to_string());
                        self.edit_segment_file_action = false;
                        self.edit_segment_file_name = Some("".to_string());
                    }
                    NewSegmentType::Html => {}
                }

                self.new_segment_type = None;
                self.is_new = true;
            }
            Msg::OnAddRequestCompleted(result) => {
                if result.is_ok() {
                    self.segment_to_edit = None;
                    self.current_segment = None;
                    self.edit_segment_gallery_name = None;
                    self.is_new = false;
                    self.load_segments_task = Some(self.segment_service.get_segments(self.id, self.link.callback(|data| Msg::OnSegmentsLoaded(data))));
                    if self.tiny_mce.is_some() {
                        self.tiny_mce.as_ref().unwrap().destroy_editor();
                        self.tiny_mce = None;
                    }
                } else {
                    Toast::negative_toast(self.translator.translate("segment_pages.designer.error_create_segment_failed"))
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
        html! {
            <Page>
                <div class="jinya-designer-segment-page-designer__container">
                    <div class="jinya-designer-segment-page-designer__list jinya-designer-segment-page-designer__list--new-items">
                        <div draggable=true ondragstart=self.link.callback(move |event| Msg::OnNewGalleryDragStart(event)) class="jinya-designer-segment__list-item jinya-designer-segment__list-item--gallery">
                            <span class="mdi mdi-drag-horizontal-variant mdi-24px"></span> {self.translator.translate("segment_pages.designer.gallery")}
                        </div>
                        <div draggable=true ondragstart=self.link.callback(move |event| Msg::OnNewFileDragStart(event)) class="jinya-designer-segment__list-item jinya-designer-segment__list-item--file">
                            <span class="mdi mdi-drag-horizontal-variant mdi-24px"></span> {self.translator.translate("segment_pages.designer.file")}
                        </div>
                        <div draggable=true ondragstart=self.link.callback(move |event| Msg::OnNewHtmlDragStart(event)) class="jinya-designer-segment__list-item jinya-designer-segment__list-item--html">
                            <span class="mdi mdi-drag-horizontal-variant mdi-24px"></span> {self.translator.translate("segment_pages.designer.html")}
                        </div>
                    </div>
                    <div ondragover=self.link.callback(|event| Msg::OnSegmentsDragOver(event)) ondragenter=self.link.callback(|event| Msg::OnSegmentsDragEnter(event)) class="jinya-designer-segment-page-designer__list jinya-designer-segment-page-designer__list--segments">
                        {for self.segments.iter().enumerate().map(|(idx, item)| {
                            let drop_target_class = if self.current_segment.is_some() {
                                if self.current_segment.unwrap() == idx {
                                    "jinya-designer-segment__drop-target jinya-designer-segment__drop-target--drag-over"
                                } else {
                                    "jinya-designer-segment__drop-target"
                                }
                            } else {
                                "jinya-designer-segment__drop-target"
                            };
                            if item.gallery.is_some() {
                                let gallery = item.gallery.as_ref().unwrap();
                                html! {
                                    <div ondragover=self.link.callback(move |event| Msg::OnDragOverSegment(idx)) class="jinya-designer-segment-container">
                                        <div ondrop=self.link.callback(|event| Msg::OnNewSegmentDrop) class=drop_target_class>
                                            <span class="mdi mdi-plus jinya-designer-segment__drop-target-icon"></span>
                                        </div>
                                        <div draggable=true class="jinya-designer-segment jinya-designer-segment--gallery">
                                            <a onclick=self.link.callback(move |event| Msg::OnDeleteSegment(event, idx)) class="mdi mdi-delete jinya-designer-segment__button jinya-designer-segment__button--negative"></a>
                                            <a onclick=self.link.callback(move |event| Msg::OnEditSegment(event, idx)) class="mdi mdi-pencil jinya-designer-segment__button jinya-designer-segment__button--primary"></a>
                                            <span class="jinya-designer-segment__title jinya-designer-segment__title--gallery">{self.translator.translate("segment_pages.designer.gallery")}</span>
                                            {if self.segment_to_edit.is_some() && self.segment_to_edit.as_ref().unwrap().id == item.id {
                                                html! {
                                                    <>
                                                        <Row>
                                                            <Dropdown
                                                                label=self.translator.translate("segment_pages.designer.gallery")
                                                                on_select=self.link.callback(|value| Msg::OnGallerySelect(value))
                                                                items=&self.galleries
                                                                value=self.edit_segment_gallery_name.as_ref().unwrap()
                                                            />
                                                        </Row>
                                                        <ButtonRow>
                                                            <Button label=self.translator.translate("segment_pages.designer.action_save_segment") button_type=ButtonType::Primary on_click=self.link.callback(|_| Msg::OnUpdateGallerySegment)></Button>
                                                            <Button label=self.translator.translate("segment_pages.designer.action_discard_segment") button_type=ButtonType::Secondary on_click=self.link.callback(|_| Msg::OnDiscardUpdateGallerySegment)></Button>
                                                        </ButtonRow>
                                                    </>
                                                }
                                            } else {
                                                html! {
                                                    <span>{&gallery.name}</span>
                                                }
                                            }}
                                        </div>
                                    </div>
                                }
                            } else if item.file.is_some() {
                                if self.segment_to_edit.is_some() && self.segment_to_edit.as_ref().unwrap().id == item.id {
                                    html! {
                                        <div draggable=true class="jinya-designer-segment jinya-designer-segment--gallery">
                                            <a onclick=self.link.callback(move |event| Msg::OnDeleteSegment(event, idx)) class="mdi mdi-delete jinya-designer-segment__button jinya-designer-segment__button--negative"></a>
                                            <a onclick=self.link.callback(move |event| Msg::OnEditSegment(event, idx)) class="mdi mdi-pencil jinya-designer-segment__button jinya-designer-segment__button--primary"></a>
                                            <Row>
                                                <Dropdown
                                                    label=self.translator.translate("segment_pages.designer.file")
                                                    on_select=self.link.callback(|value| Msg::OnFileSelect(value))
                                                    items=&self.files
                                                    value=self.edit_segment_file_name.as_ref().unwrap()
                                                />
                                            </Row>
                                            <Row>
                                                <Checkbox
                                                    label=self.translator.translate("segment_pages.designer.has_link")
                                                    on_change=self.link.callback(|_| Msg::OnActionCheck)
                                                    checked=self.edit_segment_file_action
                                                />
                                            </Row>
                                            {if self.edit_segment_file_action {
                                                html! {
                                                    <Row>
                                                        <Input
                                                            label=self.translator.translate("segment_pages.designer.target")
                                                            on_input=self.link.callback(|value| Msg::OnTargetChange(value))
                                                            value=self.edit_segment_file_target.as_ref().unwrap()
                                                        />
                                                    </Row>
                                                }
                                            } else {
                                                html! {}
                                            }}
                                            <ButtonRow>
                                                <Button label=self.translator.translate("segment_pages.designer.action_save_segment") button_type=ButtonType::Primary on_click=self.link.callback(|_| Msg::OnUpdateFileSegment)></Button>
                                                <Button label=self.translator.translate("segment_pages.designer.action_discard_segment") button_type=ButtonType::Secondary on_click=self.link.callback(|_| Msg::OnDiscardUpdateFileSegment)></Button>
                                            </ButtonRow>
                                        </div>
                                    }
                                } else {
                                    let file = item.file.as_ref().unwrap();
                                    let action = if item.action.is_some() {
                                        self.translator.translate(format!("segment_pages.designer.action.{}", item.action.as_ref().unwrap().to_string()).as_str())
                                    } else {
                                        "–".to_string()
                                    };
                                    let target = if item.target.is_some() {
                                        item.target.as_ref().unwrap().to_string()
                                    } else {
                                        "–".to_string()
                                    };
                                    html! {
                                        <div ondragover=self.link.callback(move |event| Msg::OnDragOverSegment(idx)) class="jinya-designer-segment-container">
                                            <div ondrop=self.link.callback(|event| Msg::OnNewSegmentDrop) class=drop_target_class>
                                                <span class="mdi mdi-plus jinya-designer-segment__drop-target-icon"></span>
                                            </div>
                                            <div draggable=true class="jinya-designer-segment jinya-designer-segment--file">
                                                <img class="jinya-designer-segment__image" src={format!("{}{}", get_host(), &file.path)} />
                                                <div class="jinya-designer-segment__modifiers">
                                                    <a onclick=self.link.callback(move |event| Msg::OnDeleteSegment(event, idx)) class="mdi mdi-delete jinya-designer-segment__button jinya-designer-segment__button--negative"></a>
                                                    <a onclick=self.link.callback(move |event| Msg::OnEditSegment(event, idx)) class="mdi mdi-pencil jinya-designer-segment__button jinya-designer-segment__button--primary"></a>
                                                    <dl>
                                                        <dt class="jinya-designer-segment__action-header">{self.translator.translate("segment_pages.designer.action")}</dt>
                                                        <dd class="jinya-designer-segment__action-value">{&action}</dd>
                                                        <dt class="jinya-designer-segment__target-header">{self.translator.translate("segment_pages.designer.target")}</dt>
                                                        <dd class="jinya-designer-segment__target-value">{&target}</dd>
                                                    </dl>
                                                </div>
                                            </div>
                                        </div>
                                    }
                                }
                            } else if item.html.is_some() {
                                let html = item.html.as_ref().unwrap();
                                let content = {
                                    let div = web_sys::window()
                                        .unwrap()
                                        .document()
                                        .unwrap()
                                        .create_element("div")
                                        .unwrap();
                                    div.set_inner_html(html);
                                    div
                                };
                                let node = Node::from(content);
                                let vnode = VNode::VRef(node);
                                html! {
                                    <div ondragover=self.link.callback(move |event| Msg::OnDragOverSegment(idx)) class="jinya-designer-segment-container">
                                        <div ondrop=self.link.callback(|event| Msg::OnNewSegmentDrop) class=drop_target_class>
                                            <span class="mdi mdi-plus jinya-designer-segment__drop-target-icon"></span>
                                        </div>
                                        <div class="jinya-designer-segment jinya-designer-segment--html">
                                            <a onclick=self.link.callback(move |event| Msg::OnDeleteSegment(event, idx)) class="mdi mdi-delete jinya-designer-segment__button jinya-designer-segment__button--negative"></a>
                                            <a onclick=self.link.callback(move |event| Msg::OnEditSegment(event, idx)) class="mdi mdi-pencil jinya-designer-segment__button jinya-designer-segment__button--primary"></a>
                                            <span class="jinya-designer-segment__title jinya-designer-segment__title--gallery">{self.translator.translate("segment_pages.designer.html")}</span>
                                            {if self.segment_to_edit.is_some() && self.segment_to_edit.as_ref().unwrap().id == item.id {
                                                html! {
                                                    <>
                                                        <div id="formatted-text-editor"></div>
                                                        <ButtonRow>
                                                            <Button label=self.translator.translate("segment_pages.designer.action_save_segment") button_type=ButtonType::Primary on_click=self.link.callback(|_| Msg::OnUpdateHtmlSegment)></Button>
                                                            <Button label=self.translator.translate("segment_pages.designer.action_discard_segment") button_type=ButtonType::Secondary on_click=self.link.callback(|_| Msg::OnDiscardUpdateHtmlSegment)></Button>
                                                        </ButtonRow>
                                                    </>
                                                }
                                            } else {
                                                html! {
                                                    {vnode}
                                                }
                                            }}
                                        </div>
                                    </div>
                                }
                            } else {
                                html! {}
                            }
                        })}
                        {if self.current_segment.is_some() {
                            let idx = self.segments.len() + 1;
                            if self.current_segment.unwrap() == self.segments.len() + 1 {
                                html! {
                                    <div ondragover=self.link.callback(move |event| Msg::OnDragOverSegment(idx)) class="jinya-designer-segment-container">
                                        <div class="jinya-designer-segment__drop-target jinya-designer-segment__drop-target--drag-over" ondrop=self.link.callback(|event| Msg::OnNewSegmentDrop)>
                                            <span class="mdi mdi-plus jinya-designer-segment__drop-target-icon"></span>
                                        </div>
                                    </div>
                                }
                            } else {
                                html! {
                                    <div ondragover=self.link.callback(move |event| Msg::OnDragOverSegment(idx)) class="jinya-designer-segment-container">
                                        <div class="jinya-designer-segment__drop-target" ondrop=self.link.callback(|event| Msg::OnNewSegmentDrop)>
                                            <span class="mdi mdi-plus jinya-designer-segment__drop-target-icon"></span>
                                        </div>
                                    </div>
                                }
                            }
                        } else {
                            let idx = self.segments.len() + 1;
                            log::info!("{}", idx);
                            html! {
                                <div ondragover=self.link.callback(move |event| Msg::OnDragOverSegment(idx)) class="jinya-designer-segment-container">
                                    <div class="jinya-designer-segment__drop-target" ondrop=self.link.callback(|event| Msg::OnNewSegmentDrop)>
                                        <span class="mdi mdi-plus jinya-designer-segment__drop-target-icon"></span>
                                    </div>
                                </div>
                            }
                        }}
                    </div>
                </div>
                {if self.segment_to_delete.is_some() {
                    html! {
                        <ConfirmationDialog
                            title=self.translator.translate("segment_pages.designer.delete.title")
                            dialog_type=DialogType::Negative
                            message=self.translator.translate("segment_pages.designer.delete.content")
                            decline_label=self.translator.translate("segment_pages.designer.delete.decline")
                            approve_label=self.translator.translate("segment_pages.designer.delete.approve")
                            on_approve=self.link.callback(|_| Msg::OnDeleteApprove)
                            on_decline=self.link.callback(|_| Msg::OnDeleteDecline)
                            is_open=self.segment_to_delete.is_some()
                        />
                    }
                } else {
                    html! {}
                }}
            </Page>
        }
    }

    fn rendered(&mut self, first_render: bool) {
        if first_render {
            self.menu_dispatcher.send(MenuAgentRequest::ChangeTitle(self.translator.translate("app.menu.content.pages.segment_pages")));
            self.menu_dispatcher.send(MenuAgentRequest::HideSearch);
            self.load_segments_task = Some(self.segment_service.get_segments(self.id, self.link.callback(|data| Msg::OnSegmentsLoaded(data))));
        }

        if self.segment_to_edit.is_some() {
            let segment = self.segment_to_edit.as_ref().unwrap();

            if segment.html.is_some() {
                let text = segment.html.as_ref().unwrap();
                let editor = TinyMce::new("formatted-text-editor".to_string());
                editor.init_tiny_mce(text.to_string());
                self.tiny_mce = Some(editor);
            }
        }
    }
}