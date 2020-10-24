use jinya_ui::layout::page::Page;
use jinya_ui::widgets::toast::Toast;
use web_sys::Node;
use yew::{Component, ComponentLink, Html};
use yew::agent::Dispatcher;
use yew::prelude::*;
use yew::services::fetch::FetchTask;
use yew::virtual_dom::VNode;
use yew_router::agent::RouteRequest;
use yew_router::prelude::*;

use crate::agents::menu_agent::{MenuAgent, MenuAgentRequest};
use crate::ajax::{AjaxError, get_host};
use crate::ajax::artist_service::ArtistService;
use crate::app::AppRoute;
use crate::i18n::Translator;
use crate::models::artist::Artist;

pub struct ProfilePage {
    link: ComponentLink<Self>,
    translator: Translator,
    load_profile_task: Option<FetchTask>,
    artist_service: ArtistService,
    menu_dispatcher: Dispatcher<MenuAgent>,
    profile: Option<Artist>,
    route_dispatcher: Dispatcher<RouteAgent>,
    id: usize,
}

pub enum Msg {
    OnProfileLoaded(Result<Artist, AjaxError>),
}

#[derive(Clone, PartialEq, Properties)]
pub struct Props {
    pub id: usize,
}

impl Component for ProfilePage {
    type Message = Msg;
    type Properties = Props;

    fn create(props: Self::Properties, link: ComponentLink<Self>) -> Self {
        let translator = Translator::new();
        let menu_dispatcher = MenuAgent::dispatcher();
        let route_dispatcher = RouteAgent::dispatcher();

        ProfilePage {
            link,
            translator,
            load_profile_task: None,
            artist_service: ArtistService::new(),
            route_dispatcher,
            menu_dispatcher,
            profile: None,
            id: props.id,
        }
    }

    fn update(&mut self, msg: Self::Message) -> bool {
        match msg {
            Msg::OnProfileLoaded(artist) => {
                if artist.is_ok() {
                    self.profile = Some(artist.unwrap());
                } else {
                    Toast::negative_toast(self.translator.translate("artists.profile.not_found"));
                    self.route_dispatcher.send(RouteRequest::ChangeRoute(Route::from(AppRoute::Artists)));
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
                {if self.profile.is_some() {
                    let profile = self.profile.as_ref().unwrap();
                    let about_me = profile.about_me.as_ref().unwrap();
                    let content = {
                        let div = web_sys::window()
                            .unwrap()
                            .document()
                            .unwrap()
                            .create_element("div")
                            .unwrap();
                        div.set_inner_html(about_me);
                        div
                    };
                    let node = Node::from(content);
                    let vnode = VNode::VRef(node);
                    html! {
                        <div class="jinya-designer-my-profile">
                            <div class="jinya-designer-my-profile__left">
                                <img class="jinya-designer-my-profile__profile-picture" src={format!("{}{}", get_host(), profile.profile_picture)} />
                                <span class="jinya-designer-my-profile__artist-name">{&profile.artist_name}</span>
                                <span class="jinya-designer-my-profile__email">{&profile.email}</span>
                            </div>
                            <div class="jinya-designer-my-profile__right">
                                {vnode}
                            </div>
                        </div>
                    }
                } else {
                    html! {}
                }}
            </Page>
        }
    }

    fn rendered(&mut self, first_render: bool) {
        if first_render {
            self.menu_dispatcher.send(MenuAgentRequest::HideSearch);
            self.load_profile_task = Some(self.artist_service.get_artist(self.id, self.link.callback(|result| Msg::OnProfileLoaded(result))));
        }
    }
}