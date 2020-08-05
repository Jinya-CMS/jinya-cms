use anyhow::Error;
use uuid::Uuid;
use yew::prelude::*;
use yew::services::fetch::FetchTask;

use crate::agents::menu_agent::{MenuAgent, MenuAgentRequest};
use crate::ajax::picsum_service::{PicsumMetaData, PicsumService};
use crate::i18n::Translator;

pub struct HomePage {
    link: ComponentLink<Self>,
    image_task: Option<FetchTask>,
    image_seed: String,
    image_credits: String,
    image_meta_task: Option<FetchTask>,
    picsum_service: PicsumService,
    translator: Translator,
    menu_agent: Box<dyn Bridge<MenuAgent>>,
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
        let seed = Uuid::new_v4().to_string();
        let mut menu_agent = MenuAgent::bridge(link.callback(|_| Msg::Ignore));
        menu_agent.send(MenuAgentRequest::HideSearch);
        HomePage {
            link,
            picsum_service: PicsumService::new(),
            image_task: None,
            image_seed: seed,
            image_credits: "".to_string(),
            image_meta_task: None,
            translator: Translator::new(),
            menu_agent,
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
                <div style={format!("background-image: url(\"https://picsum.photos/seed/{}/2560/1440\"); height: calc(100vh - 3.5rem); width: 100vw;background-size: cover;", &self.image_seed)}>
                    <span style="position: fixed; bottom: 0; left: 0;  background: var(--white); color: var(--primary-color); padding: 0.25rem 0.5rem; border-top-right-radius: 5px">{self.translator.translate("home.credits")}<b>{&self.image_credits}</b></span>
                </div>
            </>
        }
    }

    fn rendered(&mut self, first_render: bool) {
        if first_render {
            self.menu_agent.send(MenuAgentRequest::HideSearch);
            self.image_task = Some(self.picsum_service.get_picsum_id(&self.image_seed, self.link.callback(|id| Msg::OnImageTaskFetched(id))));
        }
    }
}
