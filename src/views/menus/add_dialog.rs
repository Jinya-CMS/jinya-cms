use http::StatusCode;
use jinya_ui::layout::row::{Row, RowAlignment};
use jinya_ui::widgets::alert::{Alert, AlertType};
use jinya_ui::widgets::dialog::content::ContentDialog;
use jinya_ui::widgets::form::dropdown::{Dropdown, DropdownItem};
use jinya_ui::widgets::form::input::{Input, InputState};
use jinya_ui::widgets::toast::Toast;
use yew::{Component, ComponentLink, Html};
use yew::prelude::*;
use yew::services::fetch::FetchTask;

use crate::ajax::AjaxError;
use crate::ajax::file_service::FileService;
use crate::ajax::menu_service::MenuService;
use crate::i18n::*;
use crate::models::file::File;
use crate::models::list_model::ListModel;

pub struct AddDialog {
    link: ComponentLink<Self>,
    translator: Translator,
    on_save_changes: Callback<()>,
    on_discard_changes: Callback<()>,
    menu_service: MenuService,
    alert_message: String,
    alert_type: AlertType,
    alert_visible: bool,
    saving: bool,
    name_input_state: InputState,
    name_validation_message: String,
    name: String,
    logo: String,
    is_open: bool,
    create_menu_task: Option<FetchTask>,
    files: Vec<File>,
    dropdown_files: Vec<DropdownItem>,
    file_service: FileService,
    load_files_task: Option<FetchTask>,
}

#[derive(Clone, PartialEq, Properties)]
pub struct AddDialogProps {
    pub on_save_changes: Callback<()>,
    pub on_discard_changes: Callback<()>,
    pub is_open: bool,
}

pub enum AddDialogMsg {
    OnNameInput(String),
    OnAddPrimary,
    OnAddSecondary,
    OnAdded(Result<bool, AjaxError>),
    OnLogoSelected(String),
    OnFilesLoaded(Result<ListModel<File>, AjaxError>),
}

impl Component for AddDialog {
    type Message = AddDialogMsg;
    type Properties = AddDialogProps;

    fn create(props: Self::Properties, link: ComponentLink<Self>) -> Self {
        let translator = Translator::new();

        AddDialog {
            link,
            translator,
            on_save_changes: props.on_save_changes,
            on_discard_changes: props.on_discard_changes,
            menu_service: MenuService::new(),
            alert_message: "".to_string(),
            alert_type: AlertType::Information,
            alert_visible: false,
            saving: false,
            name_input_state: InputState::Default,
            name_validation_message: "".to_string(),
            name: "".to_string(),
            logo: "".to_string(),
            is_open: props.is_open,
            create_menu_task: None,
            files: vec![],
            dropdown_files: vec![],
            file_service: FileService::new(),
            load_files_task: None,
        }
    }

    fn update(&mut self, msg: Self::Message) -> bool {
        match msg {
            AddDialogMsg::OnNameInput(value) => {
                self.name = value;
                self.name_input_state = if self.name.is_empty() {
                    self.name_validation_message = self.translator.translate("menus.add.error_name_empty");
                    InputState::Negative
                } else {
                    self.name_validation_message = "".to_string();
                    InputState::Default
                };
            }
            AddDialogMsg::OnAddPrimary => {
                if !self.name.is_empty() {
                    self.saving = true;
                    let logo_opt = self.files.iter().find(|item| item.name.eq(&self.logo.to_string()));
                    let logo = if let Some(res) = logo_opt {
                        Some(res.id)
                    } else {
                        None
                    };
                    self.create_menu_task = Some(self.menu_service.add_menu(self.name.to_string(), logo, self.link.callback(AddDialogMsg::OnAdded)));
                }
            }
            AddDialogMsg::OnAddSecondary => self.on_discard_changes.emit(()),
            AddDialogMsg::OnAdded(result) => {
                if result.is_err() {
                    self.alert_visible = true;
                    self.alert_message = match result.err().unwrap().status_code {
                        StatusCode::CONFLICT => self.translator.translate("menus.add.error_conflict"),
                        _ => self.translator.translate("menus.add.error_generic")
                    };
                    self.alert_type = AlertType::Negative;
                    self.saving = false;
                } else {
                    self.on_save_changes.emit(());
                }
            }
            AddDialogMsg::OnLogoSelected(value) => {
                self.logo = value;
            }
            AddDialogMsg::OnFilesLoaded(result) => {
                if let Ok(files) = result {
                    self.files = files.items;
                    self.dropdown_files = self.files.iter().map(|item| DropdownItem { value: item.name.clone(), text: item.name.clone() }).collect();
                    self.logo = self.files.first().unwrap().name.to_string();
                } else {
                    Toast::negative_toast(self.translator.translate("menus.add.error_load_files"));
                    self.logo = "".to_string();
                }
            }
        }

        true
    }

    fn change(&mut self, props: Self::Properties) -> bool {
        self.on_save_changes = props.on_save_changes;
        self.on_discard_changes = props.on_discard_changes;
        self.is_open = props.is_open;

        true
    }

    fn view(&self) -> Html {
        html! {
            <ContentDialog
                is_open=self.is_open
                on_primary=self.link.callback(|_| AddDialogMsg::OnAddPrimary)
                on_secondary=self.link.callback(|_| AddDialogMsg::OnAddSecondary)
                primary_label=self.translator.translate("menus.add.action_save")
                secondary_label=self.translator.translate("menus.add.action_discard")
                title=self.translator.translate("menus.add.title")
            >
                {if self.alert_visible {
                    html! {
                        <Row alignment=RowAlignment::Stretch>
                            <Alert message=&self.alert_message alert_type=&self.alert_type />
                        </Row>
                    }
                } else {
                    html! {}
                }}
                <Row alignment=RowAlignment::Stretch>
                    <Input validation_message=&self.name_validation_message state=&self.name_input_state disabled=self.saving label=self.translator.translate("menus.add.name") on_input=self.link.callback(|value| AddDialogMsg::OnNameInput(value)) value=&self.name />
                </Row>
                <Row alignment=RowAlignment::Stretch>
                    <Dropdown items=&self.dropdown_files disabled=self.saving label=self.translator.translate("menus.add.logo") on_select=self.link.callback(AddDialogMsg::OnLogoSelected) value=&self.logo />
                </Row>
            </ContentDialog>
        }
    }

    fn rendered(&mut self, first_render: bool) {
        if first_render {
            self.load_files_task = Some(self.file_service.get_list("".to_string(), self.link.callback(AddDialogMsg::OnFilesLoaded)));
        }
    }
}