use anyhow::Error;
use jinya_ui::layout::button_row::ButtonRow;
use jinya_ui::layout::form::Form;
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

use crate::ajax::authentication_service::{AuthenticationService, TwoFactorRequest};
use crate::ajax::picsum_service::{PicsumMetaData, PicsumService};
use crate::app::AppRoute;
use crate::i18n::Translator;
use crate::storage::AuthenticationStorage;

pub struct HomePage {
    link: ComponentLink<Self>,
    image_task: Option<FetchTask>,
    image_seed: String,
    image_credits: String,
    image_meta_task: Option<FetchTask>,
    picsum_service: PicsumService,
    translator: Translator<'static>,
}

pub enum Msg {
    OnImageTaskFetched(String),
    OnImageMetaFetched(Result<PicsumMetaData, Error>),
    Ignore,
}

impl Component for HomePage {
    type Message = Msg;
    type Properties = ();

    fn create(_props: Self::Properties, link: ComponentLink<Self>) -> Self {
        let picsum_service = PicsumService::new();
        let seed = Uuid::new_v4().to_string();
        let image_task = picsum_service.get_picsum_id(&seed, link.callback(|id| Msg::OnImageTaskFetched(id)));

        HomePage {
            link,
            picsum_service,
            image_task: Some(image_task),
            image_seed: seed,
            image_credits: "".to_string(),
            image_meta_task: None,
            translator: Translator::new(),
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
                    <span style="position: fixed; bottom: 0; left: 0;  background: var(--white); color: var(--primary-color); padding: 0.25rem 0.5rem; border-top-right-radius: 5px">{self.translator.translate("home.credits")}<b>{&self.image_credits}</b></span>
                </div>
            </>
        }
    }
}
