use jinya_ui::widgets::menu::bar::MenuBar;
use jinya_ui::widgets::menu::item::{MenuItem, SubItem, SubItemGroup};
use yew::prelude::*;
use yew::services::fetch::FetchTask;
use yew::utils::window;
use yew_router::{prelude::*, Switch};
use yew_router::agent::RouteRequest;
use yew_router::router::Render;

use crate::agents::menu_agent::{MenuAgent, MenuAgentRequest, MenuAgentResponse};
use crate::ajax::{AjaxError, get_host};
use crate::ajax::authentication_service::AuthenticationService;
use crate::ajax::update_service::UpdateService;
use crate::i18n::Translator;
use crate::storage::AuthenticationStorage;
use crate::views::artists::ArtistsPage;
use crate::views::artists::profile::ProfilePage;
use crate::views::authentication::change_password_dialog::ChangePasswordDialog;
use crate::views::authentication::login::LoginPage;
use crate::views::authentication::two_factor::TwoFactorPage;
use crate::views::files::FilesPage;
use crate::views::galleries::designer::GalleryDesignerPage;
use crate::views::galleries::GalleriesPage;
use crate::views::home::HomePage;
use crate::views::menus::designer::MenuDesignerPage;
use crate::views::menus::MenusPage;
use crate::views::my_jinya::my_profile::MyProfilePage;
use crate::views::segment_pages::designer::SegmentPageDesignerPage;
use crate::views::segment_pages::SegmentPagesPage;
use crate::views::simple_pages::add_page::AddSimplePagePage;
use crate::views::simple_pages::edit_page::EditSimplePagePage;
use crate::views::simple_pages::SimplePagesPage;
use crate::views::themes::configuration::ConfigurationPage;
use crate::views::themes::links::LinksPage;
use crate::views::themes::scss::ScssPage;
use crate::views::themes::ThemesPage;

pub struct JinyaDesignerApp {
    link: ComponentLink<Self>,
    route_service: RouteService<()>,
    route: Route<()>,
    router: Box<dyn Bridge<RouteAgent>>,
    menu_agent: Box<dyn Bridge<MenuAgent>>,
    menu_title: String,
    translator: Translator,
    hide_search: bool,
    change_password_open: bool,
    check_api_key_task: Option<FetchTask>,
    update_service: UpdateService,
    update_task: Option<FetchTask>,
}

#[derive(Switch, Clone, PartialEq)]
pub enum AppRoute {
    #[to = "/designer/login"]
    Login,
    #[to = "/designer/two-factor"]
    TwoFactor,
    #[to = "/designer/content/files"]
    Files,
    #[to = "/designer/content/galleries/{id}/designer"]
    GalleryDesigner(usize),
    #[to = "/designer/content/galleries"]
    Galleries,
    #[to = "/designer/content/simple-pages/{id}/edit"]
    EditSimplePage(usize),
    #[to = "/designer/content/simple-pages/add"]
    AddSimplePage,
    #[to = "/designer/content/simple-pages"]
    SimplePages,
    #[to = "/designer/content/segment-pages/{id}/designer"]
    SegmentPageDesigner(usize),
    #[to = "/designer/content/segment-pages"]
    SegmentPages,
    #[to = "/designer/account/me"]
    MyProfile,
    #[to = "/designer/configuration/artists/{id}/profile"]
    ArtistProfile(usize),
    #[to = "/designer/configuration/artists"]
    Artists,
    #[to = "/designer/configuration/menu/{id}/designer"]
    MenuDesigner(usize),
    #[to = "/designer/configuration/menus"]
    Menus,
    #[to = "/designer/configuration/themes/{id}/styling"]
    ThemeScssPage(usize),
    #[to = "/designer/configuration/themes/{id}/links"]
    ThemeLinksPage(usize),
    #[to = "/designer/configuration/themes/{id}/settings"]
    ThemeConfigurationPage(usize),
    #[to = "/designer/configuration/themes"]
    Themes,
    #[to = "/designer/"]
    Homepage,
}

pub enum Msg {
    OnRouteChanged(Route<()>),
    OnAuthenticationChecked(bool),
    OnMenuAgentResponse(MenuAgentResponse),
    OnMenuKeyword(String),
    OnMenuSearch(String),
    OnChangePassword(MouseEvent),
    OnLogout(MouseEvent),
    OnChangePasswordSave,
    OnChangePasswordDiscard,
    OnStartUpdate(MouseEvent),
    OnUpdateStarted(Result<bool, AjaxError>),
}

impl Component for JinyaDesignerApp {
    type Message = Msg;
    type Properties = ();

    fn create(_props: Self::Properties, link: ComponentLink<Self>) -> Self {
        let mut route_service: RouteService<()> = RouteService::new();
        let callback = link.callback(Msg::OnRouteChanged);
        route_service.register_callback(callback);

        let api_key = AuthenticationStorage::get_api_key();
        let route = if api_key.is_some() {
            route_service.get_route()
        } else {
            route_service.set_route("/login", ());
            AppRoute::Login.into()
        };

        let router = RouteAgent::bridge(link.callback(Msg::OnRouteChanged));
        let menu_agent = MenuAgent::bridge(link.callback(Msg::OnMenuAgentResponse));
        let translator = Translator::new();

        JinyaDesignerApp {
            link,
            route_service,
            route,
            router,
            menu_agent,
            menu_title: translator.translate("app.title.home_page"),
            translator,
            hide_search: false,
            change_password_open: false,
            check_api_key_task: None,
            update_service: UpdateService::new(),
            update_task: None,
        }
    }

    #[allow(unused_must_use)]
    fn update(&mut self, msg: Self::Message) -> bool {
        match msg {
            Msg::OnRouteChanged(route) => {
                self.route = route;
                self.hide_search = false;
            }
            Msg::OnAuthenticationChecked(valid) => if !valid {
                self.router.send(RouteRequest::ChangeRoute(Route::from(AppRoute::Login)));
            },
            Msg::OnMenuAgentResponse(response) => {
                match response {
                    MenuAgentResponse::OnChangeTitle(title) => self.menu_title = title,
                    MenuAgentResponse::OnHideSearch => {
                        self.hide_search = true;
                    }
                    _ => {}
                }
            }
            Msg::OnMenuKeyword(value) => self.menu_agent.send(MenuAgentRequest::Keyword(value)),
            Msg::OnMenuSearch(value) => self.menu_agent.send(MenuAgentRequest::Search(value)),
            Msg::OnChangePassword(event) => {
                event.prevent_default();
                self.change_password_open = true;
            }
            Msg::OnLogout(event) => {
                event.prevent_default();
                AuthenticationStorage::clear_api_key();
                AuthenticationStorage::clear_roles();
                AuthenticationStorage::clear_device_code();
                self.router.send(RouteRequest::ChangeRoute(Route::from(AppRoute::Login)));
            }
            Msg::OnChangePasswordSave => {
                self.change_password_open = false;
                self.router.send(RouteRequest::ChangeRoute(Route::from(AppRoute::Login)));
            }
            Msg::OnChangePasswordDiscard => self.change_password_open = false,
            Msg::OnStartUpdate(event) => {
                event.prevent_default();
                self.update_task = Some(self.update_service.start_update(self.link.callback(Msg::OnUpdateStarted)));
            }
            Msg::OnUpdateStarted(result) => if result.is_ok() {
                window().location().set_href(format!("{}/update", get_host()).as_str());
            }
        }

        true
    }

    fn change(&mut self, _props: Self::Properties) -> bool {
        false
    }

    fn view(&self) -> Html {
        if self.route_service.get_route().route.eq("/login") {
            html! {
                <LoginPage />
            }
        } else if self.route_service.get_route().route.eq("/two-factor") {
            html! {
                <TwoFactorPage />
            }
        } else {
            html! {
                <main>
                    {self.get_menu()}
                    <Router<AppRoute, ()>
                        render=self.router_render()
                        redirect=Router::redirect(|route: Route| {
                            AppRoute::Homepage
                        })
                    />
                    <ChangePasswordDialog is_open=self.change_password_open on_save_changes=self.link.callback(|_| Msg::OnChangePasswordSave) on_discard_changes=self.link.callback(|_| Msg::OnChangePasswordDiscard) />
                </main>
            }
        }
    }

    fn rendered(&mut self, first_render: bool) {
        if first_render {
            self.check_api_key_task = Some(AuthenticationService::new().check_api_key(self.link.callback(Msg::OnAuthenticationChecked)));
        }
    }
}

impl JinyaDesignerApp {
    fn get_menu(&self) -> Html {
        let roles = AuthenticationStorage::get_roles().unwrap();
        let is_writer = roles.contains(&"ROLE_WRITER".to_string());
        let is_admin = roles.contains(&"ROLE_ADMIN".to_string());
        let content_group = if is_writer {
            vec![
                SubItemGroup {
                    title: self.translator.translate("app.menu.content.media"),
                    items: vec![
                        SubItem {
                            label: self.translator.translate("app.menu.content.media.files"),
                            route: Some(&AppRoute::Files),
                            on_click: None,
                        },
                        SubItem {
                            label: self.translator.translate("app.menu.content.media.galleries"),
                            route: Some(&AppRoute::Galleries),
                            on_click: None,
                        },
                    ],
                },
                SubItemGroup {
                    title: self.translator.translate("app.menu.content.pages"),
                    items: vec![
                        SubItem {
                            label: self.translator.translate("app.menu.content.pages.simple_pages"),
                            route: Some(&AppRoute::SimplePages),
                            on_click: None,
                        },
                        SubItem {
                            label: self.translator.translate("app.menu.content.pages.segment_pages"),
                            route: Some(&AppRoute::SegmentPages),
                            on_click: None,
                        },
                    ],
                },
            ]
        } else {
            vec![]
        };
        let mut configuration_group = if is_admin {
            vec![
                SubItemGroup {
                    title: self.translator.translate("app.menu.configuration.generic"),
                    items: vec![
                        SubItem {
                            label: self.translator.translate("app.menu.configuration.generic.artists"),
                            route: Some(&AppRoute::Artists),
                            on_click: None,
                        },
                    ],
                },
            ]
        } else {
            vec![]
        };
        if is_writer {
            configuration_group.push(SubItemGroup {
                title: self.translator.translate("app.menu.configuration.frontend"),
                items: vec![
                    SubItem {
                        label: self.translator.translate("app.menu.configuration.frontend.menus"),
                        route: Some(&AppRoute::Menus),
                        on_click: None,
                    },
                    SubItem {
                        label: self.translator.translate("app.menu.configuration.frontend.themes"),
                        route: Some(&AppRoute::Themes),
                        on_click: None,
                    },
                ],
            });
        }
        let my_jinya_group = vec![
            SubItemGroup {
                title: self.translator.translate("app.menu.my_jinya.my_account"),
                items: vec![
                    SubItem {
                        label: self.translator.translate("app.menu.my_jinya.my_account.my_profile"),
                        route: Some(&AppRoute::MyProfile),
                        on_click: None,
                    },
                    SubItem {
                        label: self.translator.translate("app.menu.my_jinya.my_account.change_password"),
                        route: None,
                        on_click: Some(self.link.callback(Msg::OnChangePassword)),
                    },
                    SubItem {
                        label: self.translator.translate("app.menu.my_jinya.my_account.logout"),
                        route: None,
                        on_click: Some(self.link.callback(Msg::OnLogout)),
                    },
                ],
            },
        ];
        let maintenance_group = vec![
            SubItemGroup {
                title: self.translator.translate("app.menu.maintenance.system"),
                items: vec![
                    SubItem {
                        label: self.translator.translate("app.menu.maintenance.system.update"),
                        route: None,
                        on_click: Some(self.link.callback(Msg::OnStartUpdate)),
                    },
                ],
            },
        ];

        let placeholder = if self.hide_search {
            None
        } else {
            Some(self.translator.translate("app.menu.search"))
        };

        html! {
            <div style="position: sticky; top: 0; z-index: 1;">
                <MenuBar search_placeholder=placeholder title=&self.menu_title on_search=self.link.callback(|value| Msg::OnMenuSearch(value)) on_keyword=self.link.callback(|value| Msg::OnMenuKeyword(value))>
                    <MenuItem<AppRoute> groups=content_group label=self.translator.translate("app.menu.content") />
                    <MenuItem<AppRoute> groups=configuration_group label=self.translator.translate("app.menu.configuration") />
                    <MenuItem<AppRoute> groups=maintenance_group label=self.translator.translate("app.menu.maintenance") />
                    <MenuItem<AppRoute> groups=my_jinya_group label=self.translator.translate("app.menu.my_jinya") />
                </MenuBar>
            </div>
        }
    }

    fn router_render(&self) -> Render<AppRoute, ()> {
        let roles = AuthenticationStorage::get_roles().unwrap();
        let is_writer = roles.contains(&"ROLE_WRITER".to_string());
        let is_admin = roles.contains(&"ROLE_ADMIN".to_string());
        Router::render(move |switch: AppRoute| {
            match switch {
                AppRoute::Login => html! {<LoginPage />},
                AppRoute::TwoFactor => html! {<TwoFactorPage />},
                AppRoute::Homepage => html! {<HomePage />},
                AppRoute::Files => if is_writer { html! {<FilesPage />} } else { html! {} },
                AppRoute::Galleries => if is_writer { html! {<GalleriesPage />} } else { html! {} },
                AppRoute::GalleryDesigner(id) => if is_writer { html! {<GalleryDesignerPage id=id />} } else { html! {} },
                AppRoute::SimplePages => if is_writer { html! {<SimplePagesPage />} } else { html! {} },
                AppRoute::EditSimplePage(id) => if is_writer { html! {<EditSimplePagePage id=id />} } else { html! {} },
                AppRoute::AddSimplePage => if is_writer { html! {<AddSimplePagePage />} } else { html! {} },
                AppRoute::SegmentPages => if is_writer { html! {<SegmentPagesPage />} } else { html! {} },
                AppRoute::SegmentPageDesigner(id) => if is_writer { html! {<SegmentPageDesignerPage id=id />} } else { html! {} },
                AppRoute::MyProfile => if is_admin { html! {<MyProfilePage />} } else { html! {} },
                AppRoute::Artists => if is_admin { html! {<ArtistsPage />} } else { html! {} },
                AppRoute::ArtistProfile(id) => if is_admin { html! {<ProfilePage id=id />} } else { html! {} },
                AppRoute::Menus => if is_writer { html! {<MenusPage /> } } else { html! {} },
                AppRoute::MenuDesigner(id) => if is_writer { html! {<MenuDesignerPage id=id /> } } else { html! {} },
                AppRoute::Themes => if is_writer { html! {<ThemesPage /> } } else { html! {} },
                AppRoute::ThemeScssPage(id) => if is_writer { html! {<ScssPage id=id /> } } else { html! {} },
                AppRoute::ThemeLinksPage(id) => if is_writer { html! {<LinksPage id=id /> } } else { html! {} },
                AppRoute::ThemeConfigurationPage(id) => if is_writer { html! {<ConfigurationPage id=id /> } } else { html! {} },
            }
        })
    }
}