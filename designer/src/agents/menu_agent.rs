use serde_derive::{Deserialize, Serialize};
use wasm_bindgen::__rt::std::collections::HashSet;
use yew::agent::HandlerId;
use yew::worker::{Agent, AgentLink, Context};

#[derive(Serialize, Deserialize)]
pub enum MenuAgentRequest {
    Search(String),
    Keyword(String),
    ChangeTitle(String),
    HideSearch,
}

#[derive(Serialize, Deserialize)]
pub enum MenuAgentResponse {
    OnSearch(String),
    OnKeyword(String),
    OnChangeTitle(String),
    OnHideSearch,
}

pub struct MenuAgent {
    link: AgentLink<MenuAgent>,
    subscribers: HashSet<HandlerId>,
}

impl Agent for MenuAgent {
    type Reach = Context<Self>;
    type Message = ();
    type Input = MenuAgentRequest;
    type Output = MenuAgentResponse;

    fn create(link: AgentLink<Self>) -> Self {
        MenuAgent {
            link,
            subscribers: HashSet::new(),
        }
    }

    fn update(&mut self, _msg: Self::Message) {}

    fn connected(&mut self, id: HandlerId) {
        self.subscribers.insert(id);
    }

    fn handle_input(&mut self, msg: Self::Input, _id: HandlerId) {
        match msg {
            MenuAgentRequest::Search(value) => for sub in &self.subscribers {
                self.link.respond(*sub, MenuAgentResponse::OnSearch(value.clone()))
            }
            MenuAgentRequest::Keyword(value) => for sub in &self.subscribers {
                self.link.respond(*sub, MenuAgentResponse::OnKeyword(value.clone()))
            },
            MenuAgentRequest::ChangeTitle(value) => for sub in &self.subscribers {
                self.link.respond(*sub, MenuAgentResponse::OnChangeTitle(value.clone()))
            },
            MenuAgentRequest::HideSearch => for sub in &self.subscribers {
                self.link.respond(*sub, MenuAgentResponse::OnHideSearch)
            },
        }
    }

    fn disconnected(&mut self, id: HandlerId) {
        self.subscribers.remove(&id);
    }
}