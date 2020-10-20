use http::StatusCode;
use jinya_ui::layout::button_row::ButtonRow;
use jinya_ui::layout::page::Page;
use jinya_ui::layout::row::Row;
use jinya_ui::widgets::button::{Button, ButtonType};
use jinya_ui::widgets::floating_action_button::Fab;
use jinya_ui::widgets::form::file_upload::{FileUpload, FileUploadState};
use jinya_ui::widgets::form::input::{Input, InputState};
use jinya_ui::widgets::toast::Toast;
use web_sys::Node;
use yew::{Component, ComponentLink, Html};
use yew::agent::Dispatcher;
use yew::prelude::*;
use yew::services::fetch::FetchTask;
use yew::services::reader::{File, FileData, ReaderService, ReaderTask};
use yew::virtual_dom::VNode;
use yew_router::agent::{RouteAgent, RouteRequest};
use yew_router::route::Route;

use crate::agents::menu_agent::{MenuAgent, MenuAgentRequest};
use crate::ajax::{AjaxError, get_host};
use crate::ajax::profile_service::ProfileService;
use crate::app::AppRoute;
use crate::i18n::Translator;
use crate::models::artist::Artist;
use crate::utils::TinyMce;

pub struct MyProfilePage {
    link: ComponentLink<Self>,
    translator: Translator,
    load_profile_task: Option<FetchTask>,
    save_profile_picture_task: Option<FetchTask>,
    save_profile_task: Option<FetchTask>,
    profile_service: ProfileService,
    menu_dispatcher: Dispatcher<MenuAgent>,
    tinymce: TinyMce,
    profile_saving: bool,
    picture_saving: bool,
    edit_mode: bool,
    profile: Option<Artist>,
    tinymce_initialized: bool,
    email: String,
    artist_name: String,
    selected_file: Option<File>,
    artist_name_state: InputState,
    artist_name_validation_message: String,
    email_state: InputState,
    email_validation_message: String,
    reader_service: ReaderService,
    reader_task: Option<ReaderTask>,
}

pub enum Msg {
    OnEditProfile,
    OnProfileLoaded(Result<Artist, AjaxError>),
    OnSaveClick,
    OnDiscardClick,
    OnArtistNameInput(String),
    OnEmailInput(String),
    OnFileSelect(Vec<File>),
    OnDetailsSaved(Result<bool, AjaxError>),
    OnProfilePictureRead(FileData),
    OnProfilePictureSaved(Result<bool, AjaxError>),
}

impl Component for MyProfilePage {
    type Message = Msg;
    type Properties = ();

    fn create(_props: Self::Properties, link: ComponentLink<Self>) -> Self {
        let translator = Translator::new();
        let menu_dispatcher = MenuAgent::dispatcher();

        MyProfilePage {
            link,
            translator,
            load_profile_task: None,
            save_profile_picture_task: None,
            save_profile_task: None,
            profile_service: ProfileService::new(),
            menu_dispatcher,
            tinymce: TinyMce::new("about-me-content".to_string()),
            profile_saving: false,
            picture_saving: false,
            edit_mode: false,
            profile: None,
            tinymce_initialized: false,
            email: "".to_string(),
            artist_name: "".to_string(),
            selected_file: None,
            artist_name_state: InputState::Default,
            artist_name_validation_message: "".to_string(),
            email_state: InputState::Default,
            email_validation_message: "".to_string(),
            reader_service: ReaderService::new(),
            reader_task: None,
        }
    }

    fn update(&mut self, msg: Self::Message) -> bool {
        match msg {
            Msg::OnEditProfile => {
                self.edit_mode = true;
                self.artist_name = self.profile.as_ref().unwrap().artist_name.to_string();
                self.email = self.profile.as_ref().unwrap().email.to_string();
            }
            Msg::OnProfileLoaded(result) => {
                if result.is_ok() {
                    self.profile = Some(result.unwrap());
                    self.menu_dispatcher.send(MenuAgentRequest::ChangeTitle(self.profile.as_ref().unwrap().artist_name.clone()));
                } else {}
            }
            Msg::OnSaveClick => {
                self.profile_saving = true;
                if self.selected_file.is_some() {
                    self.picture_saving = true;
                    self.reader_task = Some(self.reader_service.read_file(self.selected_file.clone().unwrap(), self.link.callback(|data| Msg::OnProfilePictureRead(data))).unwrap())
                }
                self.save_profile_task = Some(self.profile_service.put_profile(self.artist_name.clone(), self.email.clone(), self.tinymce.get_content(), self.link.callback(|result| Msg::OnDetailsSaved(result))));
            }
            Msg::OnDiscardClick => {
                self.edit_mode = false;
                self.tinymce.destroy_editor();
            }
            Msg::OnArtistNameInput(value) => {
                if value.is_empty() {
                    self.artist_name_state = InputState::Negative;
                    self.artist_name_validation_message = self.translator.translate("my_jinya.my_account.my_profile.error_artist_name_empty");
                } else {
                    self.artist_name_state = InputState::Default;
                    self.artist_name_validation_message = "".to_string();
                }
                self.artist_name = value;
            }
            Msg::OnEmailInput(value) => {
                if value.is_empty() {
                    self.email_state = InputState::Negative;
                    self.email_validation_message = self.translator.translate("my_jinya.my_account.my_profile.error_email_empty");
                } else {
                    self.email_state = InputState::Default;
                    self.email_validation_message = "".to_string();
                }
                self.email = value;
            }
            Msg::OnFileSelect(files) => {
                self.selected_file = Some(files[0].clone());
            }
            Msg::OnDetailsSaved(result) => {
                if result.is_ok() {
                    self.profile_saving = false;
                    self.disable_edit_mode();
                } else {
                    Toast::negative_toast(self.translator.translate("my_jinya.my_account.my_profile.error_profile_failed"));
                }
            }
            Msg::OnProfilePictureRead(data) => {
                self.save_profile_picture_task = Some(self.profile_service.upload_profile_picture(data.content, self.link.callback(|result| Msg::OnProfilePictureSaved(result))));
            }
            Msg::OnProfilePictureSaved(result) => {
                if result.is_ok() {
                    self.picture_saving = false;
                    self.disable_edit_mode();
                } else {
                    Toast::negative_toast(self.translator.translate("my_jinya.my_account.my_profile.error_profile_picture_failed"));
                }
            }
        }

        true
    }

    fn change(&mut self, _props: Self::Properties) -> bool {
        true
    }

    fn view(&self) -> Html {
        html! {
            <Page>
                {if self.profile.is_some() {
                    let profile = self.profile.as_ref().unwrap();
                    html! {
                        <div class="jinya-designer-my-profile">
                            {if !self.edit_mode {
                                html! {
                                    <Fab icon="pencil" on_click=self.link.callback(|_| Msg::OnEditProfile) />
                                }
                            } else {
                                html! {}
                            }}
                            <div class="jinya-designer-my-profile__left">
                                {if !self.edit_mode {
                                    html! {
                                        <>
                                            <img class="jinya-designer-my-profile__profile-picture" src={format!("{}{}", get_host(), profile.profile_picture)} />
                                            <span class="jinya-designer-my-profile__artist-name">{&profile.artist_name}</span>
                                            <span class="jinya-designer-my-profile__email">{&profile.email}</span>
                                        </>
                                    }
                                } else {
                                    html! {
                                        <>
                                            <FileUpload on_select=self.link.callback(|files| Msg::OnFileSelect(files)) label=self.translator.translate("my_jinya.my_account.my_profile.profile_picture") placeholder=self.translator.translate("my_jinya.my_account.my_profile.profile_picture") />
                                            <Input validation_message=&self.artist_name_validation_message state=&self.artist_name_state on_input=self.link.callback(|value| Msg::OnArtistNameInput(value)) input_type="text" value=&self.artist_name label=self.translator.translate("my_jinya.my_account.my_profile.artist_name") />
                                            <Input validation_message=&self.email_validation_message state=&self.email_state on_input=self.link.callback(|value| Msg::OnEmailInput(value)) input_type="email" value=&self.email label=self.translator.translate("my_jinya.my_account.my_profile.email") />
                                        </>
                                    }
                                }}
                            </div>
                            <div class="jinya-designer-my-profile__right">
                                {if !self.edit_mode {
                                    let about_me = profile.about_me.as_ref().unwrap();
                                    let content = {
                                        let div = web_sys::window()
                                            .unwrap()
                                            .document()
                                            .unwrap()
                                            .create_element("div")
                                            .unwrap();
                                        div.set_inner_html(about_me);
                                        div
                                    };
                                    let node = Node::from(content);
                                    let vnode = VNode::VRef(node);
                                    html! {
                                        {vnode}
                                    }
                                } else {
                                    html! {
                                        <>
                                            <div id="about-me-content"></div>
                                            <ButtonRow>
                                                <Button button_type=ButtonType::Secondary label=self.translator.translate("my_jinya.my_account.my_profile.discard") on_click=self.link.callback(|_| Msg::OnDiscardClick)/>
                                                <Button label=self.translator.translate("my_jinya.my_account.my_profile.save") on_click=self.link.callback(|_| Msg::OnSaveClick)/>
                                            </ButtonRow>
                                        </>
                                    }
                                }}
                            </div>
                        </div>
                    }
                } else {
                    html! {}
                }}
            </Page>
        }
    }

    fn rendered(&mut self, first_render: bool) {
        if first_render {
            self.menu_dispatcher.send(MenuAgentRequest::HideSearch);
            self.load_profile_task = Some(self.profile_service.get_profile(self.link.callback(|result| Msg::OnProfileLoaded(result))));
        }
        if self.edit_mode {
            if !self.tinymce_initialized {
                self.tinymce.init_tiny_mce(self.profile.as_ref().unwrap().about_me.as_ref().unwrap().to_string());
                self.tinymce_initialized = true;
            }
        } else if self.tinymce_initialized {
            self.tinymce.destroy_editor();
            self.tinymce_initialized = false;
        }
    }

    fn destroy(&mut self) {
        self.tinymce.destroy_editor();
    }
}

impl MyProfilePage {
    pub fn disable_edit_mode(&mut self) {
        if !self.picture_saving && !self.profile_saving {
            self.edit_mode = false;
            self.load_profile_task = Some(self.profile_service.get_profile(self.link.callback(|result| Msg::OnProfileLoaded(result))));
        }
    }
}