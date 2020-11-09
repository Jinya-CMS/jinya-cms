use jinya_ui::layout::button_row::ButtonRow;
use jinya_ui::layout::page::Page;
use jinya_ui::layout::row::Row;
use jinya_ui::widgets::button::{Button, ButtonType};
use jinya_ui::widgets::dialog::confirmation::{ConfirmationDialog, DialogType};
use jinya_ui::widgets::form::checkbox::Checkbox;
use jinya_ui::widgets::form::dropdown::{Dropdown, DropdownItem};
use jinya_ui::widgets::form::input::Input;
use jinya_ui::widgets::toast::Toast;
use yew::agent::Dispatcher;
use yew::prelude::*;
use yew::services::fetch::*;

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

#[derive(PartialEq, Clone, Properties)]
pub struct MenuDesignerPageProps {
    pub id: usize,
}

enum NewItemType {
    Gallery,
    Page,
    SegmentPage,
    Group,
    ExternalLink,
}

pub enum Msg {
    OnMenuItemsLoaded(Result<Vec<MenuItem>, AjaxError>),
    OnIncreaseNesting(MouseEvent, Option<MenuItem>, MenuItem),
    OnDecreaseNesting(MouseEvent, MenuItem),
    OnRequestComplete,
    OnMenuItemDeleteClicked(MouseEvent, MenuItem),
    OnDeleteApprove,
    OnDeleteDecline,
    OnDeleteRequestComplete(Result<bool, AjaxError>),
}

pub struct MenuDesignerPage {
    link: ComponentLink<Self>,
    id: usize,
    menu_items: Vec<MenuItem>,
    menu_item_service: MenuItemService,
    load_menu_items_task: Option<FetchTask>,
    translator: Translator,
    menu_dispatcher: Dispatcher<MenuAgent>,
    change_nesting_task: Option<FetchTask>,
    menu_item_to_delete: Option<MenuItem>,
    menu_item_delete_task: Option<FetchTask>,
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
            menu_item_service: MenuItemService::new(),
            load_menu_items_task: None,
            translator,
            menu_dispatcher,
            change_nesting_task: None,
            menu_item_to_delete: None,
            menu_item_delete_task: None,
        }
    }

    fn update(&mut self, msg: Self::Message) -> bool {
        match msg {
            Msg::OnMenuItemsLoaded(result) => {
                if let Ok(data) = result {
                    self.menu_items = data;
                } else {
                    Toast::negative_toast(self.translator.translate("menus.designer.error_load_menu_items"))
                }
            }
            Msg::OnIncreaseNesting(event, previous, item) => {
                event.prevent_default();
                self.change_parent(previous, item);
            }
            Msg::OnDecreaseNesting(event, item) => {
                event.prevent_default();
                self.move_one_level_up(item);
            }
            Msg::OnRequestComplete => self.load_menu_items_task = Some(self.menu_item_service.get_by_menu(self.id, self.link.callback(Msg::OnMenuItemsLoaded))),
            Msg::OnMenuItemDeleteClicked(event, item) => {
                event.prevent_default();
                self.menu_item_to_delete = Some(item)
            }
            Msg::OnDeleteApprove => self.menu_item_delete_task = Some(self.menu_item_service.delete_menu_item(self.menu_item_to_delete.as_ref().unwrap().id, self.link.callback(Msg::OnDeleteRequestComplete))),
            Msg::OnDeleteDecline => self.menu_item_to_delete = None,
            Msg::OnDeleteRequestComplete(result) => {
                if result.is_ok() {
                    self.menu_item_to_delete = None;
                    self.load_menu_items_task = Some(self.menu_item_service.get_by_menu(self.id, self.link.callback(Msg::OnMenuItemsLoaded)));
                } else {
                    self.menu_item_to_delete = None;
                    Toast::negative_toast(self.translator.translate("menus.designer.item.delete.failed"));
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
                                let previous = if idx > 0 {
                                    Some(self.menu_items[idx - 1].clone())
                                } else {
                                    None
                                };
                                html! {
                                    {self.get_item_view(item, None, idx == 0, previous, idx + 1 == item.items.len())}
                                }
                            })}
                        </ul>
                    </div>
                </div>
                {if self.menu_item_to_delete.is_some() {
                    let item = self.menu_item_to_delete.as_ref().unwrap();
                    html! {
                        <ConfirmationDialog
                            title=self.translator.translate("menus.designer.item.delete.title")
                            dialog_type=DialogType::Negative
                            message=self.translator.translate_with_args("menus.designer.item.delete.content", map! { "title" => item.title.as_str() })
                            decline_label=self.translator.translate("menus.designer.item.delete.decline")
                            approve_label=self.translator.translate("menus.designer.item.delete.approve")
                            on_approve=self.link.callback(|_| Msg::OnDeleteApprove)
                            on_decline=self.link.callback(|_| Msg::OnDeleteDecline)
                            is_open=self.menu_item_to_delete.is_some()
                        />
                    }
                } else {
                    html! {}
                }}
            </Page>
        }
    }

    fn rendered(&mut self, first_render: bool) {
        if first_render {
            self.menu_dispatcher.send(MenuAgentRequest::ChangeTitle(self.translator.translate("app.menu.content.pages.segment_pages")));
            self.menu_dispatcher.send(MenuAgentRequest::HideSearch);
            self.load_menu_items_task = Some(self.menu_item_service.get_by_menu(self.id, self.link.callback(Msg::OnMenuItemsLoaded)));
        }
    }
}

impl MenuDesignerPage {
    fn get_item_view(&self, item: &MenuItem, parent: Option<MenuItem>, first: bool, previous: Option<MenuItem>, last: bool) -> Html {
        let delete_item = item.clone();
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
                                let cloned_item = item.clone();
                                html! {
                                    <a onclick=self.link.callback(move |event| Msg::OnIncreaseNesting(event, previous.clone(), cloned_item.clone())) class="jinya-designer-menu-item__button jinya-designer-menu-item__button--primary mdi mdi-chevron-right"></a>
                                }
                            } else if item.items.is_empty() {
                                html! {
                                    <>
                                        {if parent.is_some() && last {
                                            let cloned_item = item.clone();
                                            html! {
                                                <a onclick=self.link.callback(move |event| Msg::OnDecreaseNesting(event, cloned_item.clone())) class="jinya-designer-menu-item__button jinya-designer-menu-item__button--primary mdi mdi-chevron-left"></a>
                                            }
                                        } else {
                                            html! {}
                                        }}
                                        {if previous.is_some() {
                                            let cloned_item = item.clone();
                                            html! {
                                                <a onclick=self.link.callback(move |event| Msg::OnIncreaseNesting(event, previous.clone(), cloned_item.clone())) class="jinya-designer-menu-item__button jinya-designer-menu-item__button--primary mdi mdi-chevron-right"></a>
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
                            <a onclick=self.link.callback(move |event| Msg::OnMenuItemDeleteClicked(event, delete_item.clone())) class="jinya-designer-menu-item__button jinya-designer-menu-item__button--negative mdi mdi-delete"></a>
                        </div>
                    </div>
                    <div class="jinya-designer-menu-item__drop-target">
                        <span class="mdi mdi-plus jinya-designer-menu-item__drop-target-icon"></span>
                    </div>
                    {if !item.items.is_empty() {
                        html! {
                            <ul class="jinya-designer-menu-designer__items">
                                {for item.items.iter().enumerate().map(|(idx, subitem)| {
                                    let previous = if idx > 0 {
                                        Some(item.items[idx - 1].clone())
                                    } else {
                                        None
                                    };
                                    html! {
                                        {self.get_item_view(subitem, Some(item.clone()), false, previous, idx + 1 == item.items.len())}
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

    fn change_parent(&mut self, new_parent: Option<MenuItem>, item: MenuItem) {
        if let Some(new_parent_item) = new_parent {
            self.change_nesting_task = Some(self.menu_item_service.change_menu_item_parent(new_parent_item.id, item, self.link.callback(|_| Msg::OnRequestComplete)));
        }
    }

    fn move_one_level_up(&mut self, item: MenuItem) {
        self.change_nesting_task = Some(self.menu_item_service.move_item_one_level_up(self.id, item, self.link.callback(|_| Msg::OnRequestComplete)));
    }
}