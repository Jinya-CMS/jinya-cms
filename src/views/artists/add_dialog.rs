use http::StatusCode;
use jinya_ui::layout::row::{Row, RowAlignment};
use jinya_ui::widgets::alert::{Alert, AlertType};
use jinya_ui::widgets::dialog::content::ContentDialog;
use jinya_ui::widgets::form::file_upload::FileUpload;
use jinya_ui::widgets::form::input::{Input, InputState};
use jinya_ui::widgets::form::multi_select::*;
use jinya_ui::widgets::form::multi_select::MultiSelectItem;
use yew::{Component, ComponentLink, Html};
use yew::prelude::*;
use yew::services::fetch::FetchTask;
use yew::services::reader::{File, FileData, ReaderService, ReaderTask};

use crate::ajax::AjaxError;
use crate::ajax::artist_service::ArtistService;
use crate::i18n::*;
use crate::models::artist::Artist;

pub struct AddDialog {
    link: ComponentLink<Self>,
    translator: Translator,
    selected_file: Option<File>,
    on_save_changes: Callback<()>,
    on_discard_changes: Callback<()>,
    upload_profile_picture_task: Option<FetchTask>,
    add_artist_task: Option<FetchTask>,
    artist_service: ArtistService,
    alert_message: String,
    alert_type: AlertType,
    alert_visible: bool,
    reader_task: Option<ReaderTask>,
    saving: bool,
    artist_name_input_state: InputState,
    artist_name_validation_message: String,
    artist_name: String,
    password_input_state: InputState,
    password_validation_message: String,
    password: String,
    email_input_state: InputState,
    email_validation_message: String,
    email: String,
    roles: Vec<MultiSelectItem>,
    is_open: bool,
    available_roles_options: Vec<MultiSelectItem>,
    available_roles_original_options: Vec<MultiSelectItem>,
    id: Option<usize>,
}

#[derive(Clone, PartialEq, Properties)]
pub struct AddDialogProps {
    pub on_save_changes: Callback<()>,
    pub on_discard_changes: Callback<()>,
    pub is_open: bool,
}

pub enum AddDialogMsg {
    OnArtistNameInput(String),
    OnEmailInput(String),
    OnPasswordInput(String),
    OnRoleSelected(MultiSelectItem),
    OnRoleDeselected(MultiSelectItem),
    OnRolesFilter(String),
    OnFileSelect(Vec<File>),
    OnAddPrimary,
    OnAddSecondary,
    OnArtistCreated(Result<Artist, AjaxError>),
    OnProfilePictureUploaded(Result<bool, AjaxError>),
    OnReadFile(FileData),
}

impl Component for AddDialog {
    type Message = AddDialogMsg;
    type Properties = AddDialogProps;

    fn create(props: Self::Properties, link: ComponentLink<Self>) -> Self {
        let translator = Translator::new();
        let available_roles = vec![
            MultiSelectItem {
                value: "ROLE_WRITER".to_string(),
                text: "Writer".to_string(),
            },
            MultiSelectItem {
                value: "ROLE_ADMIN".to_string(),
                text: "Admin".to_string(),
            },
            MultiSelectItem {
                value: "ROLE_SUPER_ADMIN".to_string(),
                text: "Super Admin".to_string(),
            },
        ];

        AddDialog {
            link,
            translator,
            selected_file: None,
            on_save_changes: props.on_save_changes,
            on_discard_changes: props.on_discard_changes,
            upload_profile_picture_task: None,
            add_artist_task: None,
            artist_service: ArtistService::new(),
            alert_message: "".to_string(),
            alert_type: AlertType::Information,
            alert_visible: false,
            reader_task: None,
            saving: false,
            artist_name_input_state: InputState::Default,
            artist_name_validation_message: "".to_string(),
            artist_name: "".to_string(),
            password_input_state: InputState::Default,
            password_validation_message: "".to_string(),
            password: "".to_string(),
            email_input_state: InputState::Default,
            email_validation_message: "".to_string(),
            email: "".to_string(),
            roles: vec![],
            is_open: props.is_open,
            available_roles_options: available_roles.clone(),
            available_roles_original_options: available_roles,
            id: None,
        }
    }

    fn update(&mut self, msg: Self::Message) -> bool {
        match msg {
            AddDialogMsg::OnAddPrimary => {
                if !self.artist_name.is_empty() && !self.password.is_empty() && !self.email.is_empty() {
                    self.saving = true;
                    let roles = self.roles.iter().map(|item| item.value.clone()).collect();
                    self.add_artist_task = Some(self.artist_service.add_artist(self.artist_name.clone(), self.email.clone(), self.password.clone(), roles, self.link.callback(AddDialogMsg::OnArtistCreated)));
                }
            }
            AddDialogMsg::OnAddSecondary => self.on_discard_changes.emit(()),
            AddDialogMsg::OnFileSelect(files) => {
                self.selected_file = Some(files[0].clone());
            }
            AddDialogMsg::OnArtistCreated(result) => {
                if let Ok(artist) = result {
                    if self.selected_file.is_some() {
                        let file = self.selected_file.as_ref().unwrap().clone();
                        self.id = Some(artist.id);
                        self.reader_task = Some(ReaderService::new().read_file(file, self.link.callback(AddDialogMsg::OnReadFile)).unwrap());
                        self.saving = false;
                    } else {
                        self.on_save_changes.emit(());
                    }
                } else {
                    let error = result.err().unwrap();
                    self.alert_visible = true;
                    self.alert_type = AlertType::Negative;
                    match error.status_code {
                        StatusCode::CONFLICT => self.alert_message = self.translator.translate("artists.add.error_exists"),
                        _ => self.alert_message = self.translator.translate("artists.add.error_generic"),
                    }
                    self.saving = false;
                }
            }
            AddDialogMsg::OnReadFile(data) => {
                self.upload_profile_picture_task = Some(self.artist_service.upload_profile_picture(self.id.unwrap(), data.content, self.link.callback(AddDialogMsg::OnProfilePictureUploaded)))
            }
            AddDialogMsg::OnArtistNameInput(value) => {
                self.artist_name = value;
                self.artist_name_input_state = if self.artist_name.is_empty() {
                    self.artist_name_validation_message = self.translator.translate("artists.add.error_artist_name_empty");
                    InputState::Negative
                } else {
                    self.artist_name_validation_message = "".to_string();
                    InputState::Default
                };
            }
            AddDialogMsg::OnEmailInput(value) => {
                self.email = value;
                self.email_input_state = if self.email.is_empty() {
                    self.email_validation_message = self.translator.translate("artists.add.error_email_empty");
                    InputState::Negative
                } else {
                    self.email_validation_message = "".to_string();
                    InputState::Default
                };
            }
            AddDialogMsg::OnPasswordInput(value) => {
                self.password = value;
                self.password_input_state = if self.password.is_empty() {
                    self.password_validation_message = self.translator.translate("artists.add.error_password_empty");
                    InputState::Negative
                } else {
                    self.password_validation_message = "".to_string();
                    InputState::Default
                };
            }
            AddDialogMsg::OnProfilePictureUploaded(result) => {
                self.saving = false;
                if result.is_ok() {
                    self.on_save_changes.emit(());
                } else {
                    self.alert_visible = true;
                    self.alert_type = AlertType::Negative;
                    self.translator.translate("artists.add.profile_picture.error_generic");
                }
            }
            AddDialogMsg::OnRoleSelected(value) => {
                self.roles.push(value.clone());
                let options_idx = self.available_roles_options.iter().position(|item| item.value.eq(&value.value));
                if let Some(idx) =options_idx {
                    self.available_roles_options.remove(idx);
                }
            }
            AddDialogMsg::OnRoleDeselected(value) => {
                let index = self.roles.binary_search_by(|item| item.value.cmp(&value.value));
                if let Ok(idx) = index {
                    self.roles.remove(idx);
                }
            }
            AddDialogMsg::OnRolesFilter(value) => {
                let options = &self.available_roles_original_options;
                self.available_roles_options = options
                    .iter()
                    .filter(move |item| item.value.contains(&value))
                    .cloned()
                    .collect();
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
                primary_label=self.translator.translate("artists.add.action_save")
                secondary_label=self.translator.translate("artists.add.action_discard")
                title=self.translator.translate("artists.add.title")
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
                    <Input validation_message=&self.artist_name_validation_message state=&self.artist_name_input_state disabled=self.saving label=self.translator.translate("artists.add.artist_name") on_input=self.link.callback(|value| AddDialogMsg::OnArtistNameInput(value)) value=&self.artist_name />
                </Row>
                <Row alignment=RowAlignment::Stretch>
                    <Input input_type="email" validation_message=&self.email_validation_message state=&self.email_input_state disabled=self.saving label=self.translator.translate("artists.add.email") on_input=self.link.callback(|value| AddDialogMsg::OnEmailInput(value)) value=&self.email />
                </Row>
                <Row alignment=RowAlignment::Stretch>
                    <Input input_type="password" validation_message=&self.password_validation_message state=&self.password_input_state disabled=self.saving label=self.translator.translate("artists.add.password") on_input=self.link.callback(|value| AddDialogMsg::OnPasswordInput(value)) value=&self.password />
                </Row>
                <Row alignment=RowAlignment::Stretch>
                    <FileUpload disabled=self.saving placeholder=self.translator.translate("artists.add.profile_picture") label=self.translator.translate("artists.add.profile_picture") on_select=self.link.callback(|files| AddDialogMsg::OnFileSelect(files)) />
                </Row>
                <Row alignment=RowAlignment::Stretch>
                    <MultiSelect
                        label=self.translator.translate("artists.add.roles")
                        selected_items=&self.roles
                        options=&self.available_roles_options
                        on_deselect=self.link.callback(|item| AddDialogMsg::OnRoleDeselected(item))
                        on_select=self.link.callback(|item| AddDialogMsg::OnRoleSelected(item))
                        on_filter=self.link.callback(|keyword| AddDialogMsg::OnRolesFilter(keyword))
                    />
                </Row>
            </ContentDialog>
        }
    }
}