use std::collections::BTreeMap;

use jinya_ui::layout::button_row::ButtonRow;
use jinya_ui::layout::page::Page;
use jinya_ui::widgets::button::Button;
use jinya_ui::widgets::toast::Toast;
use serde_json::map::Map as SerdeMap;
use serde_json::Value;
use yew::{Component, ComponentLink, Html};
use yew::agent::Dispatcher;
use yew::prelude::*;
use yew::services::fetch::FetchTask;
use yew_router::agent::{RouteAgent, RouteRequest};
use yew_router::route::Route;

use crate::agents::menu_agent::{MenuAgent, MenuAgentRequest};
use crate::ajax::AjaxError;
use crate::ajax::theme_service::ThemeService;
use crate::app::AppRoute;
use crate::i18n::Translator;
use crate::models::theme::Theme;

pub struct LinksPage {
    link: ComponentLink<Self>,
    id: usize,
    theme_service: ThemeService,
    menu_dispatcher: Dispatcher<MenuAgent>,
    translator: Translator,
    load_theme_task: Option<FetchTask>,
    route_dispatcher: Dispatcher<RouteAgent>,
}

pub enum Msg {
    OnThemeLoaded(Result<Theme, AjaxError>),
    OnSaveClick,
    OnBackClick,
}

#[derive(Properties, Clone)]
pub struct ScssPageProperties {
    pub id: usize,
}

impl Component for LinksPage {
    type Message = Msg;
    type Properties = ScssPageProperties;

    fn create(props: Self::Properties, link: ComponentLink<Self>) -> Self {
        let menu_dispatcher = MenuAgent::dispatcher();
        let route_dispatcher = RouteAgent::dispatcher();

        LinksPage {
            link,
            id: props.id,
            theme_service: ThemeService::new(),
            menu_dispatcher,
            translator: Translator::new(),
            load_theme_task: None,
            route_dispatcher,
        }
    }

    fn update(&mut self, msg: Self::Message) -> bool {
        match msg {
            Msg::OnThemeLoaded(result) => {
                if let Ok(theme) = result {
                    self.menu_dispatcher.send(MenuAgentRequest::ChangeTitle(self.translator.translate_with_args("app.menu.configuration.frontend.themes.links", map! { "name" => theme.display_name.as_str() })))
                }
            }
            Msg::OnSaveClick => {
            }
            Msg::OnBackClick => self.route_dispatcher.send(RouteRequest::ChangeRoute(Route::from(AppRoute::Themes))),
        }

        true
    }

    fn change(&mut self, props: Self::Properties) -> bool {
        self.id = props.id;

        true
    }

    fn view(&self) -> Html {
        html! {
            <Page>
                <ButtonRow>
                    <Button label=self.translator.translate("themes.links.action_back") on_click=self.link.callback(|_| Msg::OnBackClick)/>
                    <Button label=self.translator.translate("themes.links.action_save") on_click=self.link.callback(|_| Msg::OnSaveClick)/>
                </ButtonRow>
            </Page>
        }
    }

    fn rendered(&mut self, first_render: bool) {
        if first_render {
            self.menu_dispatcher.send(MenuAgentRequest::HideSearch);
            self.load_theme_task = Some(self.theme_service.get_theme(self.id, self.link.callback(Msg::OnThemeLoaded)));
        }
    }
}