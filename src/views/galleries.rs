use jinya_ui::layout::button_row::{ButtonRow, ButtonRowAlignment};
use jinya_ui::layout::page::Page;
use jinya_ui::layout::row::Row;
use jinya_ui::listing::table::{Table, TableCell, TableHeader, TableRow};
use jinya_ui::widgets::alert::{Alert, AlertType};
use jinya_ui::widgets::button::{Button, ButtonType};
use jinya_ui::widgets::dialog::confirmation::{ConfirmationDialog, DialogType};
use jinya_ui::widgets::toast::Toast;
use yew::{Component, ComponentLink, Html};
use yew::prelude::*;
use yew::services::ConsoleService;
use yew::services::fetch::FetchTask;

use add_dialog::AddDialog;
use edit_dialog::EditDialog;

use crate::agents::menu_agent::{MenuAgent, MenuAgentRequest, MenuAgentResponse};
use crate::ajax::AjaxError;
use crate::ajax::gallery_service::GalleryService;
use crate::i18n::Translator;
use crate::models::gallery::{Gallery, GalleryType, Orientation};
use crate::models::list_model::ListModel;

mod edit_dialog;
mod add_dialog;

pub struct GalleriesPage {
    link: ComponentLink<Self>,
    selected_index: Option<usize>,
    headers: Vec<TableHeader>,
    translator: Translator,
    load_galleries_task: Option<FetchTask>,
    rows: Vec<TableRow>,
    galleries: Vec<Gallery>,
    gallery_service: GalleryService,
    menu_agent: Box<dyn Bridge<MenuAgent>>,
    show_delete_dialog: bool,
    delete_gallery_task: Option<FetchTask>,
    alert_message: Option<String>,
    alert_type: AlertType,
    add_gallery_open: bool,
    edit_gallery_open: bool,
    keyword: String,
}

pub enum Msg {
    OnNewGalleryClick,
    OnEditGalleryClick,
    OnDeleteGalleryClick,
    OnGalleriesLoaded(Result<ListModel<Gallery>, AjaxError>),
    OnGallerySelected(usize),
    OnMenuAgentResponse(MenuAgentResponse),
    OnDeleteApprove,
    OnDeleteDecline,
    OnGalleryDeleted(Result<bool, AjaxError>),
    OnSaveAdd(Gallery),
    OnDiscardAdd,
    OnSaveEdit,
    OnDiscardEdit,
}

impl Component for GalleriesPage {
    type Message = Msg;
    type Properties = ();

    fn create(_props: Self::Properties, link: ComponentLink<Self>) -> Self {
        let translator = Translator::new();
        let menu_agent = MenuAgent::bridge(link.callback(|response| Msg::OnMenuAgentResponse(response)));

        GalleriesPage {
            link,
            selected_index: None,
            headers: vec![
                TableHeader {
                    title: translator.translate("galleries.overview.table.name_column"),
                    key: "name".to_string(),
                },
                TableHeader {
                    title: translator.translate("galleries.overview.table.orientation_column"),
                    key: "orientation".to_string(),
                },
                TableHeader {
                    title: translator.translate("galleries.overview.table.type_column"),
                    key: "type".to_string(),
                },
                TableHeader {
                    title: translator.translate("galleries.overview.table.description_column"),
                    key: "description".to_string(),
                },
            ],
            translator,
            load_galleries_task: None,
            rows: vec![],
            galleries: vec![],
            gallery_service: GalleryService::new(),
            menu_agent,
            show_delete_dialog: false,
            delete_gallery_task: None,
            alert_message: None,
            alert_type: AlertType::Negative,
            add_gallery_open: false,
            edit_gallery_open: false,
            keyword: "".to_string(),
        }
    }

    fn update(&mut self, msg: Self::Message) -> bool {
        match msg {
            Msg::OnNewGalleryClick => self.add_gallery_open = true,
            Msg::OnEditGalleryClick => self.edit_gallery_open = true,
            Msg::OnDeleteGalleryClick => self.show_delete_dialog = true,
            Msg::OnGalleriesLoaded(result) => {
                if result.is_ok() {
                    self.galleries = result.unwrap().items;
                    self.rows = self.galleries.iter().enumerate().map(|(_, item)| {
                        TableRow::new(vec![
                            TableCell {
                                key: "name".to_string(),
                                value: item.name.to_string(),
                            },
                            TableCell {
                                key: "orientation".to_string(),
                                value: match &item.orientation {
                                    Orientation::Vertical => self.translator.translate("galleries.overview.table.orientation.vertical"),
                                    Orientation::Horizontal => self.translator.translate("galleries.overview.table.orientation.horizontal"),
                                }.to_string(),
                            },
                            TableCell {
                                key: "type".to_string(),
                                value: match &item.gallery_type {
                                    GalleryType::Masonry => self.translator.translate("galleries.overview.table.type.masonry"),
                                    GalleryType::Sequence => self.translator.translate("galleries.overview.table.type.sequence"),
                                }.to_string(),
                            },
                            TableCell {
                                key: "description".to_string(),
                                value: item.description.to_string(),
                            },
                        ])
                    }).collect();
                } else {
                    ConsoleService::error(result.err().unwrap().error.to_string().as_str());
                }
            }
            Msg::OnGallerySelected(idx) => self.selected_index = Some(idx),
            Msg::OnMenuAgentResponse(response) => match response {
                MenuAgentResponse::OnSearch(value) => self.keyword = value,
                _ => {}
            },
            Msg::OnDeleteApprove => self.delete_gallery_task = Some(self.gallery_service.delete_gallery(self.get_selected_gallery(), self.link.callback(|result| Msg::OnGalleryDeleted(result)))),
            Msg::OnDeleteDecline => self.show_delete_dialog = false,
            Msg::OnGalleryDeleted(result) => {
                if result.is_ok() {
                    self.selected_index = None;
                    self.show_delete_dialog = false;
                    self.load_galleries_task = Some(self.gallery_service.get_list(self.keyword.clone(), self.link.callback(|result| Msg::OnGalleriesLoaded(result))));
                } else {
                    ConsoleService::error(result.err().unwrap().error.to_string().as_str());
                    let gallery = self.get_selected_gallery();
                    self.alert_message = Some(self.translator.translate_with_args("galleries.delete.failed", map! {"name" => gallery.name.as_str()}));
                    self.show_delete_dialog = false;
                }
            }
            Msg::OnSaveAdd(gallery) => {
                self.add_gallery_open = false;
                self.load_galleries_task = Some(self.gallery_service.get_list(self.keyword.clone(), self.link.callback(|result| Msg::OnGalleriesLoaded(result))));
                Toast::positive_toast(self.translator.translate_with_args("galleries.add.saved", map! {"name" => gallery.name.as_str()}));
            }
            Msg::OnDiscardAdd => self.add_gallery_open = false,
            Msg::OnSaveEdit => self.edit_gallery_open = false,
            Msg::OnDiscardEdit => self.edit_gallery_open = false,
        }

        true
    }

    fn change(&mut self, _props: Self::Properties) -> bool {
        true
    }

    fn view(&self) -> Html {
        html! {
            <Page>
                <ButtonRow alignment=ButtonRowAlignment::Start>
                    <Button label=self.translator.translate("galleries.overview.action_new") on_click=self.link.callback(|_| Msg::OnNewGalleryClick) />
                    <Button disabled=self.selected_index.is_none() label=self.translator.translate("galleries.overview.action_edit") on_click=self.link.callback(|_| Msg::OnEditGalleryClick) />
                    <Button disabled=self.selected_index.is_none() label=self.translator.translate("galleries.overview.action_designer") on_click=self.link.callback(|_| Msg::OnNewGalleryClick) />
                    <Button disabled=self.selected_index.is_none() label=self.translator.translate("galleries.overview.action_delete") button_type=ButtonType::Negative on_click=self.link.callback(|_| Msg::OnDeleteGalleryClick) />
                </ButtonRow>
                {if self.alert_message.is_some() {
                    html! {
                        <Row>
                            <Alert alert_type=&self.alert_type message=self.alert_message.as_ref().unwrap() />
                        </Row>
                    }
                } else {
                    html! {}
                }}
                <Table selected_index=self.selected_index headers=&self.headers rows=&self.rows on_select=self.link.callback(|idx| Msg::OnGallerySelected(idx)) />
                {if self.show_delete_dialog {
                    let gallery = self.get_selected_gallery();
                    html! {
                        <ConfirmationDialog
                            title=self.translator.translate("galleries.delete.title")
                            dialog_type=DialogType::Negative
                            message=self.translator.translate_with_args("galleries.delete.content", map! {"name" => gallery.name.as_str()})
                            decline_label=self.translator.translate("galleries.delete.decline")
                            approve_label=self.translator.translate("galleries.delete.approve")
                            on_approve=self.link.callback(|_| Msg::OnDeleteApprove)
                            on_decline=self.link.callback(|_| Msg::OnDeleteDecline)
                            is_open=self.show_delete_dialog
                        />
                    }
                } else {
                    html! {}
                }}
                {if self.add_gallery_open {
                    html! {
                        <AddDialog is_open=self.add_gallery_open on_save_changes=self.link.callback(|gallery| Msg::OnSaveAdd(gallery)) on_discard_changes=self.link.callback(|_| Msg::OnDiscardAdd) />
                    }
                } else {
                    html! {}
                }}
                {if self.edit_gallery_open {
                    let gallery = self.get_selected_gallery();
                    html! {
                        <EditDialog gallery=gallery is_open=self.edit_gallery_open on_save_changes=self.link.callback(|gallery| Msg::OnSaveEdit) on_discard_changes=self.link.callback(|_| Msg::OnDiscardEdit) />
                    }
                } else {
                    html! {}
                }}
            </Page>
        }
    }

    fn rendered(&mut self, first_render: bool) {
        if first_render {
            self.menu_agent.send(MenuAgentRequest::ChangeTitle(self.translator.translate("app.menu.content.media.galleries")));
            self.load_galleries_task = Some(self.gallery_service.get_list(self.keyword.clone(), self.link.callback(|result| Msg::OnGalleriesLoaded(result))));
        }
    }
}

impl GalleriesPage {
    fn get_selected_gallery(&self) -> Gallery {
        self.galleries[self.selected_index.unwrap()].clone()
    }
}