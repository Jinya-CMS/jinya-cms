use http::StatusCode;
use jinya_ui::layout::page::Page;
use jinya_ui::listing::card::{Card, CardButton, CardContainer};
use jinya_ui::widgets::button::ButtonType;
use jinya_ui::widgets::dialog::confirmation::{ConfirmationDialog, DialogType};
use jinya_ui::widgets::floating_action_button::Fab;
use jinya_ui::widgets::toast::Toast;
use yew::{Bridge, Bridged, Component, ComponentLink, Html};
use yew::agent::Dispatcher;
use yew::prelude::*;
use yew::services::fetch::FetchTask;
use yew_router::agent::RouteRequest;
use yew_router::prelude::RouteAgent;
use yew_router::route::Route;

use add_dialog::AddDialog;
use edit_dialog::EditDialog;

use crate::agents::menu_agent::{MenuAgent, MenuAgentRequest, MenuAgentResponse};
use crate::ajax::{AjaxError, get_host};
use crate::ajax::artist_service::ArtistService;
use crate::ajax::profile_service::ProfileService;
use crate::app::AppRoute;
use crate::i18n::*;
use crate::models::artist::Artist;
use crate::models::list_model::ListModel;

mod add_dialog;
mod edit_dialog;
pub mod profile;

pub struct ArtistsPage {
    link: ComponentLink<Self>,
    menu_agent: Box<dyn Bridge<MenuAgent>>,
    artists: Vec<Artist>,
    artist_service: ArtistService,
    load_artists_task: Option<FetchTask>,
    translator: Translator,
    artist_to_edit: Option<Artist>,
    artist_to_delete: Option<Artist>,
    artist_to_deactivate: Option<Artist>,
    artist_to_activate: Option<Artist>,
    delete_artist_task: Option<FetchTask>,
    activate_artist_task: Option<FetchTask>,
    deactivate_artist_task: Option<FetchTask>,
    add_artist_open: bool,
    keyword: String,
    profile_service: ProfileService,
    me: Option<Artist>,
    route_dispatcher: Dispatcher<RouteAgent>,
}

pub enum Msg {
    OnArtistsLoaded(Result<ListModel<Artist>, AjaxError>),
    OnProfileLoaded(Result<Artist, AjaxError>),
    OnMenuAgentResponse(MenuAgentResponse),
    OnCreateArtistClicked,
    OnDeleteArtistClicked(usize),
    OnEditArtistClicked(usize),
    OnDeactivateArtistClicked(usize),
    OnActivateArtistClicked(usize),
    OnDeleteApprove,
    OnDeleteDecline,
    OnActivateApprove,
    OnActivateDecline,
    OnDeactivateApprove,
    OnDeactivateDecline,
    OnArtistDeleted(Result<bool, AjaxError>),
    OnArtistActivated(Result<bool, AjaxError>),
    OnArtistDeactivated(Result<bool, AjaxError>),
    OnSaveEdit,
    OnDiscardEdit,
    OnSaveAdd,
    OnDiscardAdd,
    OnProfileClicked(usize),
}

impl Component for ArtistsPage {
    type Message = Msg;
    type Properties = ();

    fn create(_props: Self::Properties, link: ComponentLink<Self>) -> Self {
        let menu_agent = MenuAgent::bridge(link.callback(|response| Msg::OnMenuAgentResponse(response)));
        let route_dispatcher = RouteAgent::dispatcher();

        ArtistsPage {
            link,
            menu_agent,
            artists: vec![],
            artist_service: ArtistService::new(),
            load_artists_task: None,
            translator: Translator::new(),
            artist_to_edit: None,
            artist_to_delete: None,
            artist_to_deactivate: None,
            artist_to_activate: None,
            delete_artist_task: None,
            activate_artist_task: None,
            deactivate_artist_task: None,
            add_artist_open: false,
            keyword: "".to_string(),
            profile_service: ProfileService::new(),
            me: None,
            route_dispatcher,
        }
    }

    fn update(&mut self, msg: Self::Message) -> bool {
        match msg {
            Msg::OnArtistsLoaded(list) => {
                if list.is_ok() {
                    self.artists = list.unwrap().items
                } else {
                    log::error!("{}", list.err().unwrap().error.to_string().as_str());
                }
            }
            Msg::OnMenuAgentResponse(response) => match response {
                MenuAgentResponse::OnSearch(value) => {
                    self.keyword = value;
                    self.load_artists_task = Some(self.artist_service.get_list(self.keyword.to_string(), self.link.callback(|response| Msg::OnArtistsLoaded(response))));
                }
                _ => {}
            },
            Msg::OnCreateArtistClicked => self.add_artist_open = true,
            Msg::OnDeleteArtistClicked(idx) => self.artist_to_delete = Some(self.artists[idx].clone()),
            Msg::OnEditArtistClicked(idx) => self.artist_to_edit = Some(self.artists[idx].clone()),
            Msg::OnDeleteApprove => self.delete_artist_task = Some(self.artist_service.delete_artist(self.artist_to_delete.as_ref().unwrap().id, self.link.callback(|result| Msg::OnArtistDeleted(result)))),
            Msg::OnDeleteDecline => self.artist_to_delete = None,
            Msg::OnActivateApprove => self.activate_artist_task = Some(self.artist_service.activate_artist(self.artist_to_activate.as_ref().unwrap().id, self.link.callback(|result| Msg::OnArtistActivated(result)))),
            Msg::OnActivateDecline => self.artist_to_activate = None,
            Msg::OnDeactivateApprove => self.deactivate_artist_task = Some(self.artist_service.deactivate_artist(self.artist_to_deactivate.as_ref().unwrap().id, self.link.callback(|result| Msg::OnArtistDeactivated(result)))),
            Msg::OnDeactivateDecline => self.artist_to_deactivate = None,
            Msg::OnArtistDeleted(result) => {
                if result.is_ok() {
                    self.load_artists_task = Some(self.artist_service.get_list(self.keyword.to_string(), self.link.callback(|response| Msg::OnArtistsLoaded(response))));
                    self.artist_to_delete = None;
                } else {
                    if result.err().unwrap().status_code == StatusCode::CONFLICT {
                        Toast::negative_toast(self.translator.translate_with_args("artists.delete.failed_created_content", map! {"artist_name" => self.artist_to_delete.as_ref().unwrap().artist_name.as_str()}));
                    } else {
                        Toast::negative_toast(self.translator.translate_with_args("artists.delete.failed", map! {"artist_name" => self.artist_to_delete.as_ref().unwrap().artist_name.as_str()}));
                    }
                    self.artist_to_delete = None;
                }
            }
            Msg::OnSaveEdit => {
                self.load_artists_task = Some(self.artist_service.get_list(self.keyword.to_string(), self.link.callback(|response| Msg::OnArtistsLoaded(response))));
                self.artist_to_edit = None;
            }
            Msg::OnDiscardEdit => self.artist_to_edit = None,
            Msg::OnSaveAdd => {
                self.load_artists_task = Some(self.artist_service.get_list(self.keyword.to_string(), self.link.callback(|response| Msg::OnArtistsLoaded(response))));
                self.add_artist_open = false
            }
            Msg::OnDiscardAdd => self.add_artist_open = false,
            Msg::OnProfileLoaded(result) => {
                if result.is_ok() {
                    self.me = Some(result.unwrap());
                    self.load_artists_task = Some(self.artist_service.get_list("".to_string(), self.link.callback(|response| Msg::OnArtistsLoaded(response))));
                }
            }
            Msg::OnDeactivateArtistClicked(idx) => self.artist_to_deactivate = Some(self.artists[idx].clone()),
            Msg::OnActivateArtistClicked(idx) => self.artist_to_activate = Some(self.artists[idx].clone()),
            Msg::OnArtistActivated(result) => {
                if result.is_ok() {
                    Toast::positive_toast(self.translator.translate_with_args("artists.activate.success", map! {"artist_name" => self.artist_to_activate.as_ref().unwrap().artist_name.as_str()}));
                    self.load_artists_task = Some(self.artist_service.get_list(self.keyword.to_string(), self.link.callback(|response| Msg::OnArtistsLoaded(response))));
                    self.artist_to_activate = None;
                } else {
                    Toast::negative_toast(self.translator.translate_with_args("artists.activate.failed", map! {"artist_name" => self.artist_to_activate.as_ref().unwrap().artist_name.as_str()}));
                    self.artist_to_activate = None;
                }
            }
            Msg::OnArtistDeactivated(result) => {
                if result.is_ok() {
                    Toast::positive_toast(self.translator.translate_with_args("artists.deactivate.success", map! {"artist_name" => self.artist_to_deactivate.as_ref().unwrap().artist_name.as_str()}));
                    self.load_artists_task = Some(self.artist_service.get_list(self.keyword.to_string(), self.link.callback(|response| Msg::OnArtistsLoaded(response))));
                    self.artist_to_deactivate = None;
                } else {
                    Toast::negative_toast(self.translator.translate_with_args("artists.deactivate.failed", map! {"artist_name" => self.artist_to_deactivate.as_ref().unwrap().artist_name.as_str()}));
                    self.artist_to_deactivate = None;
                }
            }
            Msg::OnProfileClicked(idx) => {
                self.route_dispatcher.send(RouteRequest::ChangeRoute(Route::from(AppRoute::ArtistProfile(self.artists[idx].id))))
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
                    <div style="display: flex; align-items: flex-start;">
                        <CardContainer>
                            {for self.artists.iter().enumerate().map(move |(idx, item)| {
                                html! {
                                    <Card title=&item.artist_name src=format!("{}{}", get_host(), &item.profile_picture)>
                                        <CardButton button_type=ButtonType::Information icon="card-account-details" tooltip=self.translator.translate("artists.card.button.profile") on_click=self.link.callback(move |_| Msg::OnProfileClicked(idx)) />
                                        {if self.me.as_ref().unwrap().id != item.id {
                                            html! {
                                                <>
                                                    <CardButton button_type=ButtonType::Secondary icon="account-edit" tooltip=self.translator.translate("artists.card.button.edit") on_click=self.link.callback(move |_| Msg::OnEditArtistClicked(idx)) />
                                                    {if !item.enabled {
                                                        html! {
                                                            <CardButton button_type=ButtonType::Positive icon="account-check" tooltip=self.translator.translate("artists.card.button.activate") on_click=self.link.callback(move |_| Msg::OnActivateArtistClicked(idx)) />
                                                        }
                                                    } else {
                                                        html! {
                                                            <CardButton button_type=ButtonType::Negative icon="account-off" tooltip=self.translator.translate("artists.card.button.deactivate") on_click=self.link.callback(move |_| Msg::OnDeactivateArtistClicked(idx)) />
                                                        }
                                                    }}
                                                    <CardButton button_type=ButtonType::Negative icon="account-remove" tooltip=self.translator.translate("artists.card.button.delete") on_click=self.link.callback(move |_| Msg::OnDeleteArtistClicked(idx)) />
                                                </>
                                            }
                                        } else {
                                            html! {}
                                        }}
                                    </Card>
                                }
                            })}
                        </CardContainer>
                    </div>
                </div>
                {if self.artist_to_delete.is_some() {
                    let artist = self.artist_to_delete.as_ref().unwrap();
                    html! {
                        <ConfirmationDialog
                            title=self.translator.translate("artists.delete.title")
                            dialog_type=DialogType::Negative
                            message=self.translator.translate_with_args("artists.delete.content", map!{"artist_name" => artist.artist_name.as_str()})
                            decline_label=self.translator.translate("artists.delete.decline")
                            approve_label=self.translator.translate("artists.delete.approve")
                            on_approve=self.link.callback(|_| Msg::OnDeleteApprove)
                            on_decline=self.link.callback(|_| Msg::OnDeleteDecline)
                            is_open=self.artist_to_delete.is_some()
                        />
                    }
                } else {
                    html! {}
                }}
                {if self.artist_to_activate.is_some() {
                    let artist = self.artist_to_activate.as_ref().unwrap();
                    html! {
                        <ConfirmationDialog
                            title=self.translator.translate("artists.activate.title")
                            dialog_type=DialogType::Primary
                            message=self.translator.translate_with_args("artists.activate.content", map!{"artist_name" => artist.artist_name.as_str()})
                            decline_label=self.translator.translate("artists.activate.decline")
                            approve_label=self.translator.translate("artists.activate.approve")
                            on_approve=self.link.callback(|_| Msg::OnActivateApprove)
                            on_decline=self.link.callback(|_| Msg::OnActivateDecline)
                            is_open=self.artist_to_activate.is_some()
                        />
                    }
                } else {
                    html! {}
                }}
                {if self.artist_to_deactivate.is_some() {
                    let artist = self.artist_to_deactivate.as_ref().unwrap();
                    html! {
                        <ConfirmationDialog
                            title=self.translator.translate("artists.deactivate.title")
                            dialog_type=DialogType::Negative
                            message=self.translator.translate_with_args("artists.deactivate.content", map!{"artist_name" => artist.artist_name.as_str()})
                            decline_label=self.translator.translate("artists.deactivate.decline")
                            approve_label=self.translator.translate("artists.deactivate.approve")
                            on_approve=self.link.callback(|_| Msg::OnDeactivateApprove)
                            on_decline=self.link.callback(|_| Msg::OnDeactivateDecline)
                            is_open=self.artist_to_deactivate.is_some()
                        />
                    }
                } else {
                    html! {}
                }}
                {if self.artist_to_edit.is_some() {
                    let artist = self.artist_to_edit.as_ref().unwrap();
                    html! {
                        <EditDialog artist=artist on_save_changes=self.link.callback(|_ | Msg::OnSaveEdit) on_discard_changes=self.link.callback(|_| Msg::OnDiscardEdit) />
                    }
                } else {
                    html! {}
                }}
                {if self.add_artist_open {
                    html! {
                        <AddDialog is_open=self.add_artist_open on_save_changes=self.link.callback(|_| Msg::OnSaveAdd) on_discard_changes=self.link.callback(|_| Msg::OnDiscardAdd) />
                    }
                } else {
                    html! {}
                }}
                <Fab icon="account-plus" on_click=self.link.callback(|_| Msg::OnCreateArtistClicked) />
            </Page>
        }
    }

    fn rendered(&mut self, first_render: bool) {
        if first_render {
            self.menu_agent.send(MenuAgentRequest::ChangeTitle(self.translator.translate("app.menu.configuration.generic.artists")));
            self.load_artists_task = Some(self.profile_service.get_profile(self.link.callback(|result| Msg::OnProfileLoaded(result))));
        }
    }
}