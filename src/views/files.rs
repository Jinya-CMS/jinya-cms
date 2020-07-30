use anyhow::Error;
use jinya_ui::layout::page::Page;
use jinya_ui::layout::row::Row;
use jinya_ui::listing::card::{Card, CardButton, CardContainer};
use jinya_ui::widgets::alert::{Alert, AlertType};
use jinya_ui::widgets::button::ButtonType;
use jinya_ui::widgets::dialog::confirmation::{ConfirmationDialog, DialogType};
use jinya_ui::widgets::floating_action_button::Fab;
use yew::{Bridge, Bridged, Component, ComponentLink, Html};
use yew::prelude::*;
use yew::services::ConsoleService;
use yew::services::fetch::FetchTask;

use crate::agents::menu_agent::{MenuAgent, MenuAgentResponse};
use crate::ajax::file_service::FileService;
use crate::ajax::get_host;
use crate::i18n::*;
use crate::models::file::File;
use crate::models::list_model::ListModel;

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
}

pub enum Msg {
    OnFilesLoaded(Result<ListModel<File>, Error>),
    OnFileSelected(usize),
    OnMenuAgentResponse(MenuAgentResponse),
    OnUploadFileClicked,
    OnDeleteFileClicked(usize),
    OnEditFileClicked(usize),
    DeleteApprove,
    DeleteDecline,
    FileDeleted,
    DeleteFailed,
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
        }
    }

    fn update(&mut self, msg: Self::Message) -> bool {
        match msg {
            Msg::OnFilesLoaded(list) => {
                if list.is_ok() {
                    self.files = list.unwrap().items;
                } else {
                    ConsoleService::info(list.err().unwrap().to_string().as_str());
                }
            }
            Msg::OnMenuAgentResponse(response) => match response {
                MenuAgentResponse::OnSearch(value) => self.load_files_task = Some(self.file_service.get_list(value, self.link.callback(|response| Msg::OnFilesLoaded(response)))),
                _ => {}
            },
            Msg::OnFileSelected(idx) => self.selected_file = Some(self.files[idx].clone()),
            Msg::OnUploadFileClicked => {}
            Msg::OnDeleteFileClicked(idx) => self.file_to_delete = Some(self.files[idx].clone()),
            Msg::OnEditFileClicked(idx) => self.file_to_edit = Some(self.files[idx].clone()),
            Msg::DeleteApprove => {
                self.delete_file_task = Some(self.file_service.delete_file(self.file_to_delete.as_ref().unwrap(), self.link.callback(|result: Result<bool, Error>| {
                    if result.is_ok() {
                        Msg::FileDeleted
                    } else {
                        ConsoleService::error(result.err().unwrap().to_string().as_str());
                        Msg::DeleteFailed
                    }
                })))
            }
            Msg::DeleteDecline => self.file_to_delete = None,
            Msg::FileDeleted => {
                self.load_files_task = Some(self.file_service.get_list("".to_string(), self.link.callback(|response| Msg::OnFilesLoaded(response))));
                self.file_to_delete = None;
            }
            Msg::DeleteFailed => {
                self.alert_type = AlertType::Negative;
                self.alert_message = Some(self.translator.translate_with_args("files.delete.failed", map! {"name" => self.file_to_delete.as_ref().unwrap().name.as_str()}));
                self.file_to_delete = None;
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
                            on_approve=self.link.callback(|_| Msg::DeleteApprove)
                            on_decline=self.link.callback(|_| Msg::DeleteDecline)
                            is_open=self.file_to_delete.is_some()
                        />
                    }
                } else {
                    html! {}
                }}
                <Fab icon="file-upload" on_click=self.link.callback(|_| Msg::OnUploadFileClicked) />
            </Page>
        }
    }

    fn rendered(&mut self, first_render: bool) {
        if first_render {
            self.load_files_task = Some(self.file_service.get_list("".to_string(), self.link.callback(|response| Msg::OnFilesLoaded(response))));
        }
    }
}