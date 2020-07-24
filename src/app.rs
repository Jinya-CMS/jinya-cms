use yew::prelude::*;
use yew::services::fetch::FetchTask;
use yew_router::{prelude::*, Switch};
use yew_router::agent::RouteRequest;

use crate::ajax::authentication_service::AuthenticationService;
use crate::storage::AuthenticationStorage;
use crate::views::authentication::login::LoginPage;
use crate::views::authentication::two_factor::TwoFactorPage;

pub struct JinyaDesignerApp {
    link: ComponentLink<Self>,
    route_service: RouteService<()>,
    route: Route<()>,
    check_api_key_task: Option<FetchTask>,
    router: Box<dyn Bridge<RouteAgent>>,
}

#[derive(Switch, Clone)]
pub enum AppRoute {
    #[to = "/login"]
    Login,
    #[to = "/two-factor"]
    TwoFactor,
    #[to = "/"]
    Homepage,
}

pub enum Msg {
    OnRouteChanged(Route<()>),
    OnAuthenticationChecked(bool),
    Ignore,
}

impl Component for JinyaDesignerApp {
    type Message = Msg;
    type Properties = ();

    fn create(_props: Self::Properties, link: ComponentLink<Self>) -> Self {
        let mut route_service: RouteService<()> = RouteService::new();
        let callback = link.callback(Msg::OnRouteChanged);
        route_service.register_callback(callback);

        let api_key = AuthenticationStorage::get_api_key();
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

        let router = RouteAgent::bridge(link.callback(|_| Msg::Ignore));

        JinyaDesignerApp {
            link,
            route_service,
            route,
            check_api_key_task,
            router,
        }
    }

    fn update(&mut self, msg: Self::Message) -> bool {
        match msg {
            Msg::OnRouteChanged(route) => self.route = route,
            Msg::OnAuthenticationChecked(valid) => if !valid {
                self.router.send(RouteRequest::ChangeRoute(Route::from(AppRoute::Login)));
            },
            Msg::Ignore => {}
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
                        AppRoute::TwoFactor => html! {<TwoFactorPage />},
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
