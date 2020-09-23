use jinya_ui::layout::page::Page;
use jinya_ui::layout::split_view::SplitView;
use jinya_ui::widgets::tab::{TabControl, TabPage};
use jinya_ui::widgets::toast::Toast;
use web_sys::Node;
use yew::prelude::*;
use yew::services::fetch::*;
use yew::virtual_dom::VNode;

use crate::ajax::{AjaxError, get_host};
use crate::ajax::file_service::FileService;
use crate::ajax::gallery_service::GalleryService;
use crate::ajax::segment_service::SegmentService;
use crate::i18n::Translator;
use crate::models::file::File;
use crate::models::gallery::Gallery;
use crate::models::list_model::ListModel;
use crate::models::segment::Segment;

mod gallery_segment;

#[derive(PartialEq, Clone, Properties)]
pub struct SegmentPageDesignerPageProps {
    pub id: usize,
}

pub enum Msg {
    OnSegmentsLoaded(Result<Vec<Segment>, AjaxError>),
    OnFilesLoaded(Result<ListModel<File>, AjaxError>),
    OnGalleriesLoaded(Result<ListModel<Gallery>, AjaxError>),
}

pub struct SegmentPageDesignerPage {
    link: ComponentLink<Self>,
    id: usize,
    segments: Vec<Segment>,
    load_segments_task: Option<FetchTask>,
    load_files_task: Option<FetchTask>,
    load_galleries_task: Option<FetchTask>,
    segment_service: SegmentService,
    file_service: FileService,
    gallery_service: GalleryService,
    translator: Translator,
    files: Vec<File>,
    galleries: Vec<Gallery>,
}

impl Component for SegmentPageDesignerPage {
    type Message = Msg;
    type Properties = SegmentPageDesignerPageProps;

    fn create(props: Self::Properties, link: ComponentLink<Self>) -> Self {
        SegmentPageDesignerPage {
            link,
            id: props.id,
            segments: vec![],
            load_segments_task: None,
            load_files_task: None,
            load_galleries_task: None,
            segment_service: SegmentService::new(),
            file_service: FileService::new(),
            gallery_service: GalleryService::new(),
            translator: Translator::new(),
            files: vec![],
            galleries: vec![],
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
                    self.files = data.unwrap().items
                } else {
                    Toast::negative_toast(self.translator.translate("segment_pages.designer.error_load_files"))
                }
            }
            Msg::OnGalleriesLoaded(data) => {
                if data.is_ok() {
                    self.galleries = data.unwrap().items
                } else {
                    Toast::negative_toast(self.translator.translate("segment_pages.designer.error_load_galleries"))
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
                <SplitView
                    left={html! {
                        <TabControl pages=self.get_tab_pages() />
                    }}
                    right={html! {
                        {for self.segments.iter().enumerate().map(|(idx, item)| {
                            if item.gallery.is_some() {
                                let gallery = item.gallery.as_ref().unwrap();
                                html! {
                                    <div class="jinya-designer-segment jinya-designer-segment--gallery">
                                        <span class="jinya-designer-segment__title jinya-designer-segment__title--gallery">{self.translator.translate("segment_pages.designer.gallery")}</span>
                                        <span>{&gallery.name}</span>
                                    </div>
                                }
                            } else if item.file.is_some() {
                                let file = item.file.as_ref().unwrap();
                                let action = if item.action.is_some() {
                                    format!("segment_pages.designer.action.{}", item.action.as_ref().unwrap().to_string())
                                } else {
                                    "–".to_string()
                                };
                                let target = if item.target.is_some() {
                                    item.target.as_ref().unwrap().to_string()
                                } else {
                                    "–".to_string()
                                };
                                let script = if item.script.is_some() {
                                    item.script.as_ref().unwrap().to_string()
                                } else {
                                    "–".to_string()
                                };
                                html! {
                                    <div class="jinya-designer-segment jinya-designer-segment--file">
                                        <img class="jinya-designer-segment__image" src={format!("{}{}", get_host(), &file.path)} />
                                        <dl class="jinya-designer-segment__modifiers">
                                            <dt class="jinya-designer-segment__action-header">{self.translator.translate("segment_pages.designer.action")}</dt>
                                            <dd class="jinya-designer-segment__action-value">{self.translator.translate(action.as_str())}</dd>
                                            <dt class="jinya-designer-segment__target-header">{self.translator.translate("segment_pages.designer.target")}</dt>
                                            <dt class="jinya-designer-segment__target-value">{&target}</dt>
                                            <dt class="jinya-designer-segment__script-header">{self.translator.translate("segment_pages.designer.script")}</dt>
                                            <dt class="jinya-designer-segment__script-value"><pre>{&script}</pre></dt>
                                        </dl>
                                    </div>
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
                                    <div class="jinya-designer-segment jinya-designer-segment--html">
                                        <span class="jinya-designer-segment__title jinya-designer-segment__title--gallery">{self.translator.translate("segment_pages.designer.html")}</span>
                                        {vnode}
                                    </div>
                                }
                            } else {
                                html! {}
                            }
                        })}
                    }}
                />
            </Page>
        }
    }

    fn rendered(&mut self, first_render: bool) {
        if first_render {
            self.load_segments_task = Some(self.segment_service.get_segments(self.id, self.link.callback(|data| Msg::OnSegmentsLoaded(data))));
            self.load_files_task = Some(self.file_service.get_list("".to_string(), self.link.callback(|data| Msg::OnFilesLoaded(data))));
            self.load_galleries_task = Some(self.gallery_service.get_list("".to_string(), self.link.callback(|data| Msg::OnGalleriesLoaded(data))));
        }
    }
}

impl SegmentPageDesignerPage {
    fn get_tab_pages(&self) -> Vec<TabPage> {
        vec![
            TabPage::new(
                self.translator.translate("segment_pages.designer.tabs.files"),
                html! {
                    <div class="jinya-designer-segment__files">
                        {for self.files.iter().enumerate().map(|(idx, item)| {
                            html! {
                                <img class="jinya-designer-segment__file" src={format!("{}{}", get_host(), item.path)} />
                            }
                        })}
                    </div>
                },
                "files".to_string(),
            ),
            TabPage::new(
                self.translator.translate("segment_pages.designer.tabs.galleries"),
                html! {
                    <div class="jinya-designer-segment__items">
                        {for self.galleries.iter().enumerate().map(|(idx, item)| {
                            html! {
                                <span class="jinya-designer-segment__item">
                                    <span class="mdi mdi-menu mdi-24px"></span> {&item.name}
                                </span>
                            }
                        })}
                    </div>
                },
                "galleries".to_string(),
            ),
            TabPage::new(
                self.translator.translate("segment_pages.designer.tabs.other"),
                html! {
                    <div class="jinya-designer-segment__items">
                        <span class="jinya-designer-segment__item">
                            <span class="mdi mdi-menu mdi-24px"></span> {self.translator.translate("segment_pages.designer.tabs.other.formatted_text")}
                        </span>
                    </div>
                },
                "other".to_string(),
            ),
        ]
    }
}