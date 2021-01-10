use anyhow::Error;
use jinya_ui::layout::button_row::ButtonRow;
use jinya_ui::layout::row::Row;
use jinya_ui::widgets::alert::{Alert, AlertType};
use jinya_ui::widgets::button::Button;
use jinya_ui::widgets::form::input::Input;
use uuid::Uuid;
use yew::prelude::*;
use yew::services::fetch::FetchTask;
use yew_router::agent::{RouteAgent, RouteRequest};
use yew_router::route::Route;

use crate::ajax::authentication_service::AuthenticationService;
use crate::ajax::picsum_service::{PicsumMetaData, PicsumService};
use crate::app::AppRoute;
use crate::i18n::Translator;
use crate::storage::AuthenticationStorage;

pub struct TwoFactorPage {
    link: ComponentLink<Self>,
    code: String,
    image_task: Option<FetchTask>,
    image_seed: String,
    image_credits: String,
    image_meta_task: Option<FetchTask>,
    login_task: Option<FetchTask>,
    translator: Translator,
    picsum_service: PicsumService,
    login_failed: bool,
    router: Box<dyn Bridge<RouteAgent>>,
    authentication_service: AuthenticationService,
}

pub enum Msg {
    OnLogin,
    OnCode(String),
    OnImageTaskFetched(String),
    OnImageMetaFetched(Result<PicsumMetaData, Error>),
    OnLoginFinished(bool),
    Ignore,
}

impl Component for TwoFactorPage {
    type Message = Msg;
    type Properties = ();

    fn create(_props: Self::Properties, link: ComponentLink<Self>) -> Self {
        let seed = Uuid::new_v4().to_string();

        let callback = link.callback(|_| Msg::Ignore);
        let router = RouteAgent::bridge(callback);

        TwoFactorPage {
            link,
            code: "".to_string(),
            image_task: None,
            image_seed: seed,
            image_credits: "".to_string(),
            image_meta_task: None,
            translator: Translator::new(),
            picsum_service: PicsumService::new(),
            login_failed: false,
            router,
            login_task: None,
            authentication_service: AuthenticationService::new(),
        }
    }

    fn update(&mut self, msg: Self::Message) -> bool {
        match msg {
            Msg::OnImageTaskFetched(picsum_id) => {
                self.image_meta_task = Some(self.picsum_service.fetch_picsum_metadata(picsum_id, self.link.callback(Msg::OnImageMetaFetched)))
            }
            Msg::OnImageMetaFetched(result) => {
                if let Ok(picsum_meta) = result {
                    self.image_credits = picsum_meta.author;
                }
            }
            Msg::OnLogin => {
                self.login_task = Some(self.authentication_service.login(
                    AuthenticationStorage::get_username().unwrap(),
                    AuthenticationStorage::get_password().unwrap(),
                    Some(self.code.to_string()),
                    self.link.callback(Msg::OnLoginFinished)));
            }
            Msg::OnCode(value) => {
                self.code = value;
                self.login_failed = false;
            }
            Msg::OnLoginFinished(success) => {
                if success {
                    AuthenticationStorage::clear_user_auth();
                    self.router.send(RouteRequest::ChangeRoute(Route::from(AppRoute::Homepage)))
                } else {
                    self.login_failed = true
                }
            }
            Msg::Ignore => {}
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
                        <span style="font-size: var(--font-size-24)">{self.translator.translate("2fa.header")}</span>
                        {if self.login_failed {
                            html! {
                                <Row>
                                    <Alert message=self.translator.translate("2fa.error_code") alert_type=AlertType::Negative/>
                                </Row>
                            }
                        } else {
                            html! {}
                        }}
                        <Input input_type="number" label=self.translator.translate("2fa.code") on_input=self.link.callback(|value| Msg::OnCode(value)) value=&self.code />
                        <ButtonRow>
                            <Button label=self.translator.translate("2fa.action_login") on_click=self.link.callback(|_| Msg::OnLogin) />
                        </ButtonRow>
                        <span>{self.translator.translate("2fa.credits")}<b>{&self.image_credits}</b></span>
                    </div>
                </div>
            </>
        }
    }

    fn rendered(&mut self, first_render: bool) {
        if first_render {
            self.image_task = Some(self.picsum_service.get_picsum_id(&self.image_seed, self.link.callback(Msg::OnImageTaskFetched)));
        }
    }
}
