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

pub struct AddSimplePagePage {
    link: ComponentLink<Self>,
    translator: Translator,
    title: String,
    content: String,
    simple_page_service: SimplePageService,
    menu_dispatcher: Dispatcher<MenuAgent>,
    alert_message: Option<String>,
    alert_type: AlertType,
    router_agent: Box<dyn Bridge<RouteAgent>>,
    tinymce: TinyMce,
    title_validation_message: String,
    title_input_state: InputState,
    saving: bool,
    create_page_task: Option<FetchTask>,
}

pub enum Msg {
    OnSaveClick,
    OnPageCreated(Result<bool, AjaxError>),
    OnTitleInput(String),
    Ignore,
}

impl Component for AddSimplePagePage {
    type Message = Msg;
    type Properties = ();

    fn create(props: Self::Properties, link: ComponentLink<Self>) -> Self {
        let translator = Translator::new();
        let menu_dispatcher = MenuAgent::dispatcher();
        let router_agent = RouteAgent::bridge(link.callback(|_| Msg::Ignore));

        AddSimplePagePage {
            link,
            translator,
            title: "".to_string(),
            content: "".to_string(),
            simple_page_service: SimplePageService::new(),
            menu_dispatcher,
            alert_message: None,
            alert_type: AlertType::Negative,
            router_agent,
            tinymce: TinyMce::new("page-content".to_string()),
            title_validation_message: "".to_string(),
            title_input_state: InputState::Default,
            saving: false,
            create_page_task: None,
        }
    }

    fn update(&mut self, msg: Self::Message) -> bool {
        match msg {
            Msg::OnSaveClick => {
                self.create_page_task = Some(self.simple_page_service.create_page(SimplePage::from_title_and_content(self.title.to_string(), self.tinymce.get_content()), self.link.callback(|result| Msg::OnPageCreated(result))))
            }
            Msg::Ignore => {}
            Msg::OnTitleInput(title) => {
                if title.is_empty() {
                    self.title_validation_message = self.translator.translate("simple_pages.add.error_empty_title");
                    self.title_input_state = InputState::Negative;
                } else {
                    self.title_input_state = InputState::Default;
                    self.title_validation_message = "".to_string();
                }
                self.title = title
            }
            Msg::OnPageCreated(result) => {
                if result.is_ok() {
                    self.router_agent.send(RouteRequest::ChangeRoute(Route::from(AppRoute::SimplePages)))
                } else {
                    self.alert_type = AlertType::Negative;
                    if result.unwrap_err().status_code == StatusCode::CONFLICT {
                        self.alert_message = Some(self.translator.translate("simple_pages.add.error_conflict"));
                    } else {
                        self.alert_message = Some(self.translator.translate("simple_pages.add.error_generic"));
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
                    <Input validation_message=&self.title_validation_message state=&self.title_input_state disabled=self.saving label=self.translator.translate("simple_pages.add.title") on_input=self.link.callback(|value| Msg::OnTitleInput(value)) value=&self.title />
                </Row>
                <Row>
                    <textarea id="page-content"></textarea>
                </Row>
                <ButtonRow>
                    <Button label=self.translator.translate("simple_pages.add.create") on_click=self.link.callback(|_| Msg::OnSaveClick)/>
                </ButtonRow>
            </Page>
        }
    }

    fn rendered(&mut self, first_render: bool) {
        if first_render {
            self.menu_dispatcher.send(MenuAgentRequest::ChangeTitle(self.translator.translate("app.menu.content.pages.simple_pages.add")));
            self.tinymce.init_tiny_mce("".to_string())
        }
    }

    fn destroy(&mut self) {
        self.tinymce.destroy_editor();
    }
}