use jinya_ui::layout::button_row::{ButtonRow, ButtonRowAlignment};
use jinya_ui::layout::page::Page;
use jinya_ui::layout::row::Row;
use jinya_ui::listing::table::{Table, TableCell, TableHeader, TableRow};
use jinya_ui::widgets::alert::{Alert, AlertType};
use jinya_ui::widgets::button::{Button, ButtonType};
use jinya_ui::widgets::dialog::confirmation::{ConfirmationDialog, DialogType};
use yew::{Component, ComponentLink, Html};
use yew::prelude::*;
use yew::services::fetch::FetchTask;
use yew_router::agent::{RouteAgent, RouteRequest};
use yew_router::route::Route;

use crate::agents::menu_agent::{MenuAgent, MenuAgentRequest, MenuAgentResponse};
use crate::ajax::AjaxError;
use crate::ajax::segment_page_service::SegmentPageService;
use crate::app::AppRoute;
use crate::i18n::Translator;
use crate::models::list_model::ListModel;
use crate::models::segment_page::SegmentPage;
use crate::views::segment_pages::add_dialog::AddDialog;
use crate::views::segment_pages::edit_dialog::EditDialog;

mod add_dialog;
mod edit_dialog;
pub mod designer;

pub struct SegmentPagesPage {
    link: ComponentLink<Self>,
    selected_index: Option<usize>,
    headers: Vec<TableHeader>,
    translator: Translator,
    load_segment_pages_task: Option<FetchTask>,
    rows: Vec<TableRow>,
    segment_pages: Vec<SegmentPage>,
    segment_page_service: SegmentPageService,
    page_to_edit: Option<SegmentPage>,
    menu_agent: Box<dyn Bridge<MenuAgent>>,
    show_delete_dialog: bool,
    show_add_dialog: bool,
    show_edit_dialog: bool,
    delete_page_task: Option<FetchTask>,
    alert_message: Option<String>,
    alert_type: AlertType,
    keyword: String,
    router_agent: Box<dyn Bridge<RouteAgent>>,
}

pub enum Msg {
    OnNewPageClick,
    OnEditPageClick,
    OnPageDesignerClick,
    OnDeletePageClick,
    OnPagesLoaded(Result<ListModel<SegmentPage>, AjaxError>),
    OnPageSelected(usize),
    OnMenuAgentResponse(MenuAgentResponse),
    OnDeleteApprove,
    OnDeleteDecline,
    OnPageDeleted(Result<bool, AjaxError>),
    Ignore,
    OnSaveAdd,
    OnDiscardAdd,
    OnSaveEdit,
    OnDiscardEdit,
}

impl Component for SegmentPagesPage {
    type Message = Msg;
    type Properties = ();

    fn create(_props: Self::Properties, link: ComponentLink<Self>) -> Self {
        let translator = Translator::new();
        let menu_agent = MenuAgent::bridge(link.callback(|response| Msg::OnMenuAgentResponse(response)));
        let router_agent = RouteAgent::bridge(link.callback(|_| Msg::Ignore));

        SegmentPagesPage {
            link,
            selected_index: None,
            headers: vec![
                TableHeader {
                    title: translator.translate("segment_pages.overview.table.name_column"),
                    key: "name".to_string(),
                },
                TableHeader {
                    title: translator.translate("segment_pages.overview.table.segment_count_column"),
                    key: "segment_count".to_string(),
                },
            ],
            translator,
            load_segment_pages_task: None,
            rows: vec![],
            segment_pages: vec![],
            segment_page_service: SegmentPageService::new(),
            page_to_edit: None,
            menu_agent,
            show_delete_dialog: false,
            show_add_dialog: false,
            show_edit_dialog: false,
            delete_page_task: None,
            alert_message: None,
            alert_type: AlertType::Negative,
            keyword: "".to_string(),
            router_agent,
        }
    }

    fn update(&mut self, msg: Self::Message) -> bool {
        match msg {
            Msg::OnNewPageClick => self.show_add_dialog = true,
            Msg::OnEditPageClick => self.page_to_edit = Some(self.get_selected_page()),
            Msg::OnDeletePageClick => self.show_delete_dialog = true,
            Msg::OnPagesLoaded(result) => {
                if result.is_ok() {
                    self.segment_pages = result.unwrap().items;
                    self.rows = self.segment_pages.iter().enumerate().map(|(_, item)| {
                        TableRow::new(vec![
                            TableCell {
                                key: "name".to_string(),
                                value: item.name.to_string(),
                            },
                            TableCell {
                                key: "segment_count".to_string(),
                                value: format!("{}", item.segment_count).to_string(),
                            },
                        ])
                    }).collect();
                } else {
                    log::error!("{}", result.err().unwrap().error.to_string().as_str());
                }
            }
            Msg::OnPageSelected(idx) => self.selected_index = Some(idx),
            Msg::OnMenuAgentResponse(response) => match response {
                MenuAgentResponse::OnSearch(value) => self.keyword = value,
                _ => {}
            },
            Msg::OnDeleteApprove => self.delete_page_task = Some(self.segment_page_service.delete_segment_page(self.get_selected_page(), self.link.callback(|result| Msg::OnPageDeleted(result)))),
            Msg::OnDeleteDecline => self.show_delete_dialog = false,
            Msg::OnPageDeleted(result) => {
                if result.is_ok() {
                    self.selected_index = None;
                    self.show_delete_dialog = false;
                    self.load_segment_pages_task = Some(self.segment_page_service.get_list(self.keyword.clone(), self.link.callback(|result| Msg::OnPagesLoaded(result))));
                } else {
                    log::error!("{}", result.err().unwrap().error.to_string().as_str());
                    let page = self.get_selected_page();
                    self.alert_message = Some(self.translator.translate_with_args("segment_pages.delete.failed", map! {"name" => page.name.as_str()}));
                    self.show_delete_dialog = false;
                }
            }
            Msg::Ignore => {}
            Msg::OnSaveAdd => {
                self.selected_index = None;
                self.show_add_dialog = false;
                self.load_segment_pages_task = Some(self.segment_page_service.get_list(self.keyword.clone(), self.link.callback(|result| Msg::OnPagesLoaded(result))));
            }
            Msg::OnSaveEdit => {
                self.selected_index = None;
                self.page_to_edit = None;
                self.load_segment_pages_task = Some(self.segment_page_service.get_list(self.keyword.clone(), self.link.callback(|result| Msg::OnPagesLoaded(result))));
            }
            Msg::OnDiscardAdd => self.show_add_dialog = false,
            Msg::OnDiscardEdit => self.page_to_edit = None,
            Msg::OnPageDesignerClick => self.router_agent.send(RouteRequest::ChangeRoute(Route::from(AppRoute::SegmentPageDesigner(self.get_selected_page().id))))
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
                    <Button label=self.translator.translate("segment_pages.overview.action_new") on_click=self.link.callback(|_| Msg::OnNewPageClick) />
                    <Button disabled=self.selected_index.is_none() label=self.translator.translate("segment_pages.overview.action_edit") on_click=self.link.callback(|_| Msg::OnEditPageClick) />
                    <Button disabled=self.selected_index.is_none() label=self.translator.translate("segment_pages.overview.action_designer") on_click=self.link.callback(|_| Msg::OnPageDesignerClick) />
                    <Button disabled=self.selected_index.is_none() label=self.translator.translate("segment_pages.overview.action_delete") button_type=ButtonType::Negative on_click=self.link.callback(|_| Msg::OnDeletePageClick) />
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
                <Table selected_index=self.selected_index headers=&self.headers rows=&self.rows on_select=self.link.callback(|idx| Msg::OnPageSelected(idx)) />
                {if self.show_delete_dialog {
                    let page = self.get_selected_page();
                    html! {
                        <ConfirmationDialog
                            title=self.translator.translate("segment_pages.delete.title")
                            dialog_type=DialogType::Negative
                            message=self.translator.translate_with_args("segment_pages.delete.content", map! {"name" => page.name.as_str()})
                            decline_label=self.translator.translate("segment_pages.delete.decline")
                            approve_label=self.translator.translate("segment_pages.delete.approve")
                            on_approve=self.link.callback(|_| Msg::OnDeleteApprove)
                            on_decline=self.link.callback(|_| Msg::OnDeleteDecline)
                            is_open=self.show_delete_dialog
                        />
                    }
                } else {
                    html! {}
                }}
                {if self.page_to_edit.is_some() {
                    let page = self.page_to_edit.as_ref().unwrap();
                    html! {
                        <EditDialog segment_page=page on_save_changes=self.link.callback(|_| Msg::OnSaveEdit) on_discard_changes=self.link.callback(|_| Msg::OnDiscardEdit) />
                    }
                } else {
                    html! {}
                }}
                {if self.show_add_dialog {
                    html! {
                        <AddDialog is_open=self.show_add_dialog on_save_changes=self.link.callback(|_| Msg::OnSaveAdd) on_discard_changes=self.link.callback(|_| Msg::OnDiscardAdd) />
                    }
                } else {
                    html! {}
                }}
            </Page>
        }
    }

    fn rendered(&mut self, first_render: bool) {
        if first_render {
            self.menu_agent.send(MenuAgentRequest::ChangeTitle(self.translator.translate("app.menu.content.pages.segment_pages")));
            self.load_segment_pages_task = Some(self.segment_page_service.get_list(self.keyword.clone(), self.link.callback(|result| Msg::OnPagesLoaded(result))));
        }
    }
}

impl SegmentPagesPage {
    fn get_selected_page(&self) -> SegmentPage {
        self.segment_pages[self.selected_index.unwrap()].clone()
    }
}