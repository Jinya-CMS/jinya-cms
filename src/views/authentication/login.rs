use anyhow::Error;
use jinya_ui::layout::button_row::ButtonRow;
use jinya_ui::layout::row::Row;
use jinya_ui::widgets::alert::{Alert, AlertType};
use jinya_ui::widgets::button::Button;
use jinya_ui::widgets::form::input::Input;
use uuid::Uuid;
use yew::prelude::*;
use yew::services::fetch::FetchTask;
use yew_router::agent::RouteRequest;
use yew_router::prelude::RouteAgent;
use yew_router::route::Route;

use crate::ajax::authentication_service::AuthenticationService;
use crate::ajax::picsum_service::{PicsumMetaData, PicsumService};
use crate::app::AppRoute;
use crate::i18n::Translator;
use crate::storage::AuthenticationStorage;

pub struct LoginPage {
    link: ComponentLink<Self>,
    username: String,
    password: String,
    image_task: Option<FetchTask>,
    image_seed: String,
    image_credits: String,
    image_meta_task: Option<FetchTask>,
    translator: Translator,
    device_code: Option<String>,
    login_failed: bool,
    authentication_service: AuthenticationService,
    picsum_service: PicsumService,
    two_factor_task: Option<FetchTask>,
    login_task: Option<FetchTask>,
    router: Box<dyn Bridge<RouteAgent>>,
}

pub enum Msg {
    OnLogin,
    OnTwoFactor,
    OnUsername(String),
    OnPassword(String),
    OnImageTaskFetched(String),
    OnImageMetaFetched(Result<PicsumMetaData, Error>),
    OnTwoFactorRequested(bool),
    OnLoginFinished(bool),
    Ignore,
}

impl Component for LoginPage {
    type Message = Msg;
    type Properties = ();

    fn create(_props: Self::Properties, link: ComponentLink<Self>) -> Self {
        let picsum_service = PicsumService::new();
        let seed = Uuid::new_v4().to_string();
        let image_task = picsum_service.get_picsum_id(&seed, link.callback(|id| Msg::OnImageTaskFetched(id)));

        AuthenticationStorage::clear_api_key();
        AuthenticationStorage::clear_roles();

        let device_code = AuthenticationStorage::get_device_code();
        let callback = link.callback(|_| Msg::Ignore);
        let router = RouteAgent::bridge(callback);

        LoginPage {
            link,
            picsum_service,
            username: "".to_string(),
            password: "".to_string(),
            image_task: Some(image_task),
            image_seed: seed,
            image_credits: "".to_string(),
            image_meta_task: None,
            translator: Translator::new(),
            device_code,
            login_failed: false,
            authentication_service: AuthenticationService::new(),
            two_factor_task: None,
            login_task: None,
            router,
        }
    }

    fn update(&mut self, msg: Self::Message) -> bool {
        match msg {
            Msg::OnImageTaskFetched(picsum_id) => {
                self.image_meta_task = Some(self.picsum_service.fetch_picsum_metadata(picsum_id, self.link.callback(|infos| Msg::OnImageMetaFetched(infos))))
            }
            Msg::OnImageMetaFetched(picsum_meta) => {
                if picsum_meta.is_ok() {
                    self.image_credits = picsum_meta.unwrap().author;
                }
            }
            Msg::OnLogin => {
                self.login_task = Some(self.authentication_service.login(self.username.to_string(), self.password.to_string(), None, self.link.callback(move |success| {
                    Msg::OnLoginFinished(success)
                })))
            }
            Msg::OnTwoFactor => {
                self.two_factor_task = Some(self.authentication_service.request_second_factor(self.username.to_string(), self.password.to_string(), self.link.callback(move |success| {
                    Msg::OnTwoFactorRequested(success)
                })))
            }
            Msg::OnUsername(value) => {
                self.username = value;
                self.login_failed = false;
            }
            Msg::OnPassword(value) => {
                self.password = value;
                self.login_failed = false;
            }
            Msg::Ignore => {}
            Msg::OnTwoFactorRequested(success) => {
                if success {
                    AuthenticationStorage::set_password(&self.password);
                    AuthenticationStorage::set_username(&self.username);
                    self.router.send(RouteRequest::ChangeRoute(Route::from(AppRoute::TwoFactor)));
                } else {
                    self.login_failed = true
                }
            }
            Msg::OnLoginFinished(success) => {
                if success {
                    AuthenticationStorage::clear_user_auth();
                    self.router.send(RouteRequest::ChangeRoute(Route::from(AppRoute::Homepage)))
                } else {
                    self.login_failed = true
                }
            }
        }

        true
    }

    fn change(&mut self, _props: Self::Properties) -> bool {
        false
    }

    fn view(&self) -> Html {
        html! {
            <>
                <div style={format!("background-image: url(\"https://picsum.photos/seed/{}/2560/1440\"); height: 100vh; width: 100vw;background-size: cover;", &self.image_seed)}>
                    <div style="width: 25rem; position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); background: var(--white); padding: 2rem; border-radius: 1rem;">
                        <span style="font-size: var(--font-size-24)">{self.translator.translate("login.welcome")}</span>
                        {if self.login_failed {
                            html! {
                                <Row>
                                    <Alert message=self.translator.translate("login.error_invalid_password") alert_type=AlertType::Negative/>
                                </Row>
                            }
                        } else {
                            html! {}
                        }}
                        <Input label=self.translator.translate("login.username") on_input=self.link.callback(|value| Msg::OnUsername(value)) value=&self.username />
                        <Input label=self.translator.translate("login.password") input_type="password" on_input=self.link.callback(|value| Msg::OnPassword(value)) value=&self.password />
                        <ButtonRow>
                            {if self.device_code.is_none() {
                                html! {
                                    <Button label=self.translator.translate("login.action_two_factor") on_click=self.link.callback(|_| Msg::OnTwoFactor) />
                                }
                            } else {
                                html! {
                                    <Button label=self.translator.translate("login.action_login") on_click=self.link.callback(|_| Msg::OnLogin) />
                                }
                            }}
                        </ButtonRow>
                        <span>{self.translator.translate("login.credits")}<b>{&self.image_credits}</b></span>
                    </div>
                </div>
            </>
        }
    }
}
