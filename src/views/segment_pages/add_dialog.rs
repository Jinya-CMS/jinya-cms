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

pub struct AddDialog {
    link: ComponentLink<Self>,
    translator: Translator,
    on_save_changes: Callback<()>,
    on_discard_changes: Callback<()>,
    segment_page_service: SegmentPageService,
    alert_message: String,
    alert_type: AlertType,
    alert_visible: bool,
    saving: bool,
    name_input_state: InputState,
    name_validation_message: String,
    name: String,
    is_open: bool,
    create_segment_page_task: Option<FetchTask>,
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
            segment_page_service: SegmentPageService::new(),
            alert_message: "".to_string(),
            alert_type: AlertType::Information,
            alert_visible: false,
            saving: false,
            name_input_state: InputState::Default,
            name_validation_message: "".to_string(),
            name: "".to_string(),
            is_open: props.is_open,
            create_segment_page_task: None,
        }
    }

    fn update(&mut self, msg: Self::Message) -> bool {
        match msg {
            AddDialogMsg::OnNameInput(value) => {
                self.name = value;
                self.name_input_state = if self.name.is_empty() {
                    self.name_validation_message = self.translator.translate("segment_pages.add.error_segment_pagename_empty");
                    InputState::Negative
                } else {
                    self.name_validation_message = "".to_string();
                    InputState::Default
                };
            }
            AddDialogMsg::OnAddPrimary => {
                if !self.name.is_empty() {
                    self.saving = true;
                    self.create_segment_page_task = Some(self.segment_page_service.create_segment_page(SegmentPage::from_name(self.name.to_string()), self.link.callback(|result| AddDialogMsg::OnAdded(result))));
                }
            }
            AddDialogMsg::OnAddSecondary => self.on_discard_changes.emit(()),
            AddDialogMsg::OnAdded(result) => {
                if !(result.is_ok()) {
                    self.alert_visible = true;
                    self.alert_message = match result.err().unwrap().status_code {
                        StatusCode::CONFLICT => self.translator.translate("segment_pages.add.error_conflict"),
                        _ => self.translator.translate("segment_pages.add.error_generic")
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
                primary_label=self.translator.translate("segment_pages.add.action_save")
                secondary_label=self.translator.translate("segment_pages.add.action_discard")
                title=self.translator.translate("segment_pages.add.title")
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
                    <Input validation_message=&self.name_validation_message state=&self.name_input_state disabled=self.saving label=self.translator.translate("segment_pages.add.name") on_input=self.link.callback(|value| AddDialogMsg::OnNameInput(value)) value=&self.name />
                </Row>
            </ContentDialog>
        }
    }
}