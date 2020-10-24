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
use crate::ajax::menu_service::MenuService;
use crate::app::AppRoute;
use crate::i18n::Translator;
use crate::models::list_model::ListModel;
use crate::models::menu::Menu;

pub struct MenusPage {
    link: ComponentLink<Self>,
    selected_index: Option<usize>,
    headers: Vec<TableHeader>,
    translator: Translator,
    load_menus_task: Option<FetchTask>,
    rows: Vec<TableRow>,
    menus: Vec<Menu>,
    menu_service: MenuService,
    menu_agent: Box<dyn Bridge<MenuAgent>>,
    show_delete_dialog: bool,
    delete_page_task: Option<FetchTask>,
    alert_message: Option<String>,
    alert_type: AlertType,
    keyword: String,
}

pub enum Msg {
    OnNewMenuClick,
    OnEditMenuClick,
    OnDeleteMenuClick,
    OnMenusLoaded(Result<ListModel<Menu>, AjaxError>),
    OnMenuSelected(usize),
    OnMenuAgentResponse(MenuAgentResponse),
    OnDeleteApprove,
    OnDeleteDecline,
    OnMenuDeleted(Result<bool, AjaxError>),
    Ignore,
}

impl Component for MenusPage {
    type Message = Msg;
    type Properties = ();

    fn create(_props: Self::Properties, link: ComponentLink<Self>) -> Self {
        let translator = Translator::new();
        let menu_agent = MenuAgent::bridge(link.callback(|response| Msg::OnMenuAgentResponse(response)));

        MenusPage {
            link,
            selected_index: None,
            headers: vec![
                TableHeader {
                    title: translator.translate("menus.overview.table.name_column"),
                    key: "name".to_string(),
                },
                TableHeader {
                    title: translator.translate("menus.overview.table.logo_column"),
                    key: "logo".to_string(),
                },
            ],
            translator,
            load_menus_task: None,
            rows: vec![],
            menus: vec![],
            menu_service: MenuService::new(),
            menu_agent,
            show_delete_dialog: false,
            delete_page_task: None,
            alert_message: None,
            alert_type: AlertType::Negative,
            keyword: "".to_string(),
        }
    }

    fn update(&mut self, msg: Self::Message) -> bool {
        match msg {
            Msg::OnNewMenuClick => {}
            Msg::OnEditMenuClick => {}
            Msg::OnDeleteMenuClick => self.show_delete_dialog = true,
            Msg::OnMenusLoaded(result) => {
                if result.is_ok() {
                    self.menus = result.unwrap().items;
                    self.rows = self.menus.iter().enumerate().map(|(_, item)| {
                        let logo_name = if item.logo.is_some() {
                            item.logo.as_ref().unwrap().name.to_string()
                        } else {
                            "".to_string()
                        };
                        TableRow::new(vec![
                            TableCell {
                                key: "name".to_string(),
                                value: item.name.to_string(),
                            },
                            TableCell {
                                key: "logo".to_string(),
                                value: logo_name,
                            },
                        ])
                    }).collect();
                } else {
                    log::error!("{}", result.err().unwrap().error.to_string().as_str());
                }
            }
            Msg::OnMenuSelected(idx) => self.selected_index = Some(idx),
            Msg::OnMenuAgentResponse(response) => if let MenuAgentResponse::OnSearch(value) = response { self.keyword = value },
            Msg::OnDeleteApprove => self.delete_page_task = Some(self.menu_service.delete_menu(self.get_selected_menu().id, self.link.callback(Msg::OnMenuDeleted))),
            Msg::OnDeleteDecline => self.show_delete_dialog = false,
            Msg::OnMenuDeleted(result) => {
                if result.is_ok() {
                    self.selected_index = None;
                    self.show_delete_dialog = false;
                    self.load_menus_task = Some(self.menu_service.get_list(self.keyword.clone(), self.link.callback(Msg::OnMenusLoaded)));
                } else {
                    log::error!("{}", result.err().unwrap().error.to_string().as_str());
                    let menu = self.get_selected_menu();
                    self.alert_message = Some(self.translator.translate_with_args("menus.delete.failed", map! {"name" => menu.name.as_str()}));
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
                    <Button label=self.translator.translate("menus.overview.action_new") on_click=self.link.callback(|_| Msg::OnNewMenuClick) />
                    <Button disabled=self.selected_index.is_none() label=self.translator.translate("menus.overview.action_edit") on_click=self.link.callback(|_| Msg::OnEditMenuClick) />
                    <Button disabled=self.selected_index.is_none() label=self.translator.translate("menus.overview.action_delete") button_type=ButtonType::Negative on_click=self.link.callback(|_| Msg::OnDeleteMenuClick) />
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
                <Table selected_index=self.selected_index headers=&self.headers rows=&self.rows on_select=self.link.callback(Msg::OnMenuSelected) />
                {if self.show_delete_dialog {
                    let menu = self.get_selected_menu();
                    html! {
                        <ConfirmationDialog
                            title=self.translator.translate("menus.delete.title")
                            dialog_type=DialogType::Negative
                            message=self.translator.translate_with_args("menus.delete.content", map! {"name" => menu.name.as_str()})
                            decline_label=self.translator.translate("menus.delete.decline")
                            approve_label=self.translator.translate("menus.delete.approve")
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
            self.menu_agent.send(MenuAgentRequest::ChangeTitle(self.translator.translate("app.menu.configuration.frontend.menus")));
            self.load_menus_task = Some(self.menu_service.get_list(self.keyword.clone(), self.link.callback(Msg::OnMenusLoaded)));
        }
    }
}

impl MenusPage {
    fn get_selected_menu(&self) -> Menu {
        self.menus[self.selected_index.unwrap()].clone()
    }
}