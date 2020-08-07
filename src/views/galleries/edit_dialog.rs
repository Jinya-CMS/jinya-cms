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

pub struct EditDialog {
    link: ComponentLink<Self>,
    translator: Translator,
    on_save_changes: Callback<bool>,
    on_discard_changes: Callback<()>,
    update_gallery_task: Option<FetchTask>,
    gallery_service: GalleryService,
    alert_message: String,
    alert_type: AlertType,
    alert_visible: bool,
    saving: bool,
    name_input_state: InputState,
    name_validation_message: String,
    gallery: Gallery,
    is_open: bool,
}

#[derive(Clone, PartialEq, Properties)]
pub struct EditDialogProps {
    pub on_save_changes: Callback<bool>,
    pub on_discard_changes: Callback<()>,
    pub is_open: bool,
    pub gallery: Gallery,
}

pub enum EditDialogMsg {
    OnNameInput(String),
    OnDescriptionInput(String),
    OnOrientationChange(Orientation),
    OnGalleryTypeChange(GalleryType),
    OnEditPrimary,
    OnEditSecondary,
    OnUpdated(Result<bool, AjaxError>),
}

impl Component for EditDialog {
    type Message = EditDialogMsg;
    type Properties = EditDialogProps;

    fn create(props: Self::Properties, link: ComponentLink<Self>) -> Self {
        let translator = Translator::new();

        EditDialog {
            link,
            translator,
            on_save_changes: props.on_save_changes,
            on_discard_changes: props.on_discard_changes,
            update_gallery_task: None,
            gallery_service: GalleryService::new(),
            alert_message: "".to_string(),
            alert_type: AlertType::Information,
            alert_visible: false,
            saving: false,
            name_input_state: InputState::Default,
            name_validation_message: "".to_string(),
            is_open: props.is_open,
            gallery: props.gallery,
        }
    }

    fn update(&mut self, msg: Self::Message) -> bool {
        match msg {
            EditDialogMsg::OnNameInput(value) => {
                self.gallery.name = value;
                self.name_input_state = if self.gallery.name.is_empty() {
                    self.name_validation_message = self.translator.translate("galleries.edit.error_name_empty");
                    InputState::Negative
                } else {
                    self.name_validation_message = "".to_string();
                    InputState::Default
                };
            }
            EditDialogMsg::OnEditPrimary => {
                if !self.gallery.name.is_empty() {
                    self.saving = true;
                    self.update_gallery_task = Some(self.gallery_service.update_gallery(self.gallery.clone(), self.link.callback(|result| EditDialogMsg::OnUpdated(result))));
                }
            }
            EditDialogMsg::OnEditSecondary => self.on_discard_changes.emit(()),
            EditDialogMsg::OnUpdated(result) => {
                if result.is_ok() {
                    self.alert_visible = true;
                    self.alert_type = AlertType::Information;
                    self.alert_message = self.translator.translate("galleries.edit.saving");
                    self.on_save_changes.emit(result.unwrap())
                } else {
                    self.alert_visible = true;
                    self.alert_message = match result.err().unwrap().status_code {
                        StatusCode::CONFLICT => self.translator.translate("galleries.edit.error_conflict"),
                        _ => self.translator.translate("galleries.edit.error_generic")
                    };
                    self.alert_type = AlertType::Negative;
                    self.saving = false;
                }
            }
            EditDialogMsg::OnDescriptionInput(value) => self.gallery.description = value,
            EditDialogMsg::OnOrientationChange(value) => self.gallery.orientation = value,
            EditDialogMsg::OnGalleryTypeChange(value) => self.gallery.gallery_type = value
        }

        true
    }

    fn change(&mut self, props: Self::Properties) -> bool {
        self.on_save_changes = props.on_save_changes;
        self.on_discard_changes = props.on_discard_changes;
        self.is_open = props.is_open;
        self.gallery = props.gallery;

        true
    }

    fn view(&self) -> Html {
        html! {
            <ContentDialog
                is_open=self.is_open
                on_primary=self.link.callback(|_| EditDialogMsg::OnEditPrimary)
                on_secondary=self.link.callback(|_| EditDialogMsg::OnEditSecondary)
                primary_label=self.translator.translate("galleries.edit.action_save")
                secondary_label=self.translator.translate("galleries.edit.action_discard")
                title=self.translator.translate_with_args("galleries.edit.title", map! {"name" => self.gallery.name.as_str()})
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
                    <Input validation_message=&self.name_validation_message state=&self.name_input_state disabled=self.saving label=self.translator.translate("galleries.edit.name") on_input=self.link.callback(|value| EditDialogMsg::OnNameInput(value)) value=&self.gallery.name />
                    <Row alignment=RowAlignment::Stretch>
                        <Label label=self.translator.translate("galleries.edit.orientation") />
                        <Radio value="vertical" checked={self.gallery.orientation == Orientation::Vertical} disabled=self.saving label=self.translator.translate("galleries.edit.orientation.vertical") on_change=self.link.callback(|value| EditDialogMsg::OnOrientationChange(Orientation::Vertical)) group="edit_orientation" />
                        <Radio value="horizontal" checked={self.gallery.orientation == Orientation::Horizontal} disabled=self.saving label=self.translator.translate("galleries.edit.orientation.horizontal") on_change=self.link.callback(|value| EditDialogMsg::OnOrientationChange(Orientation::Horizontal)) group="edit_orientation" />
                    </Row>
                    <Row alignment=RowAlignment::Stretch>
                        <Label label=self.translator.translate("galleries.edit.type") />
                        <Radio value="sequence" checked={self.gallery.gallery_type == GalleryType::Sequence} disabled=self.saving label=self.translator.translate("galleries.edit.type.sequence") on_change=self.link.callback(|value| EditDialogMsg::OnGalleryTypeChange(GalleryType::Sequence)) group="edit_gallery_type" />
                        <Radio value="masonry" checked={self.gallery.gallery_type == GalleryType::Masonry} disabled=self.saving label=self.translator.translate("galleries.edit.type.masonry") on_change=self.link.callback(|value| EditDialogMsg::OnGalleryTypeChange(GalleryType::Masonry)) group="edit_gallery_type" />
                    </Row>
                    <Textarea label=self.translator.translate("galleries.edit.description") value=&self.gallery.description on_input=self.link.callback(|value| EditDialogMsg::OnDescriptionInput(value)) />
                </Form>
            </ContentDialog>
        }
    }
}