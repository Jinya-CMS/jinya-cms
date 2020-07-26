use anyhow::Error;
use jinya_ui::layout::page::Page;
use jinya_ui::listing::card::{Card, CardButton, CardContainer};
use jinya_ui::widgets::button::ButtonType;
use jinya_ui::widgets::floating_action_button::Fab;
use yew::{Bridge, Bridged, Component, ComponentLink, Html};
use yew::prelude::*;
use yew::services::ConsoleService;
use yew::services::fetch::FetchTask;

use crate::agents::menu_agent::{MenuAgent, MenuAgentResponse};
use crate::ajax::file_service::FileService;
use crate::ajax::get_host;
use crate::i18n::Translator;
use crate::models::file::File;
use crate::models::list_model::ListModel;

pub struct FilesPage {
    link: ComponentLink<Self>,
    menu_agent: Box<dyn Bridge<MenuAgent>>,
    files: Vec<File>,
    file_service: FileService,
    load_files_task: Option<FetchTask>,
    translator: Translator,
}

pub enum Msg {
    OnFilesLoaded(Result<ListModel<File>, Error>),
    OnFileSelected(usize),
    OnMenuAgentResponse(MenuAgentResponse),
    OnUploadFileClicked,
    OnDeleteFileClicked(usize),
    OnEditFileClicked(usize),
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
            Msg::OnFileSelected(idx) => {}
            Msg::OnUploadFileClicked => {}
            Msg::OnDeleteFileClicked(idx) => {}
            Msg::OnEditFileClicked(idx) => {}
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