use anyhow::Error;
use http::{Response, Uri};
use serde_derive::{Deserialize, Serialize};
use uuid::Uuid;
use web_sys::*;
use yew::format::{Json, Nothing};
use yew::prelude::*;
use yew::services::fetch::{FetchService, FetchTask};
use jinya_ui::widgets::form::input::Input;
use jinya_ui::widgets::button::Button;
use jinya_ui::layout::button_row::ButtonRow;

use crate::views::authentication::picsum_model::PicsumResponse;
use crate::i18n::Translator;

pub struct LoginPage {
    link: ComponentLink<Self>,
    username: String,
    password: String,
    image_task: Option<FetchTask>,
    image_url: String,
    image_credits: String,
    image_meta_task: Option<FetchTask>,
    translator: Translator<'static>,
}

pub enum Msg {
    OnLogin,
    OnUsername(String),
    OnPassword(String),
    OnImageTaskFetched(String),
    OnImageAuthorFetched(PicsumResponse),
    Ignore,
}

impl Component for LoginPage {
    type Message = Msg;
    type Properties = ();

    fn create(_props: Self::Properties, link: ComponentLink<Self>) -> Self {
        let seed = Uuid::new_v4().to_string();
        let url = format!("https://picsum.photos/seed/{}/2560/1440", seed);
        let image_task = FetchService::fetch(http::Request::get(url.clone()).body(Nothing).unwrap(), link.callback(move |response: Response<Json<Result<String, Error>>>| {
            let picsum_id = response.headers().get("picsum-id").unwrap().to_str().unwrap().to_string();
            Msg::OnImageTaskFetched(picsum_id)
        })).unwrap();

        LoginPage {
            link,
            username: "".to_string(),
            password: "".to_string(),
            image_task: Some(image_task),
            image_url: url.to_string(),
            image_credits: "".to_string(),
            image_meta_task: None,
            translator: Translator::new(),
        }
    }

    fn update(&mut self, msg: Self::Message) -> bool {
        match msg {
            Msg::OnImageTaskFetched(picsum_id) => {
                self.image_meta_task = Some(FetchService::fetch(http::Request::get(format!("https://picsum.photos/id/{}/info", picsum_id)).body(Nothing).unwrap(), self.link.callback(move |response: Response<Json<Result<PicsumResponse, Error>>>| {
                    let (meta, Json(data)) = response.into_parts();
                    if meta.status.is_success() {
                        Msg::OnImageAuthorFetched(data.unwrap())
                    } else {
                        Msg::Ignore
                    }
                })).unwrap())
            }
            Msg::OnImageAuthorFetched(picsum_response) => {
                self.image_credits = picsum_response.author;
            }
            Msg::OnLogin => {}
            Msg::OnUsername(value) => self.username = value,
            Msg::OnPassword(value) => self.password = value,
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
                <div style={format!("background-image: url(\"{}\"); height: 100vh; width: 100vw;background-size: cover;", &self.image_url)}>
                    <div style="width: 25rem; position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); background: var(--white); padding: 2rem; border-radius: 1rem;">
                        <span style="font-size: var(--font-size-24)">{self.translator.translate("login.welcome")}</span>
                        <Input label=self.translator.translate("login.username") on_input=self.link.callback(|value| Msg::OnUsername(value)) value=&self.username />
                        <Input label=self.translator.translate("login.password") input_type="password" on_input=self.link.callback(|value| Msg::OnPassword(value)) value=&self.password />
                        <ButtonRow>
                            <Button label=self.translator.translate("login.button") on_click=self.link.callback(|_| Msg::OnLogin) />
                        </ButtonRow>
                        <span>{self.translator.translate("login.credits")}<b>{&self.image_credits}</b></span>
                    </div>
                </div>
            </>
        }
    }
}
