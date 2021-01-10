use std::collections::BTreeMap;
use std::str::FromStr;

use jinya_ui::layout::page::Page;
use jinya_ui::widgets::form::dropdown::Dropdown;
use jinya_ui::widgets::form::dropdown::DropdownItem;
use jinya_ui::widgets::toast::Toast;
use serde_json::Value;
use yew::{Component, ComponentLink, Html};
use yew::agent::Dispatcher;
use yew::prelude::*;
use yew::services::fetch::FetchTask;

use crate::agents::menu_agent::{MenuAgent, MenuAgentRequest};
use crate::ajax::AjaxError;
use crate::ajax::file_service::FileService;
use crate::ajax::gallery_service::GalleryService;
use crate::ajax::menu_service::MenuService;
use crate::ajax::segment_page_service::SegmentPageService;
use crate::ajax::simple_page_service::SimplePageService;
use crate::ajax::theme_service::ThemeService;
use crate::i18n::Translator;
use crate::models::file::File;
use crate::models::gallery::Gallery;
use crate::models::list_model::ListModel;
use crate::models::menu::Menu;
use crate::models::segment_page::SegmentPage;
use crate::models::simple_page::SimplePage;
use crate::models::theme::{Theme, ThemeConfigurationStructure, ThemeLinks};

pub struct LinksPage {
    link: ComponentLink<Self>,
    id: usize,
    theme_service: ThemeService,
    menu_dispatcher: Dispatcher<MenuAgent>,
    translator: Translator,
    load_theme_task: Option<FetchTask>,
    load_theme_config_structure_task: Option<FetchTask>,
    links: Option<ThemeLinks>,

    load_segment_pages_task: Option<FetchTask>,
    load_selected_segment_pages_task: Option<FetchTask>,
    segment_pages: Vec<DropdownItem>,
    selected_segment_pages: BTreeMap<String, String>,
    segment_page_service: SegmentPageService,
    save_segment_page_task: Option<FetchTask>,

    load_pages_task: Option<FetchTask>,
    load_selected_pages_task: Option<FetchTask>,
    pages: Vec<DropdownItem>,
    selected_pages: BTreeMap<String, String>,
    page_service: SimplePageService,
    save_page_task: Option<FetchTask>,

    load_files_task: Option<FetchTask>,
    load_selected_files_task: Option<FetchTask>,
    files: Vec<DropdownItem>,
    selected_files: BTreeMap<String, String>,
    file_service: FileService,
    save_file_task: Option<FetchTask>,

    load_menus_task: Option<FetchTask>,
    load_selected_menus_task: Option<FetchTask>,
    menus: Vec<DropdownItem>,
    selected_menus: BTreeMap<String, String>,
    menu_service: MenuService,
    save_menu_task: Option<FetchTask>,

    load_galleries_task: Option<FetchTask>,
    load_selected_galleries_task: Option<FetchTask>,
    galleries: Vec<DropdownItem>,
    selected_galleries: BTreeMap<String, String>,
    gallery_service: GalleryService,
    save_gallery_task: Option<FetchTask>,
}

#[allow(clippy::large_enum_variant)]
pub enum Msg {
    OnThemeLoaded(Result<Theme, AjaxError>),
    OnConfigStructureLoaded(Result<ThemeConfigurationStructure, AjaxError>),

    OnSegmentPagesLoaded(Result<ListModel<SegmentPage>, AjaxError>),
    OnSegmentPageChanged(String, String),
    OnSegmentPageSaved(Result<bool, AjaxError>),
    OnSelectedSegmentPagesLoaded(Result<BTreeMap<String, SegmentPage>, AjaxError>),

    OnPagesLoaded(Result<ListModel<SimplePage>, AjaxError>),
    OnPageChanged(String, String),
    OnPageSaved(Result<bool, AjaxError>),
    OnSelectedPagesLoaded(Result<BTreeMap<String, SimplePage>, AjaxError>),

    OnFilesLoaded(Result<ListModel<File>, AjaxError>),
    OnFileChanged(String, String),
    OnFileSaved(Result<bool, AjaxError>),
    OnSelectedFilesLoaded(Result<BTreeMap<String, File>, AjaxError>),

    OnMenusLoaded(Result<ListModel<Menu>, AjaxError>),
    OnMenuChanged(String, String),
    OnMenuSaved(Result<bool, AjaxError>),
    OnSelectedMenusLoaded(Result<BTreeMap<String, Menu>, AjaxError>),

    OnGalleriesLoaded(Result<ListModel<Gallery>, AjaxError>),
    OnGalleryChanged(String, String),
    OnGallerySaved(Result<bool, AjaxError>),
    OnSelectedGalleriesLoaded(Result<BTreeMap<String, Gallery>, AjaxError>),
}

#[derive(Properties, Clone)]
pub struct ScssPageProperties {
    pub id: usize,
}

impl Component for LinksPage {
    type Message = Msg;
    type Properties = ScssPageProperties;

    fn create(props: Self::Properties, link: ComponentLink<Self>) -> Self {
        let menu_dispatcher = MenuAgent::dispatcher();

        LinksPage {
            link,
            id: props.id,
            theme_service: ThemeService::new(),
            menu_dispatcher,
            translator: Translator::new(),
            load_theme_task: None,
            load_theme_config_structure_task: None,
            links: None,
            load_segment_pages_task: None,
            load_selected_segment_pages_task: None,
            segment_pages: vec![],
            selected_segment_pages: BTreeMap::new(),
            segment_page_service: SegmentPageService::new(),
            save_segment_page_task: None,
            load_pages_task: None,
            load_selected_pages_task: None,
            pages: vec![],
            selected_pages: BTreeMap::new(),
            page_service: SimplePageService::new(),
            save_page_task: None,
            load_files_task: None,
            load_selected_files_task: None,
            files: vec![],
            selected_files: BTreeMap::new(),
            file_service: FileService::new(),
            save_file_task: None,
            load_menus_task: None,
            load_selected_menus_task: None,
            menus: vec![],
            selected_menus: BTreeMap::new(),
            menu_service: MenuService::new(),
            save_menu_task: None,
            load_galleries_task: None,
            load_selected_galleries_task: None,
            galleries: vec![],
            selected_galleries: BTreeMap::new(),
            gallery_service: GalleryService::new(),
            save_gallery_task: None,
        }
    }

    fn update(&mut self, msg: Self::Message) -> bool {
        match msg {
            Msg::OnThemeLoaded(result) => {
                if let Ok(theme) = result {
                    self.menu_dispatcher.send(MenuAgentRequest::ChangeTitle(self.translator.translate_with_args("app.menu.configuration.frontend.themes.links", map! { "name" => theme.display_name.as_str() })))
                }
            }
            Msg::OnConfigStructureLoaded(result) => if let Ok(structure) = result {
                self.links = Some(structure.links);
                self.reload();
            },
            Msg::OnSegmentPagesLoaded(result) => {
                if let Ok(items) = result {
                    self.segment_pages = items.items.iter().enumerate().map(|(_, item)| {
                        DropdownItem {
                            value: item.id.to_string(),
                            text: item.name.to_string(),
                        }
                    }).collect();
                }
            }
            Msg::OnSegmentPageChanged(name, value) => {
                if !name.is_empty() {
                    let page = usize::from_str(value.as_str());
                    self.save_segment_page_task = Some(self.theme_service.save_segment_page_link(self.id, name, page.unwrap(), self.link.callback(Msg::OnSegmentPageSaved)));
                }
            }
            Msg::OnSegmentPageSaved(result) => {
                if result.is_ok() {
                    Toast::positive_toast(self.translator.translate("themes.links.notification.segment_page_changed"));
                } else {
                    Toast::negative_toast(self.translator.translate("themes.links.notification.segment_page_failed"));
                }
                self.reload()
            }
            Msg::OnSelectedSegmentPagesLoaded(result) => {
                if let Ok(items) = result {
                    let mut pages = BTreeMap::new();
                    items.iter().for_each(|(name, page)| {
                        pages.insert(name.to_string(), page.id.to_string());
                    });
                    self.selected_segment_pages = pages;
                }
            }

            Msg::OnPagesLoaded(result) => {
                if let Ok(items) = result {
                    self.pages = items.items.iter().enumerate().map(|(_, item)| {
                        DropdownItem {
                            value: item.id.to_string(),
                            text: item.title.to_string(),
                        }
                    }).collect();
                }
            }
            Msg::OnPageChanged(name, value) => {
                if !name.is_empty() {
                    let page = usize::from_str(value.as_str());
                    self.save_page_task = Some(self.theme_service.save_page_link(self.id, name, page.unwrap(), self.link.callback(Msg::OnPageSaved)));
                }
            }
            Msg::OnPageSaved(result) => {
                if result.is_ok() {
                    Toast::positive_toast(self.translator.translate("themes.links.notification.page_changed"));
                } else {
                    Toast::negative_toast(self.translator.translate("themes.links.notification.page_failed"));
                }
                self.reload()
            }
            Msg::OnSelectedPagesLoaded(result) => {
                if let Ok(items) = result {
                    let mut pages = BTreeMap::new();
                    items.iter().for_each(|(name, page)| {
                        pages.insert(name.to_string(), page.id.to_string());
                    });
                    self.selected_pages = pages;
                }
            }

            Msg::OnFilesLoaded(result) => {
                if let Ok(items) = result {
                    self.files = items.items.iter().enumerate().map(|(_, item)| {
                        DropdownItem {
                            value: item.id.to_string(),
                            text: item.name.to_string(),
                        }
                    }).collect();
                }
            }
            Msg::OnFileChanged(name, value) => {
                if !name.is_empty() {
                    let file = usize::from_str(value.as_str());
                    self.save_file_task = Some(self.theme_service.save_file_link(self.id, name, file.unwrap(), self.link.callback(Msg::OnFileSaved)));
                }
            }
            Msg::OnFileSaved(result) => {
                if result.is_ok() {
                    Toast::positive_toast(self.translator.translate("themes.links.notification.file_changed"));
                } else {
                    Toast::negative_toast(self.translator.translate("themes.links.notification.file_failed"));
                }
                self.reload()
            }
            Msg::OnSelectedFilesLoaded(result) => {
                if let Ok(items) = result {
                    let mut files = BTreeMap::new();
                    items.iter().for_each(|(name, file)| {
                        files.insert(name.to_string(), file.id.to_string());
                    });
                    self.selected_files = files;
                }
            }

            Msg::OnMenusLoaded(result) => {
                if let Ok(items) = result {
                    self.menus = items.items.iter().enumerate().map(|(_, item)| {
                        DropdownItem {
                            value: item.id.to_string(),
                            text: item.name.to_string(),
                        }
                    }).collect();
                }
            }
            Msg::OnMenuChanged(name, value) => {
                if !name.is_empty() {
                    let menu = usize::from_str(value.as_str());
                    self.save_menu_task = Some(self.theme_service.save_menu_link(self.id, name, menu.unwrap(), self.link.callback(Msg::OnMenuSaved)));
                }
            }
            Msg::OnMenuSaved(result) => {
                if result.is_ok() {
                    Toast::positive_toast(self.translator.translate("themes.links.notification.menu_changed"));
                } else {
                    Toast::negative_toast(self.translator.translate("themes.links.notification.menu_failed"));
                }
                self.reload()
            }
            Msg::OnSelectedMenusLoaded(result) => {
                if let Ok(items) = result {
                    let mut menus = BTreeMap::new();
                    items.iter().for_each(|(name, menu)| {
                        menus.insert(name.to_string(), menu.id.to_string());
                    });
                    self.selected_menus = menus;
                }
            }

            Msg::OnGalleriesLoaded(result) => {
                if let Ok(items) = result {
                    self.galleries = items.items.iter().enumerate().map(|(_, item)| {
                        DropdownItem {
                            value: item.id.to_string(),
                            text: item.name.to_string(),
                        }
                    }).collect();
                }
            }
            Msg::OnGalleryChanged(name, value) => {
                if !name.is_empty() {
                    let gallery = usize::from_str(value.as_str());
                    self.save_gallery_task = Some(self.theme_service.save_gallery_link(self.id, name, gallery.unwrap(), self.link.callback(Msg::OnGallerySaved)));
                }
            }
            Msg::OnGallerySaved(result) => {
                if result.is_ok() {
                    Toast::positive_toast(self.translator.translate("themes.links.notification.gallery_changed"));
                } else {
                    Toast::negative_toast(self.translator.translate("themes.links.notification.gallery_failed"));
                }
                self.reload()
            }
            Msg::OnSelectedGalleriesLoaded(result) => {
                if let Ok(items) = result {
                    let mut galleries = BTreeMap::new();
                    items.iter().for_each(|(name, gallery)| {
                        galleries.insert(name.to_string(), gallery.id.to_string());
                    });
                    self.selected_galleries = galleries;
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
        if let Some(links) = &self.links {
            html! {
                <Page>
                    {self.get_menus(links.clone())}
                    {self.get_files(links.clone())}
                    {self.get_galleries(links.clone())}
                    {self.get_pages(links.clone())}
                    {self.get_segment_pages(links.clone())}
                </Page>
            }
        } else {
            html! {}
        }
    }

    fn rendered(&mut self, first_render: bool) {
        if first_render {
            self.menu_dispatcher.send(MenuAgentRequest::HideSearch);
            self.load_theme_task = Some(self.theme_service.get_theme(self.id, self.link.callback(Msg::OnThemeLoaded)));
            self.load_theme_config_structure_task = Some(self.theme_service.get_configuration_structure(self.id, self.link.callback(Msg::OnConfigStructureLoaded)));
        }
    }
}

impl LinksPage {
    fn reload(&mut self) {
        if self.links.clone().unwrap().segment_pages.is_some() {
            self.load_selected_segment_pages_task = Some(self.theme_service.get_theme_segment_pages(self.id, self.link.callback(Msg::OnSelectedSegmentPagesLoaded)));
            self.load_segment_pages_task = Some(self.segment_page_service.get_list("".to_string(), self.link.callback(Msg::OnSegmentPagesLoaded)));
            self.load_selected_pages_task = Some(self.theme_service.get_theme_pages(self.id, self.link.callback(Msg::OnSelectedPagesLoaded)));
            self.load_pages_task = Some(self.page_service.get_list("".to_string(), self.link.callback(Msg::OnPagesLoaded)));
            self.load_selected_files_task = Some(self.theme_service.get_theme_files(self.id, self.link.callback(Msg::OnSelectedFilesLoaded)));
            self.load_files_task = Some(self.file_service.get_list("".to_string(), self.link.callback(Msg::OnFilesLoaded)));
            self.load_selected_menus_task = Some(self.theme_service.get_theme_menus(self.id, self.link.callback(Msg::OnSelectedMenusLoaded)));
            self.load_menus_task = Some(self.menu_service.get_list("".to_string(), self.link.callback(Msg::OnMenusLoaded)));
            self.load_selected_galleries_task = Some(self.theme_service.get_theme_galleries(self.id, self.link.callback(Msg::OnSelectedGalleriesLoaded)));
            self.load_galleries_task = Some(self.gallery_service.get_list("".to_string(), self.link.callback(Msg::OnGalleriesLoaded)));
        }
    }

    fn get_segment_page_input(&self, label: Value, name: String) -> Html {
        let lbl = label.as_str().unwrap().to_string();
        let value = if self.selected_segment_pages.contains_key(name.as_str()) {
            self.selected_segment_pages[&name].clone()
        } else {
            "".to_string()
        };
        let placeholder = Some(self.translator.translate("themes.links.placeholder.segment_page"));

        html! {
            <Dropdown placeholder=placeholder label=lbl items=&self.segment_pages on_select=self.link.callback(move |val| Msg::OnSegmentPageChanged(name.clone(), val)) value=value />
        }
    }

    fn get_segment_pages(&self, links: ThemeLinks) -> Html {
        if let Some(pages) = links.segment_pages {
            html! {
                <fieldset class="jinya-designer-themes-links__fieldset">
                    <legend class="jinya-designer-themes-links__legend">{self.translator.translate("themes.links.legend.segment_page")}</legend>
                    {for pages.iter().enumerate().map(|(_, (name, label))| {
                        self.get_segment_page_input((*label).clone(), name.to_string())
                    })}
                </fieldset>
            }
        } else {
            html! {
            }
        }
    }

    fn get_page_input(&self, label: Value, name: String) -> Html {
        let lbl = label.as_str().unwrap().to_string();
        let value = if self.selected_pages.contains_key(name.as_str()) {
            self.selected_pages[&name].clone()
        } else {
            "".to_string()
        };
        let placeholder = Some(self.translator.translate("themes.links.placeholder.page"));

        html! {
            <Dropdown placeholder=placeholder label=lbl items=&self.pages on_select=self.link.callback(move |val| Msg::OnPageChanged(name.clone(), val)) value=value />
        }
    }

    fn get_pages(&self, links: ThemeLinks) -> Html {
        if let Some(pages) = links.pages {
            html! {
                <fieldset class="jinya-designer-themes-links__fieldset">
                    <legend class="jinya-designer-themes-links__legend">{self.translator.translate("themes.links.legend.page")}</legend>
                    {for pages.iter().enumerate().map(|(_, (name, label))| {
                        self.get_page_input((*label).clone(), name.to_string())
                    })}
                </fieldset>
            }
        } else {
            html! {
            }
        }
    }

    fn get_file_input(&self, label: Value, name: String) -> Html {
        let lbl = label.as_str().unwrap().to_string();
        let value = if self.selected_files.contains_key(name.as_str()) {
            self.selected_files[&name].clone()
        } else {
            "".to_string()
        };
        let placeholder = Some(self.translator.translate("themes.links.placeholder.file"));

        html! {
            <Dropdown placeholder=placeholder label=lbl items=&self.files on_select=self.link.callback(move |val| Msg::OnFileChanged(name.clone(), val)) value=value />
        }
    }

    fn get_files(&self, links: ThemeLinks) -> Html {
        if let Some(files) = links.files {
            html! {
                <fieldset class="jinya-designer-themes-links__fieldset">
                    <legend class="jinya-designer-themes-links__legend">{self.translator.translate("themes.links.legend.file")}</legend>
                    {for files.iter().enumerate().map(|(_, (name, label))| {
                        self.get_file_input((*label).clone(), name.to_string())
                    })}
                </fieldset>
            }
        } else {
            html! {
            }
        }
    }

    fn get_menu_input(&self, label: Value, name: String) -> Html {
        let lbl = label.as_str().unwrap().to_string();
        let value = if self.selected_menus.contains_key(name.as_str()) {
            self.selected_menus[&name].clone()
        } else {
            "".to_string()
        };
        let placeholder = Some(self.translator.translate("themes.links.placeholder.menu"));

        html! {
            <Dropdown placeholder=placeholder label=lbl items=&self.menus on_select=self.link.callback(move |val| Msg::OnMenuChanged(name.clone(), val)) value=value />
        }
    }

    fn get_menus(&self, links: ThemeLinks) -> Html {
        if let Some(menus) = links.menus {
            html! {
                <fieldset class="jinya-designer-themes-links__fieldset">
                    <legend class="jinya-designer-themes-links__legend">{self.translator.translate("themes.links.legend.menu")}</legend>
                    {for menus.iter().enumerate().map(|(_, (name, label))| {
                        self.get_menu_input((*label).clone(), name.to_string())
                    })}
                </fieldset>
            }
        } else {
            html! {
            }
        }
    }

    fn get_gallery_input(&self, label: Value, name: String) -> Html {
        let lbl = label.as_str().unwrap().to_string();
        let value = if self.selected_galleries.contains_key(name.as_str()) {
            self.selected_galleries[&name].clone()
        } else {
            "".to_string()
        };
        let placeholder = Some(self.translator.translate("themes.links.placeholder.gallery"));

        html! {
            <Dropdown placeholder=placeholder label=lbl items=&self.galleries on_select=self.link.callback(move |val| Msg::OnGalleryChanged(name.clone(), val)) value=value />
        }
    }

    fn get_galleries(&self, links: ThemeLinks) -> Html {
        if let Some(galleries) = links.galleries {
            html! {
                <fieldset class="jinya-designer-themes-links__fieldset">
                    <legend class="jinya-designer-themes-links__legend">{self.translator.translate("themes.links.legend.gallery")}</legend>
                    {for galleries.iter().enumerate().map(|(_, (name, label))| {
                        self.get_gallery_input((*label).clone(), name.to_string())
                    })}
                </fieldset>
            }
        } else {
            html! {
            }
        }
    }
}