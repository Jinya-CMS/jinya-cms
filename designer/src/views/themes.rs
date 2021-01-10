use jinya_ui::layout::page::Page;
use jinya_ui::listing::card::{Card, CardButton, CardContainer};
use jinya_ui::widgets::button::ButtonType;
use jinya_ui::widgets::toast::Toast;
use yew::{Bridge, Bridged, Component, ComponentLink, Html};
use yew::agent::Dispatcher;
use yew::prelude::*;
use yew::services::fetch::FetchTask;
use yew_router::agent::{RouteAgent, RouteRequest};
use yew_router::route::Route;

use crate::agents::menu_agent::{MenuAgent, MenuAgentRequest, MenuAgentResponse};
use crate::ajax::{AjaxError, get_host};
use crate::ajax::theme_service::ThemeService;
use crate::app::AppRoute;
use crate::i18n::*;
use crate::models::list_model::ListModel;
use crate::models::theme::Theme;

pub mod scss;
pub mod links;
pub mod configuration;

pub struct ThemesPage {
    link: ComponentLink<Self>,
    menu_agent: Box<dyn Bridge<MenuAgent>>,
    themes: Vec<Theme>,
    theme_service: ThemeService,
    load_themes_task: Option<FetchTask>,
    activate_theme_task: Option<FetchTask>,
    build_theme_task: Option<FetchTask>,
    translator: Translator,
    keyword: String,
    selected_theme: Option<Theme>,
    route_dispatcher: Dispatcher<RouteAgent>,
}

pub enum Msg {
    OnThemesLoaded(Result<ListModel<Theme>, AjaxError>),
    OnMenuAgentResponse(MenuAgentResponse),
    OnActivateClick(usize),
    OnBuildClick(usize),
    OnThemeActivated(Result<bool, AjaxError>),
    OnThemeBuilt(Result<bool, AjaxError>),
    OnScssClick(usize),
    OnLinksClick(usize),
    OnConfigurationClick(usize),
}

impl Component for ThemesPage {
    type Message = Msg;
    type Properties = ();

    fn create(_props: Self::Properties, link: ComponentLink<Self>) -> Self {
        let menu_agent = MenuAgent::bridge(link.callback(Msg::OnMenuAgentResponse));
        let route_dispatcher = RouteAgent::dispatcher();

        ThemesPage {
            link,
            menu_agent,
            themes: vec![],
            theme_service: ThemeService::new(),
            load_themes_task: None,
            activate_theme_task: None,
            build_theme_task: None,
            translator: Translator::new(),
            keyword: "".to_string(),
            selected_theme: None,
            route_dispatcher,
        }
    }

    fn update(&mut self, msg: Self::Message) -> bool {
        match msg {
            Msg::OnThemesLoaded(result) => {
                if let Ok(list) = result {
                    self.themes = list.items
                } else {
                    log::error!("{}", result.err().unwrap().error.to_string().as_str());
                }
            }
            Msg::OnMenuAgentResponse(response) => if let MenuAgentResponse::OnSearch(value) = response {
                self.keyword = value;
                self.load_themes_task = Some(self.theme_service.get_list(self.keyword.to_string(), self.link.callback(Msg::OnThemesLoaded)));
            },
            Msg::OnActivateClick(idx) => {
                let theme = self.themes[idx].clone();
                let display_name = theme.clone().display_name;
                self.selected_theme = Some(theme.clone());
                Toast::information_toast(self.translator.translate_with_args("themes.overview.notification_activate", map! { "name" => display_name.as_str() }));
                self.activate_theme_task = Some(self.theme_service.activate_theme(theme.id, self.link.callback(Msg::OnThemeActivated)));
            }
            Msg::OnBuildClick(idx) => {
                let theme = self.themes[idx].clone();
                let display_name = theme.clone().display_name;
                self.selected_theme = Some(theme.clone());
                Toast::information_toast(self.translator.translate_with_args("themes.overview.notification_build", map! { "name" => display_name.as_str() }));
                self.build_theme_task = Some(self.theme_service.build_theme(theme.id, self.link.callback(Msg::OnThemeBuilt)));
            }
            Msg::OnThemeActivated(result) => {
                let display_name = self.selected_theme.as_ref().unwrap().display_name.clone();
                if result.is_ok() {
                    self.load_themes_task = Some(self.theme_service.get_list(self.keyword.to_string(), self.link.callback(Msg::OnThemesLoaded)));
                    Toast::positive_toast(self.translator.translate_with_args("themes.overview.notification_activated", map! { "name" => display_name.as_str() }));
                } else {
                    Toast::negative_toast(self.translator.translate_with_args("themes.overview.error_notification_activate_error", map! { "name" => display_name.as_str() }));
                }
                self.selected_theme = None;
            }
            Msg::OnThemeBuilt(result) => {
                let display_name = self.selected_theme.as_ref().unwrap().display_name.clone();
                if result.is_ok() {
                    self.load_themes_task = Some(self.theme_service.get_list(self.keyword.to_string(), self.link.callback(Msg::OnThemesLoaded)));
                    Toast::positive_toast(self.translator.translate_with_args("themes.overview.notification_built", map! { "name" => display_name.as_str() }));
                } else {
                    Toast::negative_toast(self.translator.translate_with_args("themes.overview.error_notification_build_error", map! { "name" => display_name.as_str() }));
                }
                self.selected_theme = None;
            }
            Msg::OnScssClick(idx) => {
                let theme = self.themes[idx].clone();
                self.route_dispatcher.send(RouteRequest::ChangeRoute(Route::from(AppRoute::ThemeScssPage(theme.id))))
            }
            Msg::OnLinksClick(idx) => {
                let theme = self.themes[idx].clone();
                self.route_dispatcher.send(RouteRequest::ChangeRoute(Route::from(AppRoute::ThemeLinksPage(theme.id))))
            }
            Msg::OnConfigurationClick(idx) => {
                let theme = self.themes[idx].clone();
                self.route_dispatcher.send(RouteRequest::ChangeRoute(Route::from(AppRoute::ThemeConfigurationPage(theme.id))))
            }
        }

        true
    }

    fn change(&mut self, _props: Self::Properties) -> bool {
        false
    }

    fn view(&self) -> Html {
        html! {
            <Page>
                <CardContainer>
                    {for self.themes.iter().enumerate().map(move |(idx, item)| {
                        html! {
                            <Card title=&item.display_name src=format!("{}/api/theme/{}/preview", get_host(), &item.id)>
                                <CardButton button_type=ButtonType::Primary icon="cog" tooltip=self.translator.translate("themes.overview.action_settings") on_click=self.link.callback(move |_| Msg::OnConfigurationClick(idx)) />
                                <CardButton button_type=ButtonType::Primary icon="link" tooltip=self.translator.translate("themes.overview.action_links") on_click=self.link.callback(move |_| Msg::OnLinksClick(idx)) />
                                <CardButton button_type=ButtonType::Primary icon="check" tooltip=self.translator.translate("themes.overview.action_activate") on_click=self.link.callback(move |_| Msg::OnActivateClick(idx)) />
                                <CardButton button_type=ButtonType::Primary icon="shape" tooltip=self.translator.translate("themes.overview.action_build") on_click=self.link.callback(move |_| Msg::OnBuildClick(idx)) />
                                <CardButton button_type=ButtonType::Primary icon="sass" tooltip=self.translator.translate("themes.overview.action_scss") on_click=self.link.callback(move |_| Msg::OnScssClick(idx)) />
                            </Card>
                        }
                    })}
                </CardContainer>
            </Page>
        }
    }

    fn rendered(&mut self, first_render: bool) {
        if first_render {
            self.menu_agent.send(MenuAgentRequest::ChangeTitle(self.translator.translate("app.menu.configuration.frontend.themes")));
            self.load_themes_task = Some(self.theme_service.get_list("".to_string(), self.link.callback(Msg::OnThemesLoaded)));
        }
    }
}