use http::StatusCode;
use jinya_ui::layout::row::{Row, RowAlignment};
use jinya_ui::widgets::alert::{Alert, AlertType};
use jinya_ui::widgets::dialog::content::ContentDialog;
use jinya_ui::widgets::form::input::{Input, InputState};
use yew::{Component, ComponentLink, Html};
use yew::prelude::*;
use yew::services::fetch::FetchTask;

use crate::ajax::AjaxError;
use crate::ajax::segment_page_service::SegmentPageService;
use crate::i18n::*;
use crate::models::segment_page::SegmentPage;

pub struct EditDialog {
    link: ComponentLink<Self>,
    segment_page: SegmentPage,
    translator: Translator,
    on_save_changes: Callback<()>,
    on_discard_changes: Callback<()>,
    change_segment_page_task: Option<FetchTask>,
    segment_page_service: SegmentPageService,
    alert_message: String,
    alert_type: AlertType,
    alert_visible: bool,
    saving: bool,
    name_input_state: InputState,
    name_validation_message: String,
    title: String,
}

#[derive(Clone, PartialEq, Properties)]
pub struct EditDialogProps {
    pub segment_page: SegmentPage,
    pub on_save_changes: Callback<()>,
    pub on_discard_changes: Callback<()>,
}

pub enum EditDialogMsg {
    OnNameInput(String),
    OnEditPrimary,
    OnEditSecondary,
    OnUpdated(Result<bool, AjaxError>),
}

impl Component for EditDialog {
    type Message = EditDialogMsg;
    type Properties = EditDialogProps;

    fn create(props: Self::Properties, link: ComponentLink<Self>) -> Self {
        let translator = Translator::new();
        let title = translator.translate_with_args("segment_pages.edit.title", map! {"name" => props.segment_page.name.as_str()});

        EditDialog {
            link,
            segment_page: props.segment_page,
            translator,
            on_save_changes: props.on_save_changes,
            on_discard_changes: props.on_discard_changes,
            change_segment_page_task: None,
            segment_page_service: SegmentPageService::new(),
            alert_message: "".to_string(),
            alert_type: AlertType::Information,
            alert_visible: false,
            saving: false,
            name_input_state: InputState::Default,
            name_validation_message: "".to_string(),
            title,
        }
    }

    fn update(&mut self, msg: Self::Message) -> bool {
        match msg {
            EditDialogMsg::OnNameInput(value) => {
                self.segment_page.name = value;
                self.name_input_state = if self.segment_page.name.is_empty() {
                    self.name_validation_message = self.translator.translate("segment_pages.edit.error_segment_pagename_empty");
                    InputState::Negative
                } else {
                    self.name_validation_message = "".to_string();
                    InputState::Default
                };
            }
            EditDialogMsg::OnEditPrimary => {
                if !self.segment_page.name.is_empty() {
                    self.saving = true;
                    self.change_segment_page_task = Some(self.segment_page_service.update_segment_page(self.segment_page.id, SegmentPage::from_name(self.segment_page.name.to_string()), self.link.callback(|result| EditDialogMsg::OnUpdated(result))));
                }
            }
            EditDialogMsg::OnEditSecondary => self.on_discard_changes.emit(()),
            EditDialogMsg::OnUpdated(result) => {
                if !(result.is_ok()) {
                    self.alert_visible = true;
                    self.alert_message = match result.err().unwrap().status_code {
                        StatusCode::CONFLICT => self.translator.translate("segment_pages.edit.error_conflict"),
                        _ => self.translator.translate("segment_pages.edit.error_generic")
                    };
                    self.alert_type = AlertType::Negative;
                    self.saving = false;
                } else {
                    self.on_save_changes.emit(());
                }
            }
        }

        true
    }

    fn change(&mut self, props: Self::Properties) -> bool {
        self.segment_page = props.segment_page;
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
                primary_label=self.translator.translate("segment_pages.edit.action_save")
                secondary_label=self.translator.translate("segment_pages.edit.action_discard")
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
                    <Input validation_message=&self.name_validation_message state=&self.name_input_state disabled=self.saving label=self.translator.translate("segment_pages.edit.name") on_input=self.link.callback(|value| EditDialogMsg::OnNameInput(value)) value=&self.segment_page.name />
                </Row>
            </ContentDialog>
        }
    }
}