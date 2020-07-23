use http::{Method, Response};
use web_sys::*;
use yew::prelude::*;

pub struct Model {
    link: ComponentLink<Self>,
}

pub enum Msg {
}

impl Component for Model {
    type Message = Msg;
    type Properties = ();

    fn create(props: Self::Properties, link: ComponentLink<Self>) -> Self {
        Model {
            link,
        }
    }

    fn update(&mut self, msg: Self::Message) -> bool {
        true
    }

    fn change(&mut self, _props: Self::Properties) -> bool {
        false
    }

    fn view(&self) -> Html {
        html! {
        }
    }
}
