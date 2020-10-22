use http::StatusCode;
use jinya_ui::layout::row::{Row, RowAlignment};
use jinya_ui::widgets::alert::{Alert, AlertType};
use jinya_ui::widgets::dialog::content::ContentDialog;
use jinya_ui::widgets::form::file_upload::FileUpload;
use jinya_ui::widgets::form::input::{Input, InputState};
use yew::{Component, ComponentLink, Html};
use yew::prelude::*;
use yew::services::fetch::FetchTask;
use yew::services::reader::{File, FileData, ReaderService, ReaderTask};

use crate::ajax::AjaxError;
use crate::ajax::file_service::FileService;
use crate::i18n::*;
use crate::models::file::File as JinyaFile;

pub struct EditDialog {
    link: ComponentLink<Self>,
    file: JinyaFile,
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
    title: String,
}

#[derive(Clone, PartialEq, Properties)]
pub struct EditDialogProps {
    pub file: JinyaFile,
    pub on_save_changes: Callback<JinyaFile>,
    pub on_discard_changes: Callback<()>,
}

pub enum EditDialogMsg {
    OnNameInput(String),
    OnFileSelect(Vec<File>),
    OnEditPrimary,
    OnEditSecondary,
    OnUpdated(Result<bool, AjaxError>),
    OnUploadStarted(Result<bool, AjaxError>),
    OnChunkUploaded(Result<bool, AjaxError>),
    OnUploadFinished(Result<bool, AjaxError>),
    OnReadFile(FileData),
}

impl Component for EditDialog {
    type Message = EditDialogMsg;
    type Properties = EditDialogProps;

    fn create(props: Self::Properties, link: ComponentLink<Self>) -> Self {
        let translator = Translator::new();
        let title = translator.translate_with_args("files.edit.title", map! {"name" => props.file.name.as_str()});

        EditDialog {
            link,
            file: props.file,
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
            title,
        }
    }

    fn update(&mut self, msg: Self::Message) -> bool {
        match msg {
            EditDialogMsg::OnNameInput(value) => {
                self.file.name = value;
                self.name_input_state = if self.file.name.is_empty() {
                    self.name_validation_message = self.translator.translate("files.edit.error_filename_empty");
                    InputState::Negative
                } else {
                    self.name_validation_message = "".to_string();
                    InputState::Default
                };
            }
            EditDialogMsg::OnEditPrimary => {
                if !self.file.name.is_empty() {
                    self.saving = true;
                    self.change_file_task = Some(self.file_service.update_file(&self.file, self.link.callback(|result| EditDialogMsg::OnUpdated(result))));
                }
            }
            EditDialogMsg::OnEditSecondary => self.on_discard_changes.emit(()),
            EditDialogMsg::OnFileSelect(files) => self.selected_file = Some(files[0].clone()),
            EditDialogMsg::OnUpdated(result) => {
                if result.is_ok() {
                    if self.selected_file.is_some() {
                        self.alert_type = AlertType::Information;
                        self.alert_visible = true;
                        self.alert_message = self.translator.translate("files.edit.saving");
                        self.start_upload_task = Some(self.file_service.start_file_upload(&self.file, self.link.callback(|result| EditDialogMsg::OnUploadStarted(result))))
                    } else {
                        self.on_save_changes.emit(self.file.clone());
                    }
                } else {
                    self.alert_visible = true;
                    self.alert_message = match result.err().unwrap().status_code {
                        StatusCode::CONFLICT => self.translator.translate("files.edit.error_conflict"),
                        _ => self.translator.translate("files.edit.error_generic")
                    };
                    self.alert_type = AlertType::Negative;
                    self.saving = false;
                }
            }
            EditDialogMsg::OnUploadStarted(result) => {
                if result.is_ok() {
                    let file = self.selected_file.as_ref().unwrap().clone();
                    self.reader_task = Some(ReaderService::new().read_file(file, self.link.callback(|data| EditDialogMsg::OnReadFile(data))).unwrap());
                } else {
                    self.alert_visible = true;
                    self.alert_message = self.translator.translate("files.edit.error_generic");
                    self.alert_type = AlertType::Negative;
                }
            }
            EditDialogMsg::OnChunkUploaded(result) => {
                if result.is_ok() {
                    self.finish_upload_task = Some(self.file_service.finish_file_upload(&self.file, self.link.callback(|result| EditDialogMsg::OnUploadFinished(result))))
                } else {
                    self.alert_visible = true;
                    self.alert_message = self.translator.translate("files.edit.error_generic");
                    self.alert_type = AlertType::Negative;
                }
            }
            EditDialogMsg::OnUploadFinished(result) => {
                if result.is_ok() {
                    self.on_save_changes.emit(self.file.clone());
                } else {
                    self.alert_visible = true;
                    self.alert_message = self.translator.translate("files.edit.error_generic");
                    self.alert_type = AlertType::Negative;
                }
            }
            EditDialogMsg::OnReadFile(data) => {
                self.upload_chunk_task = Some(self.file_service.upload_chunk(&self.file, data, self.link.callback(|result| EditDialogMsg::OnChunkUploaded(result))))
            }
        }

        true
    }

    fn change(&mut self, props: Self::Properties) -> bool {
        self.file = props.file;
        self.on_save_changes = props.on_save_changes;
        self.on_discard_changes = props.on_discard_changes;

        true
    }

    fn view(&self) -> Html {
        html! {
            <ContentDialog
                is_open=true
                on_primary=self.link.callback(|_| EditDialogMsg::OnEditPrimary)
                on_secondary=self.link.callback(|_| EditDialogMsg::OnEditSecondary)
                primary_label=self.translator.translate("files.edit.action_save")
                secondary_label=self.translator.translate("files.edit.action_discard")
                title=&self.title
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
                    <Input validation_message=&self.name_validation_message state=&self.name_input_state disabled=self.saving label=self.translator.translate("files.edit.name") on_input=self.link.callback(|value| EditDialogMsg::OnNameInput(value)) value=&self.file.name />
                </Row>
                <Row alignment=RowAlignment::Stretch>
                    <FileUpload disabled=self.saving filename=self.translator.translate("files.edit.file_selected") label=self.translator.translate("files.edit.file") on_select=self.link.callback(|files| EditDialogMsg::OnFileSelect(files)) />
                </Row>
            </ContentDialog>
        }
    }
}