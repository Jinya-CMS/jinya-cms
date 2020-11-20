use std::collections::BTreeMap;

use jinya_ui::layout::button_row::ButtonRow;
use jinya_ui::layout::page::Page;
use jinya_ui::widgets::button::Button;
use jinya_ui::widgets::form::input::Input;
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

pub struct ScssPage {
    link: ComponentLink<Self>,
    variables: BTreeMap<String, String>,
    changed_variables: BTreeMap<String, String>,
    id: usize,
    theme_service: ThemeService,
    load_variables_task: Option<FetchTask>,
    menu_dispatcher: Dispatcher<MenuAgent>,
    translator: Translator,
    load_theme_task: Option<FetchTask>,
    save_variables_task: Option<FetchTask>,
    route_dispatcher: Dispatcher<RouteAgent>,
}

pub enum Msg {
    OnVariablesFetched(Result<SerdeMap<String, Value>, AjaxError>),
    OnThemeLoaded(Result<Theme, AjaxError>),
    OnVariableChange(String, String),
    OnVariablesSaved(Result<bool, AjaxError>),
    OnSaveClick,
    OnBackClick,
}

#[derive(Properties, Clone)]
pub struct ScssPageProperties {
    pub id: usize,
}

impl Component for ScssPage {
    type Message = Msg;
    type Properties = ScssPageProperties;

    fn create(props: Self::Properties, link: ComponentLink<Self>) -> Self {
        let menu_dispatcher = MenuAgent::dispatcher();
        let route_dispatcher = RouteAgent::dispatcher();

        ScssPage {
            link,
            variables: BTreeMap::new(),
            changed_variables: BTreeMap::new(),
            id: props.id,
            theme_service: ThemeService::new(),
            load_variables_task: None,
            menu_dispatcher,
            translator: Translator::new(),
            load_theme_task: None,
            save_variables_task: None,
            route_dispatcher,
        }
    }

    fn update(&mut self, msg: Self::Message) -> bool {
        match msg {
            Msg::OnVariablesFetched(result) => {
                if let Ok(vars) = result {
                    let mut data = BTreeMap::new();
                    vars.iter().for_each(|(key, value)| {
                        data.insert(key.to_string(), value.as_str().unwrap().to_string());
                    });
                    self.variables = data;
                } else {}
            }
            Msg::OnThemeLoaded(result) => {
                if let Ok(theme) = result {
                    self.changed_variables = theme.scss_variables;
                    self.menu_dispatcher.send(MenuAgentRequest::ChangeTitle(self.translator.translate_with_args("app.menu.configuration.frontend.themes.variables", map! { "name" => theme.display_name.as_str() })))
                }
            }
            Msg::OnVariableChange(key, value) => {
                if value.is_empty() {
                    self.changed_variables.remove(key.as_str());
                } else if !self.changed_variables.contains_key(key.as_str()) {
                    self.changed_variables.insert(key, value);
                } else {
                    *self.changed_variables.get_mut(key.as_str()).unwrap() = value;
                }
            }
            Msg::OnSaveClick => {
                let mut map = SerdeMap::new();
                self.changed_variables.iter().for_each(|(key, value)| {
                    map.insert(key.to_string(), Value::String(value.to_string()));
                });
                self.save_variables_task = Some(self.theme_service.save_variables(self.id, map, self.link.callback(Msg::OnVariablesSaved)));
            }
            Msg::OnVariablesSaved(result) => {
                if result.is_ok() {
                    self.load_variables_task = Some(self.theme_service.get_variables(self.id, self.link.callback(Msg::OnVariablesFetched)));
                    Toast::positive_toast(self.translator.translate("themes.variables.notification_saved_successfully"));
                } else {
                    Toast::negative_toast(self.translator.translate("themes.variables.notification_save_failed"));
                }
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
        let vars = self.variables.iter();
        html! {
            <Page>
                <div class="jinya-designer-theme-variables">
                    {for vars.enumerate().map(move |(_, (key, variable))| {
                        let val = key.clone();
                        let value = if let Some(val) = self.changed_variables.get(key.as_str()) {
                            val.to_string()
                        } else {
                            "".to_string()
                        };
                        html! {
                            <Input label=key.to_string() on_input=self.link.callback(move |value| Msg::OnVariableChange(val.clone(), value)) value=&value placeholder=variable />
                        }
                    })}
                </div>
                <ButtonRow>
                    <Button label=self.translator.translate("themes.variables.action_back") on_click=self.link.callback(|_| Msg::OnBackClick)/>
                    <Button label=self.translator.translate("themes.variables.action_save") on_click=self.link.callback(|_| Msg::OnSaveClick)/>
                </ButtonRow>
            </Page>
        }
    }

    fn rendered(&mut self, first_render: bool) {
        if first_render {
            self.menu_dispatcher.send(MenuAgentRequest::HideSearch);
            self.load_variables_task = Some(self.theme_service.get_variables(self.id, self.link.callback(Msg::OnVariablesFetched)));
            self.load_theme_task = Some(self.theme_service.get_theme(self.id, self.link.callback(Msg::OnThemeLoaded)));
        }
    }
}