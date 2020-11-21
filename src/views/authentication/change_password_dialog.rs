use http::StatusCode;
use jinya_ui::layout::row::{Row, RowAlignment};
use jinya_ui::widgets::alert::{Alert, AlertType};
use jinya_ui::widgets::dialog::content::ContentDialog;
use jinya_ui::widgets::form::input::{Input, InputState};
use yew::{Component, ComponentLink, Html};
use yew::prelude::*;
use yew::services::fetch::FetchTask;

use crate::ajax::AjaxError;
use crate::ajax::authentication_service::AuthenticationService;
use crate::i18n::*;

pub struct ChangePasswordDialog {
    link: ComponentLink<Self>,
    translator: Translator,
    on_save_changes: Callback<()>,
    on_discard_changes: Callback<()>,
    change_password_task: Option<FetchTask>,
    alert_message: String,
    alert_type: AlertType,
    alert_visible: bool,
    saving: bool,
    old_password_input_state: InputState,
    old_password_validation_message: String,
    old_password: String,
    new_password_input_state: InputState,
    new_password_validation_message: String,
    new_password: String,
    is_open: bool,
    authentication_service: AuthenticationService,
}

#[derive(Clone, PartialEq, Properties)]
pub struct ChangePasswordDialogProps {
    pub on_save_changes: Callback<()>,
    pub on_discard_changes: Callback<()>,
    pub is_open: bool,
}

pub enum ChangePasswordDialogMsg {
    OnOldPasswordInput(String),
    OnNewPasswordInput(String),
    OnChangePrimary,
    OnChangeSecondary,
    OnChanged(Result<bool, AjaxError>),
}

impl Component for ChangePasswordDialog {
    type Message = ChangePasswordDialogMsg;
    type Properties = ChangePasswordDialogProps;

    fn create(props: Self::Properties, link: ComponentLink<Self>) -> Self {
        let translator = Translator::new();

        ChangePasswordDialog {
            link,
            translator,
            on_save_changes: props.on_save_changes,
            on_discard_changes: props.on_discard_changes,
            change_password_task: None,
            alert_message: "".to_string(),
            alert_type: AlertType::Information,
            alert_visible: false,
            saving: false,
            old_password_input_state: InputState::Default,
            old_password_validation_message: "".to_string(),
            old_password: "".to_string(),
            new_password_input_state: Default::default(),
            new_password_validation_message: "".to_string(),
            new_password: "".to_string(),
            is_open: props.is_open,
            authentication_service: AuthenticationService::new(),
        }
    }

    fn update(&mut self, msg: Self::Message) -> bool {
        match msg {
            ChangePasswordDialogMsg::OnOldPasswordInput(value) => {
                self.old_password = value;
                self.old_password_input_state = if self.old_password.is_empty() {
                    self.old_password_validation_message = self.translator.translate("my_jinya.my_account.change_password.error_old_password_empty");
                    InputState::Negative
                } else {
                    self.old_password_validation_message = "".to_string();
                    InputState::Default
                };
            }
            ChangePasswordDialogMsg::OnChangePrimary => {
                if !self.old_password.is_empty() && !self.new_password.is_empty() {
                    self.saving = true;
                    self.change_password_task = Some(self.authentication_service.change_password(self.old_password.clone(), self.new_password.clone(), self.link.callback(|result| ChangePasswordDialogMsg::OnChanged(result))));
                }
            }
            ChangePasswordDialogMsg::OnChangeSecondary => self.on_discard_changes.emit(()),
            ChangePasswordDialogMsg::OnChanged(result) => {
                if result.is_ok() {
                    self.alert_visible = true;
                    self.alert_type = AlertType::Information;
                    self.alert_message = self.translator.translate("my_jinya.my_account.change_password.saving");
                    self.on_save_changes.emit(());
                } else {
                    self.alert_visible = true;
                    self.alert_message = match result.err().unwrap().status_code {
                        StatusCode::FORBIDDEN => self.translator.translate("my_jinya.my_account.change_password.error_forbidden"),
                        _ => self.translator.translate("my_jinya.my_account.change_password.error_generic")
                    };
                    self.alert_type = AlertType::Negative;
                    self.saving = false;
                }
            }
            ChangePasswordDialogMsg::OnNewPasswordInput(value) => {
                self.new_password = value;
                self.new_password_input_state = if self.new_password.is_empty() {
                    self.new_password_validation_message = self.translator.translate("my_jinya.my_account.change_password.error_new_password_empty");
                    InputState::Negative
                } else {
                    self.new_password_validation_message = "".to_string();
                    InputState::Default
                };
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
                on_primary=self.link.callback(|_| ChangePasswordDialogMsg::OnChangePrimary)
                on_secondary=self.link.callback(|_| ChangePasswordDialogMsg::OnChangeSecondary)
                primary_label=self.translator.translate("my_jinya.my_account.change_password.action_save")
                secondary_label=self.translator.translate("my_jinya.my_account.change_password.action_discard")
                title=self.translator.translate("my_jinya.my_account.change_password.title")
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
                    <Input input_type="password" validation_message=&self.old_password_validation_message state=&self.old_password_input_state disabled=self.saving label=self.translator.translate("my_jinya.my_account.change_password.old_password") on_input=self.link.callback(|value| ChangePasswordDialogMsg::OnOldPasswordInput(value)) value=&self.old_password />
                </Row>
                <Row alignment=RowAlignment::Stretch>
                    <Input input_type="password" validation_message=&self.new_password_validation_message state=&self.new_password_input_state disabled=self.saving label=self.translator.translate("my_jinya.my_account.change_password.new_password") on_input=self.link.callback(|value| ChangePasswordDialogMsg::OnNewPasswordInput(value)) value=&self.new_password />
                </Row>
            </ContentDialog>
        }
    }
}