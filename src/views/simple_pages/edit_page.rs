use http::StatusCode;
use jinya_ui::layout::button_row::ButtonRow;
use jinya_ui::layout::page::Page;
use jinya_ui::layout::row::Row;
use jinya_ui::widgets::alert::{Alert, AlertType};
use jinya_ui::widgets::button::Button;
use jinya_ui::widgets::form::input::Input;
use jinya_ui::widgets::form::input::InputState;
use yew::{Component, ComponentLink, Html};
use yew::agent::Dispatcher;
use yew::prelude::*;
use yew::services::fetch::FetchTask;
use yew_router::agent::{RouteAgent, RouteRequest};
use yew_router::route::Route;

use crate::agents::menu_agent::{MenuAgent, MenuAgentRequest};
use crate::ajax::AjaxError;
use crate::ajax::simple_page_service::SimplePageService;
use crate::app::AppRoute;
use crate::i18n::Translator;
use crate::models::simple_page::SimplePage;
use crate::utils::TinyMce;

pub struct EditSimplePagePage {
    link: ComponentLink<Self>,
    translator: Translator,
    load_simple_page_task: Option<FetchTask>,
    title: String,
    content: String,
    simple_page_service: SimplePageService,
    menu_dispatcher: Dispatcher<MenuAgent>,
    alert_message: Option<String>,
    alert_type: AlertType,
    router_agent: Box<dyn Bridge<RouteAgent>>,
    tinymce: TinyMce,
    id: usize,
    title_validation_message: String,
    title_input_state: InputState,
    saving: bool,
    update_page_task: Option<FetchTask>,
}

pub enum Msg {
    OnSaveClick,
    OnPageLoaded(Result<SimplePage, AjaxError>),
    OnPageUpdated(Result<bool, AjaxError>),
    OnTitleInput(String),
    Ignore,
}

#[derive(Properties, PartialEq, Clone)]
pub struct Props {
    pub id: usize,
}

impl Component for EditSimplePagePage {
    type Message = Msg;
    type Properties = Props;

    fn create(props: Self::Properties, link: ComponentLink<Self>) -> Self {
        let translator = Translator::new();
        let menu_dispatcher = MenuAgent::dispatcher();
        let router_agent = RouteAgent::bridge(link.callback(|_| Msg::Ignore));

        EditSimplePagePage {
            link,
            translator,
            load_simple_page_task: None,
            title: "".to_string(),
            content: "".to_string(),
            simple_page_service: SimplePageService::new(),
            menu_dispatcher,
            alert_message: None,
            alert_type: AlertType::Negative,
            router_agent,
            tinymce: TinyMce::new("page-content".to_string()),
            id: props.id,
            title_validation_message: "".to_string(),
            title_input_state: InputState::Default,
            saving: false,
            update_page_task: None,
        }
    }

    fn update(&mut self, msg: Self::Message) -> bool {
        match msg {
            Msg::OnSaveClick => {
                self.update_page_task = Some(self.simple_page_service.update_page(self.id, SimplePage::from_title_and_content(self.title.to_string(), self.tinymce.get_content()), self.link.callback(|result| Msg::OnPageUpdated(result))))
            }
            Msg::OnPageLoaded(result) => {
                if result.is_ok() {
                    let page = result.unwrap();
                    self.content = page.content;
                    self.title = page.title;
                    self.tinymce.set_content(self.content.to_string())
                } else {
                    log::error!("{}",result.err().unwrap().error.to_string().as_str());
                }
            }
            Msg::Ignore => {}
            Msg::OnTitleInput(title) => {
                if title.is_empty() {
                    self.title_validation_message = self.translator.translate("simple_pages.edit.error_empty_title");
                    self.title_input_state = InputState::Negative;
                } else {
                    self.title_input_state = InputState::Default;
                    self.title_validation_message = "".to_string();
                }
                self.title = title
            }
            Msg::OnPageUpdated(result) => {
                if result.is_ok() {
                    self.router_agent.send(RouteRequest::ChangeRoute(Route::from(AppRoute::SimplePages)))
                } else {
                    self.alert_type = AlertType::Negative;
                    if result.unwrap_err().status_code == StatusCode::CONFLICT {
                        self.alert_message = Some(self.translator.translate("simple_pages.edit.error_conflict"));
                    } else {
                        self.alert_message = Some(self.translator.translate("simple_pages.edit.error_generic"));
                    }
                }
            }
        }

        true
    }

    fn change(&mut self, _props: Self::Properties) -> bool {
        true
    }

    fn view(&self) -> Html {
        html! {
            <Page>
                {if self.alert_message.is_some() {
                    html! {
                        <Row>
                            <Alert message=self.alert_message.as_ref().unwrap() alert_type=&self.alert_type />
                        </Row>
                    }
                } else {
                    html! {}
                }}
                <Row>
                    <Input validation_message=&self.title_validation_message state=&self.title_input_state disabled=self.saving label=self.translator.translate("simple_pages.edit.title") on_input=self.link.callback(|value| Msg::OnTitleInput(value)) value=&self.title />
                </Row>
                <Row>
                    <textarea id="page-content"></textarea>
                </Row>
                <ButtonRow>
                    <Button label=self.translator.translate("simple_pages.edit.update") on_click=self.link.callback(|_| Msg::OnSaveClick)/>
                </ButtonRow>
            </Page>
        }
    }

    fn rendered(&mut self, first_render: bool) {
        if first_render {
            self.menu_dispatcher.send(MenuAgentRequest::ChangeTitle(self.translator.translate("app.menu.content.pages.simple_pages.edit")));
            self.load_simple_page_task = Some(self.simple_page_service.get_page(self.id, self.link.callback(|result| Msg::OnPageLoaded(result))));
            self.tinymce.init_tiny_mce("".to_string())
        }
    }

    fn destroy(&mut self) {
        self.tinymce.destroy_editor();
    }
}