use anyhow::Error;
use jinya_ui::layout::page::Page;
use jinya_ui::layout::row::Row;
use jinya_ui::listing::card::{Card, CardButton, CardContainer};
use jinya_ui::widgets::alert::{Alert, AlertType};
use jinya_ui::widgets::button::ButtonType;
use jinya_ui::widgets::dialog::confirmation::{ConfirmationDialog, DialogType};
use jinya_ui::widgets::floating_action_button::Fab;
use jinya_ui::widgets::toast::Toast;
use yew::{Bridge, Bridged, Component, ComponentLink, Html};
use yew::prelude::*;
use yew::services::ConsoleService;
use yew::services::fetch::FetchTask;

use add_dialog::AddDialog;
use edit_dialog::EditDialog;

use crate::agents::menu_agent::{MenuAgent, MenuAgentRequest, MenuAgentResponse};
use crate::ajax::{AjaxError, get_host};
use crate::ajax::file_service::FileService;
use crate::i18n::*;
use crate::models::file::File;
use crate::models::list_model::ListModel;

mod add_dialog;
mod edit_dialog;

pub struct FilesPage {
    link: ComponentLink<Self>,
    menu_agent: Box<dyn Bridge<MenuAgent>>,
    files: Vec<File>,
    file_service: FileService,
    load_files_task: Option<FetchTask>,
    translator: Translator,
    selected_file: Option<File>,
    file_to_edit: Option<File>,
    file_to_delete: Option<File>,
    delete_file_task: Option<FetchTask>,
    alert_type: AlertType,
    alert_message: Option<String>,
    add_file_open: bool,
    keyword: String,
}

pub enum Msg {
    OnFilesLoaded(Result<ListModel<File>, AjaxError>),
    OnFileSelected(usize),
    OnMenuAgentResponse(MenuAgentResponse),
    OnUploadFileClicked,
    OnDeleteFileClicked(usize),
    OnEditFileClicked(usize),
    OnDeleteApprove,
    OnDeleteDecline,
    OnFileDeleted,
    OnDeleteFailed,
    OnSaveEdit(File),
    OnDiscardEdit,
    OnSaveAdd(File),
    OnDiscardAdd,
}

impl Component for FilesPage {
    type Message = Msg;
    type Properties = ();

    fn create(_props: Self::Properties, link: ComponentLink<Self>) -> Self {
        let menu_agent = MenuAgent::bridge(link.callback(|response| Msg::OnMenuAgentResponse(response)));
        FilesPage {
            link,
            menu_agent,
            files: vec![],
            file_service: FileService::new(),
            load_files_task: None,
            translator: Translator::new(),
            selected_file: None,
            file_to_edit: None,
            file_to_delete: None,
            delete_file_task: None,
            alert_type: AlertType::Negative,
            alert_message: None,
            add_file_open: false,
            keyword: "".to_string(),
        }
    }

    fn update(&mut self, msg: Self::Message) -> bool {
        match msg {
            Msg::OnFilesLoaded(list) => {
                if list.is_ok() {
                    self.files = list.unwrap().items
                } else {
                    ConsoleService::error(list.err().unwrap().error.to_string().as_str());
                }
            }
            Msg::OnMenuAgentResponse(response) => match response {
                MenuAgentResponse::OnSearch(value) => {
                    self.keyword = value;
                    self.load_files_task = Some(self.file_service.get_list(self.keyword.to_string(), self.link.callback(|response| Msg::OnFilesLoaded(response))));
                }
                _ => {}
            },
            Msg::OnFileSelected(idx) => self.selected_file = Some(self.files[idx].clone()),
            Msg::OnUploadFileClicked => self.add_file_open = true,
            Msg::OnDeleteFileClicked(idx) => self.file_to_delete = Some(self.files[idx].clone()),
            Msg::OnEditFileClicked(idx) => self.file_to_edit = Some(self.files[idx].clone()),
            Msg::OnDeleteApprove => {
                self.delete_file_task = Some(self.file_service.delete_file(self.file_to_delete.as_ref().unwrap(), self.link.callback(|result: Result<bool, AjaxError>| {
                    if result.is_ok() {
                        Msg::OnFileDeleted
                    } else {
                        ConsoleService::error(result.err().unwrap().error.to_string().as_str());
                        Msg::OnDeleteFailed
                    }
                })))
            }
            Msg::OnDeleteDecline => self.file_to_delete = None,
            Msg::OnFileDeleted => {
                self.load_files_task = Some(self.file_service.get_list(self.keyword.to_string(), self.link.callback(|response| Msg::OnFilesLoaded(response))));
                self.file_to_delete = None;
            }
            Msg::OnDeleteFailed => {
                self.alert_type = AlertType::Negative;
                self.alert_message = Some(self.translator.translate_with_args("files.delete.failed", map! {"name" => self.file_to_delete.as_ref().unwrap().name.as_str()}));
                self.file_to_delete = None;
            }
            Msg::OnSaveEdit(name) => {
                self.load_files_task = Some(self.file_service.get_list(self.keyword.to_string(), self.link.callback(|response| Msg::OnFilesLoaded(response))));
                Toast::positive_toast(self.translator.translate_with_args("files.edit.saved.success", map! {"name" => name.name.as_str()}));
                self.file_to_edit = None;
            }
            Msg::OnDiscardEdit => self.file_to_edit = None,
            Msg::OnSaveAdd(name) => {
                self.load_files_task = Some(self.file_service.get_list(self.keyword.to_string(), self.link.callback(|response| Msg::OnFilesLoaded(response))));
                Toast::positive_toast(self.translator.translate_with_args("files.add.saved.success", map! {"name" => name.name.as_str()}));
                self.add_file_open = false
            }
            Msg::OnDiscardAdd => self.add_file_open = false
        }

        true
    }

    fn change(&mut self, _props: Self::Properties) -> bool {
        false
    }

    fn view(&self) -> Html {
        html! {
            <Page>
                <div>
                    {if self.alert_message.is_some() {
                        html! {
                            <Row>
                                <Alert alert_type=&self.alert_type message=self.alert_message.as_ref().unwrap() />
                            </Row>
                        }
                    } else {
                        html! {}
                    }}
                    <div style="display: flex; align-items: flex-start;">
                        <CardContainer>
                            {for self.files.iter().enumerate().map(move |(idx, item)| {
                                html! {
                                    <Card title=&item.name src=format!("{}{}", get_host(), &item.path)>
                                         <CardButton button_type=ButtonType::Information icon="information-outline" tooltip=self.translator.translate("files.card.button.information") on_click=self.link.callback(move |_| Msg::OnFileSelected(idx)) />
                                         <CardButton button_type=ButtonType::Secondary icon="pencil" tooltip=self.translator.translate("files.card.button.edit") on_click=self.link.callback(move |_| Msg::OnEditFileClicked(idx)) />
                                         <CardButton button_type=ButtonType::Negative icon="delete" tooltip=self.translator.translate("files.card.button.delete") on_click=self.link.callback(move |_| Msg::OnDeleteFileClicked(idx)) />
                                    </Card>
                                }
                            })}
                        </CardContainer>
                        {if self.selected_file.is_some() {
                            let selected_file = self.selected_file.as_ref().unwrap();
                            let selected_file_created_at = selected_file.created.get_at().format("%d.%m.%Y %R");
                            let selected_file_updated_at = selected_file.updated.get_at().format("%d.%m.%Y %R");
                            html! {
                                <div style="width: 20rem; margin-left: 1rem; top: calc(3.5rem + var(--line-height-23)); position: sticky; background: var(--dropback); border-radius: 1rem">
                                    <img src=format!("{}{}", get_host(), &selected_file.path) style="width: 20rem; object-fit: scale-down; border-top-left-radius: 1rem; border-top-right-radius: 1rem" />
                                    <div style="padding: 1rem; border-bottom-left-radius: 1rem; border-bottom-right-radius: 1rem; display: flex; flex-flow: column wrap;">
                                        <span style="font-size: var(--font-size-24); flex: 0 0 100%;">{&selected_file.name}</span>
                                        <span style="font-weight: var(--font-weight-bold); flex: 0 0 100%;">{self.translator.translate("files.details.created_by")}</span>
                                        <span style="flex: 0 0 100%;">{format!("{} ", &selected_file.created.by.artist_name)}<a href=format!("mailto:{}", &selected_file.created.by.email)>{&selected_file.created.by.email}</a></span>
                                        <span style="font-weight: var(--font-weight-bold); flex: 0 0 100%;">{self.translator.translate("files.details.created_at")}</span>
                                        <span style="flex: 0 0 100%;">{&selected_file_created_at}</span>
                                        <span style="font-weight: var(--font-weight-bold); flex: 0 0 100%; margin-top: 1rem">{self.translator.translate("files.details.last_updated_by")}</span>
                                        <span style="flex: 0 0 100%;">{format!("{} ", &selected_file.updated.by.artist_name)}<a href=format!("mailto:{}", &selected_file.updated.by.email)>{&selected_file.updated.by.email}</a></span>
                                        <span style="font-weight: var(--font-weight-bold); flex: 0 0 100%;">{self.translator.translate("files.details.last_updated_at")}</span>
                                        <span style="flex: 0 0 100%;">{&selected_file_updated_at}</span>
                                    </div>
                                </div>
                            }
                        } else {
                            html! {}
                        }}
                    </div>
                </div>
                {if self.file_to_delete.is_some() {
                    let file = self.file_to_delete.as_ref().unwrap();
                    html! {
                        <ConfirmationDialog
                            title=self.translator.translate("files.delete.title")
                            dialog_type=DialogType::Negative
                            message=self.translator.translate_with_args("files.delete.content", map!{"name" => file.name.as_str()})
                            decline_label=self.translator.translate("files.delete.decline")
                            approve_label=self.translator.translate("files.delete.approve")
                            on_approve=self.link.callback(|_| Msg::OnDeleteApprove)
                            on_decline=self.link.callback(|_| Msg::OnDeleteDecline)
                            is_open=self.file_to_delete.is_some()
                        />
                    }
                } else {
                    html! {}
                }}
                {if self.file_to_edit.is_some() {
                    let file = self.file_to_edit.as_ref().unwrap();
                    html! {
                        <EditDialog file=file on_save_changes=self.link.callback(|file| Msg::OnSaveEdit(file)) on_discard_changes=self.link.callback(|_| Msg::OnDiscardEdit) />
                    }
                } else {
                    html! {}
                }}
                <AddDialog is_open=self.add_file_open on_save_changes=self.link.callback(|file| Msg::OnSaveAdd(file)) on_discard_changes=self.link.callback(|_| Msg::OnDiscardAdd) />
                <Fab icon="file-upload" on_click=self.link.callback(|_| Msg::OnUploadFileClicked) />
            </Page>
        }
    }

    fn rendered(&mut self, first_render: bool) {
        if first_render {
            self.menu_agent.send(MenuAgentRequest::ChangeTitle(self.translator.translate("app.menu.content.media.files")));
            self.load_files_task = Some(self.file_service.get_list("".to_string(), self.link.callback(|response| Msg::OnFilesLoaded(response))));
        }
    }
}