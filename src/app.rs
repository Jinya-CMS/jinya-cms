use yew::prelude::*;
use yew::services::fetch::FetchTask;
use yew_router::{prelude::*, Switch};

use crate::ajax::authentication_service::AuthenticationService;
use crate::storage::AuthenticationStorage;
use crate::views::authentication::login::LoginPage;

pub struct JinyaDesignerApp {
    link: ComponentLink<Self>,
    route_service: RouteService<()>,
    route: Route<()>,
    check_api_key_task: Option<FetchTask>,
}

#[derive(Switch, Clone)]
pub enum AppRoute {
    #[to = "/login"]
    Login,
    #[to = "/"]
    Homepage,
}

pub enum Msg {
    OnRouteChanged(Route<()>),
    OnAuthenticationChecked(bool),
}

impl Component for JinyaDesignerApp {
    type Message = Msg;
    type Properties = ();

    fn create(_props: Self::Properties, link: ComponentLink<Self>) -> Self {
        let mut route_service: RouteService<()> = RouteService::new();
        let callback = link.callback(Msg::OnRouteChanged);
        route_service.register_callback(callback);

        let api_key = AuthenticationStorage::get_jinya_api_key();
        let mut check_api_key_task = None;
        let route = if api_key.is_some() {
            check_api_key_task = Some(AuthenticationService::new().check_api_key(link.callback(|valid| {
                Msg::OnAuthenticationChecked(valid)
            })));

            route_service.get_route()
        } else {
            route_service.set_route("login", ());
            AppRoute::Login.into()
        };

        let result = JinyaDesignerApp {
            link,
            route_service,
            route,
            check_api_key_task
        };
        result
    }

    fn update(&mut self, msg: Self::Message) -> bool {
        match msg {
            Msg::OnRouteChanged(route) => self.route = route,
            Msg::OnAuthenticationChecked(valid) => if valid { self.route = AppRoute::Login.into() },
        }

        true
    }

    fn change(&mut self, _props: Self::Properties) -> bool {
        false
    }

    fn view(&self) -> Html {
        html! {
            <Router<AppRoute, ()>
                render=Router::render(|switch: AppRoute| {
                    match switch {
                        AppRoute::Login => html! {<LoginPage />},
                        AppRoute::Homepage => html! {<div></div>},
                    }
                })
                redirect=Router::redirect(|route: Route| {
                    AppRoute::Homepage
                })
            />
        }
    }
}
