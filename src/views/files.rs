use yew::{ComponentLink, Component, Html};

pub struct FilesPage {
    link: ComponentLink<Self>,
}

pub enum Msg {
    OnFilesLoaded,
}

impl Component for FilesPage {
    type Message = Msg;
    type Properties = ();

    fn create(props: Self::Properties, link: ComponentLink<Self>) -> Self {
        unimplemented!()
    }

    fn update(&mut self, msg: Self::Message) -> bool {
        unimplemented!()
    }

    fn change(&mut self, _props: Self::Properties) -> bool {
        unimplemented!()
    }

    fn view(&self) -> Html {
        unimplemented!()
    }
}