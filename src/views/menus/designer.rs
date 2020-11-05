use jinya_ui::layout::button_row::ButtonRow;
use jinya_ui::layout::page::Page;
use jinya_ui::layout::row::Row;
use jinya_ui::widgets::button::{Button, ButtonType};
use jinya_ui::widgets::dialog::confirmation::{ConfirmationDialog, DialogType};
use jinya_ui::widgets::form::checkbox::Checkbox;
use jinya_ui::widgets::form::dropdown::{Dropdown, DropdownItem};
use jinya_ui::widgets::form::input::Input;
use jinya_ui::widgets::toast::Toast;
use web_sys::Node;
use yew::agent::Dispatcher;
use yew::prelude::*;
use yew::services::fetch::*;
use yew::virtual_dom::VNode;
use yew_router::agent::RouteAgent;

use crate::agents::menu_agent::{MenuAgent, MenuAgentRequest};
use crate::ajax::{AjaxError, get_host};
use crate::ajax::file_service::FileService;
use crate::ajax::gallery_service::GalleryService;
use crate::ajax::menu_item_service::MenuItemService;
use crate::i18n::Translator;
use crate::models::file::File;
use crate::models::gallery::Gallery;
use crate::models::list_model::ListModel;
use crate::models::menu_item::MenuItem;
use crate::models::segment::Segment;
use crate::utils::TinyMce;

#[derive(PartialEq, Clone, Properties)]
pub struct MenuDesignerPageProps {
    pub id: usize,
}

enum NewSegmentType {
    Gallery,
    File,
    Html,
}

pub enum Msg {
    OnMenuItemsLoaded(Result<Vec<MenuItem>, AjaxError>),
}

pub struct MenuDesignerPage {
    link: ComponentLink<Self>,
    id: usize,
    menu_items: Vec<MenuItem>,
    menu_items_service: MenuItemService,
    load_menu_items_task: Option<FetchTask>,
    translator: Translator,
    menu_dispatcher: Dispatcher<MenuAgent>,
}

impl Component for MenuDesignerPage {
    type Message = Msg;
    type Properties = MenuDesignerPageProps;

    fn create(props: Self::Properties, link: ComponentLink<Self>) -> Self {
        let menu_dispatcher = MenuAgent::dispatcher();
        let translator = Translator::new();

        MenuDesignerPage {
            link,
            id: props.id,
            menu_items: vec![],
            menu_items_service: MenuItemService::new(),
            load_menu_items_task: None,
            translator,
            menu_dispatcher,
        }
    }

    fn update(&mut self, msg: Self::Message) -> bool {
        match msg {
            Msg::OnMenuItemsLoaded(result) => {
                if let Ok(data) = result {
                    self.menu_items = data;
                } else {
                    Toast::negative_toast(self.translator.translate("segment_pages.designer.error_load_menu-items"))
                }
            }
        }

        true
    }

    fn change(&mut self, props: Self::Properties) -> bool {
        self.id = props.id;

        true
    }

    fn view(&self) -> Html {
        html! {
            <Page>
                <div class="jinya-designer-menu-designer__container">
                    <div class="jinya-designer-menu-designer__list jinya-designer-menu-designer__list--new-items">
                        <div draggable=true class="jinya-designer-menu-item__list-item">
                            <span class="mdi mdi-drag-horizontal-variant mdi-24px"></span> {self.translator.translate("menus.designer.gallery")}
                        </div>
                        <div draggable=true class="jinya-designer-menu-item__list-item">
                            <span class="mdi mdi-drag-horizontal-variant mdi-24px"></span> {self.translator.translate("menus.designer.page")}
                        </div>
                        <div draggable=true class="jinya-designer-menu-item__list-item">
                            <span class="mdi mdi-drag-horizontal-variant mdi-24px"></span> {self.translator.translate("menus.designer.segment_page")}
                        </div>
                        <div draggable=true class="jinya-designer-menu-item__list-item">
                            <span class="mdi mdi-drag-horizontal-variant mdi-24px"></span> {self.translator.translate("menus.designer.profile")}
                        </div>
                        <div draggable=true class="jinya-designer-menu-item__list-item">
                            <span class="mdi mdi-drag-horizontal-variant mdi-24px"></span> {self.translator.translate("menus.designer.link")}
                        </div>
                        <div draggable=true class="jinya-designer-menu-item__list-item">
                            <span class="mdi mdi-drag-horizontal-variant mdi-24px"></span> {self.translator.translate("menus.designer.group")}
                        </div>
                    </div>
                    <div class="jinya-designer-menu-designer__list jinya-designer-menu-designer__list--menu-items">
                        <div class="jinya-designer-menu-item__drop-target">
                            <span class="mdi mdi-plus jinya-designer-menu-item__drop-target-icon"></span>
                        </div>
                        <ul class="jinya-designer-menu-designer__items jinya-designer-menu-designer__items--top">
                            {for self.menu_items.iter().enumerate().map(|(idx, item)| {
                                html! {
                                    {self.get_item_view(item, None, idx == 0, idx > 0)}
                                }
                            })}
                        </ul>
                    </div>
                </div>
            </Page>
        }
    }

    fn rendered(&mut self, first_render: bool) {
        if first_render {
            self.menu_dispatcher.send(MenuAgentRequest::ChangeTitle(self.translator.translate("app.menu.content.pages.segment_pages")));
            self.menu_dispatcher.send(MenuAgentRequest::HideSearch);
            self.load_menu_items_task = Some(self.menu_items_service.get_by_menu(self.id, self.link.callback(Msg::OnMenuItemsLoaded)));
        }
    }
}

impl MenuDesignerPage {
    fn get_item_view(&self, item: &MenuItem, parent: Option<MenuItem>, first: bool, has_previous: bool) -> Html {
        html! {
            <>
                <li>
                    <div class="jinya-designer-menu-item__list-item" draggable=true>
                        <div style="display: flex">
                            <span class="mdi mdi-drag-horizontal-variant mdi-24px"></span>
                            <span>
                                {&item.title} {if item.highlighted {
                                    " (hightlighted) "
                                } else {
                                    " "
                                }}
                            </span>
                            {if item.route.is_some() {
                                html! {
                                    <span class="jinya-designer-menu-item__link">{"/"}{&item.route.as_ref().unwrap()}</span>
                                }
                            } else {
                                html! {}
                            }}
                        </div>
                        <div class="jinya-designer-menu-item__button-row">
                            {if first {
                                html! {}
                            } else if parent.is_none() {
                                html! {
                                    <a class="jinya-designer-menu-item__button jinya-designer-menu-item__button--primary mdi mdi-chevron-right"></a>
                                }
                            } else if item.items.is_empty() {
                                html! {
                                    <>
                                        {if parent.is_some() {
                                            html! {
                                                <>
                                                    <a class="jinya-designer-menu-item__button jinya-designer-menu-item__button--primary mdi mdi-chevron-left"></a>
                                                </>
                                            }
                                        } else {
                                            html! {}
                                        }}
                                        {if has_previous {
                                            html! {
                                                <>
                                                    <a class="jinya-designer-menu-item__button jinya-designer-menu-item__button--primary mdi mdi-chevron-right"></a>
                                                </>
                                            }
                                        } else {
                                            html! {}
                                        }}
                                    </>
                                }
                            } else {
                                html! {}
                            }}
                            <a class="jinya-designer-menu-item__button jinya-designer-menu-item__button--primary mdi mdi-pencil"></a>
                            <a class="jinya-designer-menu-item__button jinya-designer-menu-item__button--negative mdi mdi-delete"></a>
                        </div>
                    </div>
                    <div class="jinya-designer-menu-item__drop-target">
                        <span class="mdi mdi-plus jinya-designer-menu-item__drop-target-icon"></span>
                    </div>
                    {if !item.items.is_empty() {
                        html! {
                            <ul class="jinya-designer-menu-designer__items">
                                {for item.items.iter().enumerate().map(|(idx, subitem)| {
                                    html! {
                                        {self.get_item_view(subitem, Some(item.clone()), false, idx > 0)}
                                    }
                                })}
                            </ul>
                        }
                    } else {
                        html! {}
                    }}
                </li>
            </>
        }
    }

    fn get_last_position(&self) -> usize {
        let mut items = self.menu_items.clone();
        items.sort_by(|a, b| a.position.cmp(&b.position));

        if items.last().is_some() {
            items.last().unwrap().position + 1
        } else {
            1
        }
    }
}