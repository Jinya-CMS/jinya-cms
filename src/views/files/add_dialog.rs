use http::StatusCode;
use jinya_ui::layout::row::{Row, RowAlignment};
use jinya_ui::widgets::alert::{Alert, AlertType};
use jinya_ui::widgets::dialog::content::ContentDialog;
use jinya_ui::widgets::form::file_upload::{FileUpload, FileUploadState};
use jinya_ui::widgets::form::input::{Input, InputState};
use yew::{Component, ComponentLink, Html};
use yew::prelude::*;
use yew::services::fetch::FetchTask;
use yew::services::reader::{File, FileData, ReaderService, ReaderTask};

use crate::ajax::AjaxError;
use crate::ajax::file_service::FileService;
use crate::i18n::*;
use crate::models::file::File as JinyaFile;

pub struct AddDialog {
    link: ComponentLink<Self>,
    translator: Translator,
    selected_file: Option<File>,
    on_save_changes: Callback<JinyaFile>,
    on_discard_changes: Callback<()>,
    start_upload_task: Option<FetchTask>,
    finish_upload_task: Option<FetchTask>,
    upload_chunk_task: Option<FetchTask>,
    change_file_task: Option<FetchTask>,
    file_service: FileService,
    alert_message: String,
    alert_type: AlertType,
    alert_visible: bool,
    reader_task: Option<ReaderTask>,
    saving: bool,
    name_input_state: InputState,
    name_validation_message: String,
    file_input_state: FileUploadState,
    file_validation_message: String,
    name: String,
    file: Option<JinyaFile>,
    is_open: bool,
}

#[derive(Clone, PartialEq, Properties)]
pub struct AddDialogProps {
    pub on_save_changes: Callback<JinyaFile>,
    pub on_discard_changes: Callback<()>,
    pub is_open: bool,
}

#[allow(clippy::large_enum_variant)]
pub enum AddDialogMsg {
    OnNameInput(String),
    OnFileSelect(Vec<File>),
    OnAddPrimary,
    OnAddSecondary,
    OnAdded(Result<JinyaFile, AjaxError>),
    OnUploadStarted(Result<bool, AjaxError>),
    OnChunkUploaded(Result<bool, AjaxError>),
    OnUploadFinished(Result<bool, AjaxError>),
    OnReadFile(FileData),
}

impl Component for AddDialog {
    type Message = AddDialogMsg;
    type Properties = AddDialogProps;

    fn create(props: Self::Properties, link: ComponentLink<Self>) -> Self {
        let translator = Translator::new();

        AddDialog {
            link,
            translator,
            selected_file: None,
            on_save_changes: props.on_save_changes,
            on_discard_changes: props.on_discard_changes,
            start_upload_task: None,
            finish_upload_task: None,
            upload_chunk_task: None,
            change_file_task: None,
            file_service: FileService::new(),
            alert_message: "".to_string(),
            alert_type: AlertType::Information,
            alert_visible: false,
            reader_task: None,
            saving: false,
            name_input_state: InputState::Default,
            name_validation_message: "".to_string(),
            file_input_state: FileUploadState::Default,
            file_validation_message: "".to_string(),
            name: "".to_string(),
            file: None,
            is_open: props.is_open,
        }
    }

    fn update(&mut self, msg: Self::Message) -> bool {
        match msg {
            AddDialogMsg::OnNameInput(value) => {
                self.name = value;
                self.name_input_state = if self.name.is_empty() {
                    self.name_validation_message = self.translator.translate("files.add.error_filename_empty");
                    InputState::Negative
                } else {
                    self.name_validation_message = "".to_string();
                    InputState::Default
                };
            }
            AddDialogMsg::OnAddPrimary => {
                if self.selected_file.is_none() {
                    self.file_input_state = FileUploadState::Negative;
                    self.file_validation_message = self.translator.translate("files.add.error_file_missing")
                } else if !self.name.is_empty() && self.selected_file.is_some() {
                    self.saving = true;
                    self.change_file_task = Some(self.file_service.add_file(self.name.clone(), self.link.callback(AddDialogMsg::OnAdded)));
                }
            }
            AddDialogMsg::OnAddSecondary => self.on_discard_changes.emit(()),
            AddDialogMsg::OnFileSelect(files) => {
                self.selected_file = Some(files[0].clone());
                self.file_input_state = FileUploadState::Default;
                self.file_validation_message = "".to_string();
            }
            AddDialogMsg::OnAdded(result) => {
                if let Ok(file) = result {
                    self.file = Some(file);
                    self.alert_visible = true;
                    self.alert_type = AlertType::Information;
                    self.alert_message = self.translator.translate("files.add.saving");
                    self.start_upload_task = Some(self.file_service.start_file_upload(self.file.as_ref().unwrap(), self.link.callback(AddDialogMsg::OnUploadStarted)))
                } else {
                    self.alert_visible = true;
                    self.alert_message = match result.err().unwrap().status_code {
                        StatusCode::CONFLICT => self.translator.translate("files.add.error_conflict"),
                        _ => self.translator.translate("files.add.error_generic")
                    };
                    self.alert_type = AlertType::Negative;
                    self.saving = false;
                }
            }
            AddDialogMsg::OnUploadStarted(result) => {
                if result.is_ok() {
                    let file = self.selected_file.as_ref().unwrap().clone();
                    self.reader_task = Some(ReaderService::new().read_file(file, self.link.callback(AddDialogMsg::OnReadFile)).unwrap());
                } else {
                    self.alert_visible = true;
                    self.alert_message = self.translator.translate("files.add.error_generic");
                    self.alert_type = AlertType::Negative;
                }
            }
            AddDialogMsg::OnChunkUploaded(result) => {
                if result.is_ok() {
                    self.finish_upload_task = Some(self.file_service.finish_file_upload(self.file.as_ref().unwrap(), self.link.callback(AddDialogMsg::OnUploadFinished)))
                } else {
                    self.alert_visible = true;
                    self.alert_message = self.translator.translate("files.add.error_generic");
                    self.alert_type = AlertType::Negative;
                }
            }
            AddDialogMsg::OnUploadFinished(result) => {
                if result.is_ok() {
                    self.on_save_changes.emit(self.file.as_ref().unwrap().clone());
                } else {
                    self.alert_visible = true;
                    self.alert_message = self.translator.translate("files.add.error_generic");
                    self.alert_type = AlertType::Negative;
                }
            }
            AddDialogMsg::OnReadFile(data) => {
                self.upload_chunk_task = Some(self.file_service.upload_chunk(self.file.as_ref().unwrap(), data, self.link.callback(AddDialogMsg::OnChunkUploaded)))
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
                primary_label=self.translator.translate("files.add.action_save")
                secondary_label=self.translator.translate("files.add.action_discard")
                title=self.translator.translate("files.add.title")
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
                    <Input validation_message=&self.name_validation_message state=&self.name_input_state disabled=self.saving label=self.translator.translate("files.add.name") on_input=self.link.callback(|value| AddDialogMsg::OnNameInput(value)) value=&self.name />
                </Row>
                <Row alignment=RowAlignment::Stretch>
                    <FileUpload validation_message=&self.file_validation_message state=&self.file_input_state disabled=self.saving placeholder=self.translator.translate("files.add.select_file") label=self.translator.translate("files.add.file") on_select=self.link.callback(|files| AddDialogMsg::OnFileSelect(files)) />
                </Row>
            </ContentDialog>
        }
    }
}