use jinya_ui::layout::button_row::ButtonRow;
use jinya_ui::layout::page::Page;
use jinya_ui::widgets::button::Button;
use jinya_ui::widgets::form::checkbox::Checkbox;
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
use crate::models::theme::{InputType, Theme, ThemeConfigurationStructure, ThemeField, ThemeGroup};

pub struct ConfigurationPage {
    link: ComponentLink<Self>,
    id: usize,
    theme_service: ThemeService,
    load_configuration_task: Option<FetchTask>,
    menu_dispatcher: Dispatcher<MenuAgent>,
    translator: Translator,
    save_configuration_task: Option<FetchTask>,
    load_theme_task: Option<FetchTask>,
    load_theme_config_structure_task: Option<FetchTask>,
    route_dispatcher: Dispatcher<RouteAgent>,
    configuration_structure: Option<ThemeConfigurationStructure>,
    changed_config: SerdeMap<String, Value>,
    default_configuration: SerdeMap<String, Value>,
    default_config_loaded: bool,
    structure_loaded: bool,
    theme_loaded: bool,
}

#[allow(clippy::large_enum_variant)]
pub enum Msg {
    OnConfigurationFetched(Result<SerdeMap<String, Value>, AjaxError>),
    OnConfigStructureLoaded(Result<ThemeConfigurationStructure, AjaxError>),
    OnThemeLoaded(Result<Theme, AjaxError>),
    OnSaveClick,
    OnBackClick,
    OnStringInput(String, String, String),
    OnCheckboxChange(String, String),
    OnConfigurationSaved(Result<bool, AjaxError>),
}

#[derive(Properties, Clone)]
pub struct ConfigurationPageProperties {
    pub id: usize,
}

impl Component for ConfigurationPage {
    type Message = Msg;
    type Properties = ConfigurationPageProperties;

    fn create(props: Self::Properties, link: ComponentLink<Self>) -> Self {
        let menu_dispatcher = MenuAgent::dispatcher();
        let route_dispatcher = RouteAgent::dispatcher();

        ConfigurationPage {
            link,
            id: props.id,
            theme_service: ThemeService::new(),
            load_configuration_task: None,
            menu_dispatcher,
            translator: Translator::new(),
            save_configuration_task: None,
            load_theme_task: None,
            load_theme_config_structure_task: None,
            route_dispatcher,
            configuration_structure: None,
            changed_config: SerdeMap::new(),
            default_configuration: SerdeMap::new(),
            default_config_loaded: false,
            structure_loaded: false,
            theme_loaded: false,
        }
    }

    fn update(&mut self, msg: Self::Message) -> bool {
        match msg {
            Msg::OnConfigurationFetched(result) => if let Ok(config) = result {
                self.default_configuration = config;
                self.default_config_loaded = true;
            },
            Msg::OnSaveClick => self.save_configuration_task = Some(self.theme_service.save_configuration(self.id, self.changed_config.clone(), self.link.callback(Msg::OnConfigurationSaved))),
            Msg::OnBackClick => self.route_dispatcher.send(RouteRequest::ChangeRoute(Route::from(AppRoute::Themes))),
            Msg::OnConfigStructureLoaded(result) => if let Ok(structure) = result {
                self.configuration_structure = Some(structure);
                self.structure_loaded = true;
                self.menu_dispatcher.send(MenuAgentRequest::ChangeTitle(self.configuration_structure.as_ref().unwrap().title.to_string()))
            },
            Msg::OnThemeLoaded(result) => if let Ok(theme) = result {
                self.changed_config = theme.configuration;
                self.theme_loaded = true;
            },
            Msg::OnStringInput(group_name, field_name, value) => {
                let config = self.changed_config.get_mut(group_name.as_str());
                if let Some(group) = config {
                    let val = group.as_object_mut();
                    if let Some(data) = val {
                        let field = data.get_mut(field_name.as_str());
                        if let Some(val) = field {
                            *val = Value::String(value);
                        } else {
                            data.insert(field_name, Value::String(value));
                        }
                    }
                } else {
                    let mut group = SerdeMap::new();
                    group.insert(field_name, Value::String(value));
                    self.changed_config.insert(group_name, Value::Object(group));
                }
            }
            Msg::OnCheckboxChange(group_name, field_name) => {
                let default_config = self.default_configuration.get(group_name.as_str());
                let default_value = ConfigurationPage::get_value_from_bool_group(&field_name, default_config);
                let changed_config = self.changed_config.get(group_name.as_str());
                let changed_value = ConfigurationPage::get_value_from_bool_group(&field_name, changed_config);
                let config = self.changed_config.get_mut(group_name.as_str());
                let new_value = if let Some(val) = changed_value {
                    !val
                } else {
                    !default_value.unwrap()
                };
                if let Some(group) = config {
                    let val = group.as_object_mut();
                    if let Some(data) = val {
                        let field = data.get_mut(field_name.as_str());
                        if let Some(val) = field {
                            *val = Value::Bool(new_value);
                        } else {
                            data.insert(field_name, Value::Bool(new_value));
                        }
                    }
                } else {
                    let mut group = SerdeMap::new();
                    group.insert(field_name, Value::Bool(new_value));
                    self.changed_config.insert(group_name, Value::Object(group));
                }
            }
            Msg::OnConfigurationSaved(result) => if result.is_ok() {
                self.route_dispatcher.send(RouteRequest::ChangeRoute(Route::from(AppRoute::Themes)));
                Toast::positive_toast(self.translator.translate("themes.configuration.notification_saved"))
            } else {
                Toast::negative_toast(self.translator.translate("themes.configuration.notification_save_error"))
            }
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
                <div class="jinya-designer-theme-configuration">
                    {if self.structure_loaded && self.default_config_loaded && self.theme_loaded {
                        self.get_groups(&self.configuration_structure.as_ref().unwrap().groups)
                    } else {
                        html! {}
                    }}
                </div>
                <ButtonRow>
                    <Button label=self.translator.translate("themes.configuration.action_back") on_click=self.link.callback(|_| Msg::OnBackClick)/>
                    <Button label=self.translator.translate("themes.configuration.action_save") on_click=self.link.callback(|_| Msg::OnSaveClick)/>
                </ButtonRow>
            </Page>
        }
    }

    fn rendered(&mut self, first_render: bool) {
        if first_render {
            self.menu_dispatcher.send(MenuAgentRequest::HideSearch);
            self.load_theme_config_structure_task = Some(self.theme_service.get_configuration_structure(self.id, self.link.callback(Msg::OnConfigStructureLoaded)));
            self.load_theme_task = Some(self.theme_service.get_theme(self.id, self.link.callback(Msg::OnThemeLoaded)));
            self.load_configuration_task = Some(self.theme_service.get_configuration(self.id, self.link.callback(Msg::OnConfigurationFetched)));
        }
    }
}

impl ConfigurationPage {
    fn get_value_from_bool_group(field_name: &String, config: Option<&Value>) -> Option<bool> {
        if let Some(group) = config {
            let val = group.as_object().unwrap().get(field_name.as_str());
            if let Some(val) = val {
                val.as_bool()
            } else {
                None
            }
        } else {
            None
        }
    }

    fn get_value_from_string_group(field_name: &String, config: Option<&Value>) -> String {
        if let Some(group) = config {
            let val = group.as_object().unwrap().get(field_name.as_str());
            if let Some(val) = val {
                if val.is_string() {
                    val.as_str().unwrap().to_string()
                } else {
                    "".to_string()
                }
            } else {
                "".to_string()
            }
        } else {
            "".to_string()
        }
    }

    fn get_string_field(&self, field: &ThemeField, group_name: String, field_name: String) -> Html {
        let changed_config = self.changed_config.get(group_name.as_str());
        let default_config = self.default_configuration.get(group_name.as_str());
        let value = ConfigurationPage::get_value_from_string_group(&field_name, changed_config);
        let placeholder = ConfigurationPage::get_value_from_string_group(&field_name, default_config);
        html! {
            <Input placeholder=&placeholder label=&field.label on_input=self.link.callback(move |value: String| Msg::OnStringInput(group_name.to_string(), field_name.to_string(), value.to_string())) value=&value />
        }
    }

    fn get_boolean_field(&self, field: &ThemeField, group_name: String, field_name: String) -> Html {
        let changed_config = self.changed_config.get(group_name.as_str());
        let default_config = self.default_configuration.get(group_name.as_str());
        let changed_value = ConfigurationPage::get_value_from_bool_group(&field_name, changed_config);
        let default_value = ConfigurationPage::get_value_from_bool_group(&field_name, default_config);
        let value = if let Some(val) = changed_value {
            val
        } else {
            default_value.unwrap()
        };

        html! {
            <Checkbox label=&field.label on_change=self.link.callback(move |_| Msg::OnCheckboxChange(group_name.to_string(), field_name.to_string())) checked=&value />
        }
    }

    fn get_field(&self, field: &ThemeField, group_name: String) -> Html {
        match field.input_type {
            InputType::String => self.get_string_field(field, group_name, field.name.clone()),
            InputType::Boolean => self.get_boolean_field(field, group_name, field.name.clone()),
        }
    }

    fn get_fields(&self, fields: &[ThemeField], group_name: String) -> Html {
        html! {
            {for fields.iter().enumerate().map(|(_, field)| {
                self.get_field(field, group_name.clone())
            })}
        }
    }

    fn get_groups(&self, groups: &[ThemeGroup]) -> Html {
        html! {
            {for groups.iter().enumerate().map(|(_, group)| {
                html! {
                    <fieldset class="jinya-designer-themes-configuration__fieldset">
                        <legend class="jinya-designer-themes-configuration__legend">{&group.title}</legend>
                        {if group.fields.is_some() {
                            self.get_fields(group.fields.as_ref().unwrap(), group.name.clone())
                        } else {
                            html! {}
                        }}
                    </fieldset>
                }
            })}
        }
    }
}
