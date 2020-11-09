use jinya_ui::layout::row::Row;
use jinya_ui::widgets::dialog::content::ContentDialog;
use jinya_ui::widgets::form::checkbox::Checkbox;
use jinya_ui::widgets::form::dropdown::{Dropdown, DropdownItem};
use jinya_ui::widgets::form::input::{Input, InputState};
use jinya_ui::widgets::toast::Toast;
use yew::{Component, ComponentLink, Html};
use yew::prelude::*;
use yew::services::fetch::FetchTask;

use crate::ajax::AjaxError;
use crate::ajax::artist_service::ArtistService;
use crate::ajax::gallery_service::GalleryService;
use crate::ajax::segment_page_service::SegmentPageService;
use crate::ajax::simple_page_service::SimplePageService;
use crate::i18n::*;
use crate::models::artist::Artist;
use crate::models::gallery::Gallery;
use crate::models::list_model::ListModel;
use crate::models::menu_item::{MenuItem, SaveMenuItem};
use crate::models::segment_page::SegmentPage;
use crate::models::simple_page::SimplePage;

#[derive(Clone, PartialEq)]
pub enum SettingsDialogType {
    Group,
    Link,
    Gallery,
    Page,
    SegmentPage,
    ArtistProfile,
}

pub struct SettingsDialog {
    link: ComponentLink<Self>,
    translator: Translator,
    on_save_changes: Callback<SaveMenuItem>,
    on_discard_changes: Callback<()>,
    highlighted: bool,
    title_input_state: InputState,
    title_validation_message: String,
    title: String,
    route_input_state: InputState,
    route_validation_message: String,
    route: Option<String>,
    is_open: bool,
    menu_item: MenuItem,
    dialog_type: SettingsDialogType,
    dropdown_item: Option<String>,
    item: Option<usize>,
    pages: Vec<SimplePage>,
    segment_pages: Vec<SegmentPage>,
    artists: Vec<Artist>,
    galleries: Vec<Gallery>,
    dropdown_items: Vec<DropdownItem>,
    artist_service: ArtistService,
    gallery_service: GalleryService,
    simple_page_service: SimplePageService,
    segment_page_service: SegmentPageService,
    load_items_task: Option<FetchTask>,
}

#[derive(Clone, PartialEq, Properties)]
pub struct SettingsDialogProps {
    pub on_save_changes: Callback<SaveMenuItem>,
    pub on_discard_changes: Callback<()>,
    pub is_open: bool,
    pub menu_item: MenuItem,
    pub dialog_type: SettingsDialogType,
}

pub enum SettingsDialogMsg {
    OnRouteInput(String),
    OnTitleInput(String),
    OnHighlightedInput,
    OnAddPrimary,
    OnAddSecondary,
    OnItemSelected(String),
    OnGalleriesLoaded(Result<ListModel<Gallery>, AjaxError>),
    OnSimplePagesLoaded(Result<ListModel<SimplePage>, AjaxError>),
    OnSegmentPagesLoaded(Result<ListModel<SegmentPage>, AjaxError>),
    OnProfilesLoaded(Result<ListModel<Artist>, AjaxError>),
}

impl Component for SettingsDialog {
    type Message = SettingsDialogMsg;
    type Properties = SettingsDialogProps;

    fn create(props: Self::Properties, link: ComponentLink<Self>) -> Self {
        let translator = Translator::new();

        SettingsDialog {
            link,
            translator,
            on_save_changes: props.on_save_changes,
            on_discard_changes: props.on_discard_changes,
            highlighted: props.menu_item.highlighted,
            title_input_state: InputState::Default,
            title_validation_message: "".to_string(),
            title: props.menu_item.title.clone(),
            route_input_state: InputState::Default,
            route_validation_message: "".to_string(),
            route: if props.dialog_type == SettingsDialogType::Group {
                None
            } else if let Some(route) = props.menu_item.clone().route {
                Some(route)
            } else {
                Some("".to_string())
            },
            is_open: props.is_open,
            menu_item: props.menu_item,
            dialog_type: props.dialog_type,
            dropdown_item: Some("".to_string()),
            item: None,
            pages: vec![],
            segment_pages: vec![],
            artists: vec![],
            galleries: vec![],
            dropdown_items: vec![],
            artist_service: ArtistService::new(),
            gallery_service: GalleryService::new(),
            simple_page_service: SimplePageService::new(),
            segment_page_service: SegmentPageService::new(),
            load_items_task: None,
        }
    }

    fn update(&mut self, msg: Self::Message) -> bool {
        match msg {
            SettingsDialogMsg::OnTitleInput(value) => {
                self.title = value;
                self.title_input_state = if self.title.is_empty() {
                    self.title_validation_message = self.translator.translate("menus.designer.settings.error_title_empty");
                    InputState::Negative
                } else {
                    self.title_validation_message = "".to_string();
                    InputState::Default
                };
            }
            SettingsDialogMsg::OnHighlightedInput => self.highlighted = !self.highlighted,
            SettingsDialogMsg::OnAddPrimary => {
                let item = if !self.title.is_empty() && self.route.is_some() {
                    Some(match self.dialog_type {
                        SettingsDialogType::Link => SaveMenuItem::new_link(self.title.clone(), self.highlighted, self.route.clone().unwrap()),
                        SettingsDialogType::Gallery => SaveMenuItem::new_gallery(self.title.clone(), self.highlighted, self.route.clone().unwrap(), self.item.unwrap()),
                        SettingsDialogType::Page => SaveMenuItem::new_page(self.title.clone(), self.highlighted, self.route.clone().unwrap(), self.item.unwrap()),
                        SettingsDialogType::SegmentPage => SaveMenuItem::new_segment_page(self.title.clone(), self.highlighted, self.route.clone().unwrap(), self.item.unwrap()),
                        SettingsDialogType::ArtistProfile => SaveMenuItem::new_profile(self.title.clone(), self.highlighted, self.route.clone().unwrap(), self.item.unwrap()),
                        _ => unreachable!(),
                    })
                } else if !self.title.is_empty() && self.dialog_type == SettingsDialogType::Group {
                    Some(match self.dialog_type {
                        SettingsDialogType::Group => SaveMenuItem::new_group(self.title.clone(), self.highlighted),
                        _ => unreachable!(),
                    })
                } else {
                    None
                };
                if let Some(item) = item {
                    self.on_save_changes.emit(item);
                }
            }
            SettingsDialogMsg::OnAddSecondary => self.on_discard_changes.emit(()),
            SettingsDialogMsg::OnRouteInput(value) => {
                self.route = Some(value.clone());
                self.route_input_state = if self.dialog_type != SettingsDialogType::Group && value.is_empty() {
                    self.route_validation_message = self.translator.translate("menus.designer.settings.error_route_empty");
                    InputState::Negative
                } else {
                    self.route_validation_message = "".to_string();
                    InputState::Default
                };
            }
            SettingsDialogMsg::OnItemSelected(value) => {
                self.dropdown_item = Some(value.clone());
                let search_value = value;
                match self.dialog_type {
                    SettingsDialogType::Gallery => self.item = self.galleries.iter().find_map(|item| if item.name == search_value { Some(item.id) } else { None }),
                    SettingsDialogType::Page => self.item = self.pages.iter().find_map(|item| if item.title == search_value { Some(item.id) } else { None }),
                    SettingsDialogType::SegmentPage => self.item = self.segment_pages.iter().find_map(|item| if item.name == search_value { Some(item.id) } else { None }),
                    SettingsDialogType::ArtistProfile => self.item = self.artists.iter().find_map(|item| if item.email == search_value { Some(item.id) } else { None }),
                    _ => unreachable!()
                }
            }
            SettingsDialogMsg::OnGalleriesLoaded(result) => if let Ok(galleries) = result {
                self.dropdown_items = galleries.items.iter().map(|item| DropdownItem { value: item.name.clone(), text: item.name.clone() }).collect();
                self.galleries = galleries.items;
                if let Some(gallery) = self.menu_item.clone().gallery {
                    self.dropdown_item = Some(gallery.name.clone());
                    self.item = self.galleries.iter().find_map(|item| if item.name == gallery.name { Some(item.id) } else { None })
                } else if let Some(gallery) = self.galleries.first() {
                    self.dropdown_item = Some(gallery.clone().name);
                    self.item = self.galleries.iter().find_map(|item| if item.name == gallery.clone().name { Some(item.id) } else { None })
                }
            } else {
                Toast::negative_toast(self.translator.translate("menus.designer.settings.error_could_not_load_galleries"))
            },
            SettingsDialogMsg::OnSimplePagesLoaded(result) => if let Ok(pages) = result {
                self.dropdown_items = pages.items.iter().map(|item| DropdownItem { value: item.title.clone(), text: item.title.clone() }).collect();
                self.pages = pages.items;
                if let Some(page) = self.menu_item.clone().page {
                    self.dropdown_item = Some(page.title.clone());
                    self.item = self.pages.iter().find_map(|item| if item.title == page.title { Some(item.id) } else { None })
                } else if let Some(page) = self.pages.first() {
                    self.dropdown_item = Some(page.clone().title);
                    self.item = self.pages.iter().find_map(|item| if item.title == page.clone().title { Some(item.id) } else { None })
                }
            } else {
                Toast::negative_toast(self.translator.translate("menus.designer.settings.error_could_not_load_simple_pages"))
            },
            SettingsDialogMsg::OnSegmentPagesLoaded(result) => if let Ok(pages) = result {
                self.dropdown_items = pages.items.iter().map(|item| DropdownItem { value: item.name.clone(), text: item.name.clone() }).collect();
                self.segment_pages = pages.items;
                if let Some(page) = self.menu_item.clone().segment_page {
                    self.dropdown_item = Some(page.name.clone());
                    self.item = self.segment_pages.iter().find_map(|item| if item.name == page.name { Some(item.id) } else { None })
                } else if let Some(page) = self.segment_pages.first() {
                    self.dropdown_item = Some(page.clone().name);
                    self.item = self.segment_pages.iter().find_map(|item| if item.name == page.clone().name { Some(item.id) } else { None })
                }
            } else {
                Toast::negative_toast(self.translator.translate("menus.designer.settings.error_could_not_load_segment_pages"))
            },
            SettingsDialogMsg::OnProfilesLoaded(result) => if let Ok(artists) = result {
                self.dropdown_items = artists.items.iter().map(|item| DropdownItem { value: item.email.clone(), text: item.artist_name.clone() }).collect();
                self.artists = artists.items;
                if let Some(profile) = self.menu_item.clone().artist {
                    self.dropdown_item = Some(profile.email.clone());
                    self.item = self.artists.iter().find_map(|item| if item.email == profile.email { Some(item.id) } else { None })
                } else if let Some(artist) = self.artists.first() {
                    self.dropdown_item = Some(artist.clone().email);
                    self.item = self.artists.iter().find_map(|item| if item.email == artist.clone().email { Some(item.id) } else { None })
                }
            } else {
                Toast::negative_toast(self.translator.translate("menus.designer.settings.error_could_not_load_artists"))
            },
        }

        true
    }

    fn change(&mut self, props: Self::Properties) -> bool {
        self.on_save_changes = props.on_save_changes;
        self.on_discard_changes = props.on_discard_changes;
        self.is_open = props.is_open;

        true
    }

    fn view(&self) -> Html {
        let dropdown_label = match self.dialog_type {
            SettingsDialogType::Group => "".to_string(),
            SettingsDialogType::Link => "".to_string(),
            SettingsDialogType::Gallery => self.translator.translate("menus.designer.settings.gallery"),
            SettingsDialogType::Page => self.translator.translate("menus.designer.settings.page"),
            SettingsDialogType::SegmentPage => self.translator.translate("menus.designer.settings.segment_page"),
            SettingsDialogType::ArtistProfile => self.translator.translate("menus.designer.settings.profile"),
        };

        html! {
            <ContentDialog
                is_open=self.is_open
                on_primary=self.link.callback(|_| SettingsDialogMsg::OnAddPrimary)
                on_secondary=self.link.callback(|_| SettingsDialogMsg::OnAddSecondary)
                primary_label=self.translator.translate("menus.designer.settings.action_save")
                secondary_label=self.translator.translate("menus.designer.settings.action_discard")
                title=self.translator.translate("menus.designer.settings.dialog_title")
            >
                <Row>
                    <Input validation_message=&self.title_validation_message state=&self.title_input_state label=self.translator.translate("menus.designer.settings.title") on_input=self.link.callback(|value| SettingsDialogMsg::OnTitleInput(value)) value=&self.title />
                </Row>
                {if self.route.is_some() {
                    html! {
                        <Row>
                            <Input validation_message=&self.route_validation_message state=&self.route_input_state label=self.translator.translate("menus.designer.settings.route") on_input=self.link.callback(|value| SettingsDialogMsg::OnRouteInput(value)) value=self.route.as_ref().unwrap() />
                        </Row>
                    }
                } else {
                    html! {}
                }}
                {if !dropdown_label.is_empty() {
                    let item = self.dropdown_item.as_ref().unwrap();
                    html! {
                        <Row>
                            <Dropdown items=&self.dropdown_items label=&dropdown_label on_select=self.link.callback(SettingsDialogMsg::OnItemSelected) value=item />
                        </Row>
                    }
                } else {
                    html! {}
                }}
                <Row>
                    <Checkbox
                        label=self.translator.translate("menus.designer.settings.highlighted")
                        on_change=self.link.callback(|_| SettingsDialogMsg::OnHighlightedInput)
                        checked=&self.highlighted
                    />
                </Row>
            </ContentDialog>
        }
    }

    fn rendered(&mut self, first_render: bool) {
        if first_render {
            self.load_items_task = match self.dialog_type {
                SettingsDialogType::Gallery => Some(self.gallery_service.get_list("".to_string(), self.link.callback(SettingsDialogMsg::OnGalleriesLoaded))),
                SettingsDialogType::Page => Some(self.simple_page_service.get_list("".to_string(), self.link.callback(SettingsDialogMsg::OnSimplePagesLoaded))),
                SettingsDialogType::SegmentPage => Some(self.segment_page_service.get_list("".to_string(), self.link.callback(SettingsDialogMsg::OnSegmentPagesLoaded))),
                SettingsDialogType::ArtistProfile => Some(self.artist_service.get_list("".to_string(), self.link.callback(SettingsDialogMsg::OnProfilesLoaded))),
                _ => None
            };
        }
    }
}