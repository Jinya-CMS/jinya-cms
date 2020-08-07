use http::StatusCode;
use jinya_ui::layout::form::Form;
use jinya_ui::layout::row::{Row, RowAlignment};
use jinya_ui::widgets::alert::{Alert, AlertType};
use jinya_ui::widgets::dialog::content::ContentDialog;
use jinya_ui::widgets::form::input::{Input, InputState};
use jinya_ui::widgets::form::label::Label;
use jinya_ui::widgets::form::radio::Radio;
use jinya_ui::widgets::form::textarea::Textarea;
use yew::{Component, ComponentLink, Html};
use yew::prelude::*;
use yew::services::fetch::FetchTask;

use crate::ajax::AjaxError;
use crate::ajax::gallery_service::GalleryService;
use crate::i18n::*;
use crate::models::gallery::{Gallery, GalleryType, Orientation};

pub struct AddDialog {
    link: ComponentLink<Self>,
    translator: Translator,
    on_save_changes: Callback<Gallery>,
    on_discard_changes: Callback<()>,
    create_gallery_task: Option<FetchTask>,
    gallery_service: GalleryService,
    alert_message: String,
    alert_type: AlertType,
    alert_visible: bool,
    saving: bool,
    name_input_state: InputState,
    name_validation_message: String,
    name: String,
    description: String,
    gallery_type: GalleryType,
    orientation: Orientation,
    is_open: bool,
}

#[derive(Clone, PartialEq, Properties)]
pub struct AddDialogProps {
    pub on_save_changes: Callback<Gallery>,
    pub on_discard_changes: Callback<()>,
    pub is_open: bool,
}

pub enum AddDialogMsg {
    OnNameInput(String),
    OnDescriptionInput(String),
    OnOrientationChange(Orientation),
    OnGalleryTypeChange(GalleryType),
    OnAddPrimary,
    OnAddSecondary,
    OnAdded(Result<Gallery, AjaxError>),
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
            create_gallery_task: None,
            gallery_service: GalleryService::new(),
            alert_message: "".to_string(),
            alert_type: AlertType::Information,
            alert_visible: false,
            saving: false,
            name_input_state: InputState::Default,
            name_validation_message: "".to_string(),
            name: "".to_string(),
            description: "".to_string(),
            gallery_type: GalleryType::Sequence,
            orientation: Orientation::Horizontal,
            is_open: props.is_open,
        }
    }

    fn update(&mut self, msg: Self::Message) -> bool {
        match msg {
            AddDialogMsg::OnNameInput(value) => {
                self.name = value;
                self.name_input_state = if self.name.is_empty() {
                    self.name_validation_message = self.translator.translate("galleries.add.error_name_empty");
                    InputState::Negative
                } else {
                    self.name_validation_message = "".to_string();
                    InputState::Default
                };
            }
            AddDialogMsg::OnAddPrimary => {
                if !self.name.is_empty() {
                    self.saving = true;
                    self.create_gallery_task = Some(self.gallery_service.add_gallery(self.name.clone(), self.description.clone(), self.orientation.clone(), self.gallery_type.clone(), self.link.callback(|result| AddDialogMsg::OnAdded(result))));
                }
            }
            AddDialogMsg::OnAddSecondary => self.on_discard_changes.emit(()),
            AddDialogMsg::OnAdded(result) => {
                if result.is_ok() {
                    self.alert_visible = true;
                    self.alert_type = AlertType::Information;
                    self.alert_message = self.translator.translate("galleries.add.saving");
                    self.on_save_changes.emit(result.unwrap())
                } else {
                    self.alert_visible = true;
                    self.alert_message = match result.err().unwrap().status_code {
                        StatusCode::CONFLICT => self.translator.translate("galleries.add.error_conflict"),
                        _ => self.translator.translate("galleries.add.error_generic")
                    };
                    self.alert_type = AlertType::Negative;
                    self.saving = false;
                }
            }
            AddDialogMsg::OnDescriptionInput(value) => self.description = value,
            AddDialogMsg::OnOrientationChange(value) => self.orientation = value,
            AddDialogMsg::OnGalleryTypeChange(value) => self.gallery_type = value
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
                primary_label=self.translator.translate("galleries.add.action_save")
                secondary_label=self.translator.translate("galleries.add.action_discard")
                title=self.translator.translate("galleries.add.title")
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
                <Form>
                    <Input validation_message=&self.name_validation_message state=&self.name_input_state disabled=self.saving label=self.translator.translate("galleries.add.name") on_input=self.link.callback(|value| AddDialogMsg::OnNameInput(value)) value=&self.name />
                    <Row alignment=RowAlignment::Stretch>
                        <Label label=self.translator.translate("galleries.add.orientation") />
                        <Radio value="vertical" checked={self.orientation == Orientation::Vertical} disabled=self.saving label=self.translator.translate("galleries.add.orientation.vertical") on_change=self.link.callback(|value| AddDialogMsg::OnOrientationChange(Orientation::Vertical)) group="add_orientation" />
                        <Radio value="horizontal" checked={self.orientation == Orientation::Horizontal} disabled=self.saving label=self.translator.translate("galleries.add.orientation.horizontal") on_change=self.link.callback(|value| AddDialogMsg::OnOrientationChange(Orientation::Horizontal)) group="add_orientation" />
                    </Row>
                    <Row alignment=RowAlignment::Stretch>
                        <Label label=self.translator.translate("galleries.add.type") />
                        <Radio value="sequence" checked={self.gallery_type == GalleryType::Sequence} disabled=self.saving label=self.translator.translate("galleries.add.type.sequence") on_change=self.link.callback(|value| AddDialogMsg::OnGalleryTypeChange(GalleryType::Sequence)) group="add_gallery_type" />
                        <Radio value="masonry" checked={self.gallery_type == GalleryType::Masonry} disabled=self.saving label=self.translator.translate("galleries.add.type.masonry") on_change=self.link.callback(|value| AddDialogMsg::OnGalleryTypeChange(GalleryType::Masonry)) group="add_gallery_type" />
                    </Row>
                    <Textarea label=self.translator.translate("galleries.add.description") value=&self.description on_input=self.link.callback(|value| AddDialogMsg::OnDescriptionInput(value)) />
                </Form>
            </ContentDialog>
        }
    }
}