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
use yew_router::agent::{RouteAgent, RouteRequest};
use yew_router::route::Route;

use crate::agents::menu_agent::{MenuAgent, MenuAgentRequest, MenuAgentResponse};
use crate::ajax::AjaxError;
use crate::ajax::simple_page_service::SimplePageService;
use crate::app::AppRoute;
use crate::i18n::Translator;
use crate::models::list_model::ListModel;
use crate::models::simple_page::SimplePage;

pub struct SimplePagesPage {
    link: ComponentLink<Self>,
    selected_index: Option<usize>,
    headers: Vec<TableHeader>,
    translator: Translator,
    load_simple_pages_task: Option<FetchTask>,
    rows: Vec<TableRow>,
    simple_pages: Vec<SimplePage>,
    simple_page_service: SimplePageService,
    menu_agent: Box<dyn Bridge<MenuAgent>>,
    show_delete_dialog: bool,
    delete_page_task: Option<FetchTask>,
    alert_message: Option<String>,
    alert_type: AlertType,
    keyword: String,
    router_agent: Box<dyn Bridge<RouteAgent>>,
}

pub enum Msg {
    OnNewPageClick,
    OnEditPageClick,
    OnDeletePageClick,
    OnPagesLoaded(Result<ListModel<SimplePage>, AjaxError>),
    OnPageSelected(usize),
    OnMenuAgentResponse(MenuAgentResponse),
    OnDeleteApprove,
    OnDeleteDecline,
    OnPageDeleted(Result<bool, AjaxError>),
    Ignore,
}

impl Component for SimplePagesPage {
    type Message = Msg;
    type Properties = ();

    fn create(_props: Self::Properties, link: ComponentLink<Self>) -> Self {
        let translator = Translator::new();
        let menu_agent = MenuAgent::bridge(link.callback(|response| Msg::OnMenuAgentResponse(response)));
        let router_agent = RouteAgent::bridge(link.callback(|_| Msg::Ignore));

        SimplePagesPage {
            link,
            selected_index: None,
            headers: vec![
                TableHeader {
                    title: translator.translate("simple_pages.overview.table.title_column"),
                    key: "title".to_string(),
                },
                TableHeader {
                    title: translator.translate("simple_pages.overview.table.content_column"),
                    key: "content".to_string(),
                },
            ],
            translator,
            load_simple_pages_task: None,
            rows: vec![],
            simple_pages: vec![],
            simple_page_service: SimplePageService::new(),
            menu_agent,
            show_delete_dialog: false,
            delete_page_task: None,
            alert_message: None,
            alert_type: AlertType::Negative,
            keyword: "".to_string(),
            router_agent,
        }
    }

    fn update(&mut self, msg: Self::Message) -> bool {
        match msg {
            Msg::OnNewPageClick => {}
            Msg::OnEditPageClick => {}
            Msg::OnDeletePageClick => self.show_delete_dialog = true,
            Msg::OnPagesLoaded(result) => {
                if result.is_ok() {
                    self.simple_pages = result.unwrap().items;
                    self.rows = self.simple_pages.iter().enumerate().map(|(_, item)| {
                        TableRow::new(vec![
                            TableCell {
                                key: "title".to_string(),
                                value: item.title.to_string(),
                            },
                            TableCell {
                                key: "content".to_string(),
                                value: item.content.to_string(),
                            },
                        ])
                    }).collect();
                } else {
                    ConsoleService::error(result.err().unwrap().error.to_string().as_str());
                }
            }
            Msg::OnPageSelected(idx) => self.selected_index = Some(idx),
            Msg::OnMenuAgentResponse(response) => match response {
                MenuAgentResponse::OnSearch(value) => self.keyword = value,
                _ => {}
            },
            Msg::OnDeleteApprove => self.delete_page_task = Some(self.simple_page_service.delete_page(self.get_selected_page(), self.link.callback(|result| Msg::OnPageDeleted(result)))),
            Msg::OnDeleteDecline => self.show_delete_dialog = false,
            Msg::OnPageDeleted(result) => {
                if result.is_ok() {
                    self.selected_index = None;
                    self.show_delete_dialog = false;
                    self.load_simple_pages_task = Some(self.simple_page_service.get_list(self.keyword.clone(), self.link.callback(|result| Msg::OnPagesLoaded(result))));
                } else {
                    ConsoleService::error(result.err().unwrap().error.to_string().as_str());
                    let page = self.get_selected_page();
                    self.alert_message = Some(self.translator.translate_with_args("simple_pages.delete.failed", map! {"name" => page.title.as_str()}));
                    self.show_delete_dialog = false;
                }
            }
            Msg::Ignore => {}
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
                    <Button label=self.translator.translate("simple_pages.overview.action_new") on_click=self.link.callback(|_| Msg::OnNewPageClick) />
                    <Button disabled=self.selected_index.is_none() label=self.translator.translate("simple_pages.overview.action_edit") on_click=self.link.callback(|_| Msg::OnEditPageClick) />
                    <Button disabled=self.selected_index.is_none() label=self.translator.translate("simple_pages.overview.action_delete") button_type=ButtonType::Negative on_click=self.link.callback(|_| Msg::OnDeletePageClick) />
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
                            title=self.translator.translate("simple_pages.delete.title")
                            dialog_type=DialogType::Negative
                            message=self.translator.translate_with_args("simple_pages.delete.content", map! {"title" => page.title.as_str()})
                            decline_label=self.translator.translate("simple_pages.delete.decline")
                            approve_label=self.translator.translate("simple_pages.delete.approve")
                            on_approve=self.link.callback(|_| Msg::OnDeleteApprove)
                            on_decline=self.link.callback(|_| Msg::OnDeleteDecline)
                            is_open=self.show_delete_dialog
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
            self.menu_agent.send(MenuAgentRequest::ChangeTitle(self.translator.translate("app.menu.content.pages.simple_pages")));
            self.load_simple_pages_task = Some(self.simple_page_service.get_list(self.keyword.clone(), self.link.callback(|result| Msg::OnPagesLoaded(result))));
        }
    }
}

impl SimplePagesPage {
    fn get_selected_page(&self) -> SimplePage {
        self.simple_pages[self.selected_index.unwrap()].clone()
    }
}