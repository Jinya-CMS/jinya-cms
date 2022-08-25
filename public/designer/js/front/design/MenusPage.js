import html from '../../../lib/jinya-html.js';
import Sortable from '../../../lib/sortable.js';
import clearChildren from '../../foundation/html/clearChildren.js';
import { get, httpDelete, put } from '../../foundation/http/request.js';
import JinyaDesignerPage from '../../foundation/JinyaDesignerPage.js';
import localize from '../../foundation/localize.js';
import alert from '../../foundation/ui/alert.js';
import confirm from '../../foundation/ui/confirm.js';

export default class MenusPage extends JinyaDesignerPage {
  constructor({ layout }) {
    super({ layout });
    this.menus = [];
    this.selectedMenu = null;
    this.selectedItem = null;
    this.items = [];
    this.resultSortable = null;
    this.toolboxSortable = null;
  }

  // eslint-disable-next-line class-methods-use-this
  toString() {
    return html`
        <div class="cosmo-list">
            <nav class="cosmo-list__items" id="menu-list">
            </nav>
            <div class="cosmo-list__content jinya-designer">
                <div class="jinya-designer__title">
                    <span class="cosmo-title" id="menu-title"></span>
                </div>
                <div class="cosmo-toolbar cosmo-toolbar--designer">
                    <div class="cosmo-toolbar__group">
                        <button id="edit-menu" class="cosmo-button">
                            ${localize({ key: 'design.menus.action.edit' })}
                        </button>
                        <button id="delete-menu" class="cosmo-button">
                            ${localize({ key: 'design.menus.action.delete' })}
                        </button>
                    </div>
                    <div class="cosmo-toolbar__group">
                        <button disabled id="decrease-nesting" class="cosmo-button">
                            ${localize({ key: 'design.menus.action.decrease_nesting' })}
                        </button>
                        <button disabled id="increase-nesting" class="cosmo-button">
                            ${localize({ key: 'design.menus.action.increase_nesting' })}
                        </button>
                        <button disabled id="edit-item" class="cosmo-button">
                            ${localize({ key: 'design.menus.action.edit_item' })}
                        </button>
                        <button disabled id="delete-item" class="cosmo-button">
                            ${localize({ key: 'design.menus.action.delete_item' })}
                        </button>
                    </div>
                </div>
                <div class="jinya-designer__content">
                    <div id="item-list" class="jinya-designer__result jinya-designer__result--horizontal">
                    </div>
                    <div id="item-toolbox" class="jinya-designer__toolbox">
                        <div data-type="gallery" class="jinya-designer-item__template">
                            <span class="jinya-designer__drag-handle"></span>
                            <span>${localize({ key: 'design.menus.designer.type_gallery' })}</span>
                        </div>
                        <div data-type="page" class="jinya-designer-item__template">
                            <span class="jinya-designer__drag-handle"></span>
                            <span>${localize({ key: 'design.menus.designer.type_page' })}</span>
                        </div>
                        <div data-type="segment_page" class="jinya-designer-item__template">
                            <span class="jinya-designer__drag-handle"></span>
                            <span>${localize({ key: 'design.menus.designer.type_segment_page' })}</span>
                        </div>
                        <div data-type="form" class="jinya-designer-item__template">
                            <span class="jinya-designer__drag-handle"></span>
                            <span>${localize({ key: 'design.menus.designer.type_form' })}</span>
                        </div>
                        <div data-type="artist" class="jinya-designer-item__template">
                            <span class="jinya-designer__drag-handle"></span>
                            <span>${localize({ key: 'design.menus.designer.type_artist' })}</span>
                        </div>
                        <div data-type="blog_category" class="jinya-designer-item__template">
                            <span class="jinya-designer__drag-handle"></span>
                            <span>${localize({ key: 'design.menus.designer.type_blog_category' })}</span>
                        </div>
                        <div data-type="group" class="jinya-designer-item__template">
                            <span class="jinya-designer__drag-handle"></span>
                            <span>${localize({ key: 'design.menus.designer.type_group' })}</span>
                        </div>
                        <div data-type="external_link" class="jinya-designer-item__template">
                            <span class="jinya-designer__drag-handle"></span>
                            <span>${localize({ key: 'design.menus.designer.type_external_link' })}</span>
                        </div>
                        <div data-type="blog_home_page" class="jinya-designer-item__template">
                            <span class="jinya-designer__drag-handle"></span>
                            <span>${localize({ key: 'design.menus.designer.type_blog_home_page' })}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    `;
  }

  selectItem({ id }) {
    document
      .querySelectorAll('.jinya-designer-item--selected')
      .forEach((item) => item.classList.remove('jinya-designer-item--selected'));

    const item = document.querySelector(`[data-item-id="${id}"]`);
    item.classList.add('jinya-designer-item--selected');

    document.getElementById('edit-item').removeAttribute('disabled');
    document.getElementById('delete-item').removeAttribute('disabled');

    const decreaseAllowed = item.menuItem && item.menuItem.nestingIndex !== 0;
    let increaseAllowed = false;
    if (item.menuItem) {
      const current = this.items.indexOf(item.menuItem);
      if (current !== 0) {
        const previous = this.items[current - 1];

        if (item.menuItem?.items[0]?.position !== item.menuItem?.position) {
          increaseAllowed = !(item.menuItem?.parent && item.menuItem?.parent === previous);
        }
      }
    }

    document.getElementById('increase-nesting').disabled = !increaseAllowed;
    document.getElementById('decrease-nesting').disabled = !decreaseAllowed;

    this.selectedItem = item.menuItem;
    this.selectedItem.parent = item.parentItem;
  }

  selectMenu({ id }) {
    document.getElementById('edit-menu').disabled = false;
    document.getElementById('delete-menu').disabled = false;
    this.selectedMenu = this.menus.find((p) => p.id === parseInt(id, 10));
    document.querySelectorAll('.cosmo-list__item--active')
      .forEach((item) => item.classList.remove('cosmo-list__item--active'));
    document.querySelector(`[data-id="${id}"]`).classList.add('cosmo-list__item--active');
    document.getElementById('edit-item').setAttribute('disabled', 'disabled');
    document.getElementById('delete-item').setAttribute('disabled', 'disabled');
  }

  displayMenus() {
    document.getElementById('edit-menu').disabled = true;
    document.getElementById('delete-menu').disabled = true;
    let list = '';
    for (const menu of this.menus) {
      list += `<a class="cosmo-list__item" data-id="${menu.id}">${menu.name}</a>`;
    }
    clearChildren({ parent: document.getElementById('menu-list') });
    document.getElementById('menu-list').innerHTML = `${list}
                <button id="new-menu-button" class="cosmo-button cosmo-button--full-width">
                    ${localize({ key: 'design.menus.action.new' })}
                </button>`;
    document.querySelectorAll('.cosmo-list__item').forEach((item) => {
      item.addEventListener('click', async () => {
        this.selectMenu({ id: item.getAttribute('data-id') });
        await this.displaySelectedMenu();
      });
    });
    document.getElementById('new-menu-button').addEventListener('click', async () => {
      const { default: AddMenuDialog } = await import('./menus/AddMenuDialog.js');
      const dialog = new AddMenuDialog({
        onHide: async (menu) => {
          this.menus.push(menu);
          this.displayMenus();
          this.selectMenu({ id: menu.id });
          await this.displaySelectedMenu();
        },
      });
      await dialog.show();
    });
  }

  displayItems() {
    if (this.resultSortable) {
      this.resultSortable.destroy();
    }
    document.getElementById('menu-title').innerText = `#${this.selectedMenu.id} ${this.selectedMenu.name}`;
    const itemList = document.getElementById('item-list');
    clearChildren({ parent: itemList });
    this.displayMenuItems({ items: this.items, itemList });
    this.resultSortable = new Sortable(document.getElementById('item-list'), {
      group: { name: 'menu_items', put: true, pull: false },
      sort: true,
      onAdd: async (e) => {
        const dropIdx = e.newIndex;
        let position = -1;
        let createMenuItemParent = null;
        if (this.items.length === 0) {
          position = 0;
          createMenuItemParent = null;
        } else if (this.items.length === dropIdx) {
          position = this.items[this.items.length - 1].position + 2;
          if (this.items[this.items.length - 1].parent) {
            createMenuItemParent = this.items[this.items.length - 1].parent.id;
          } else {
            createMenuItemParent = null;
          }
        } else {
          position = this.items[dropIdx].position;
          if (this.items[dropIdx].parent) {
            createMenuItemParent = this.items[dropIdx].parent.id;
          } else {
            createMenuItemParent = null;
          }
        }
        const { default: EditMenuItemDialog } = await import('./menus/EditMenuItemDialog.js');
        const type = e.item.getAttribute('data-type');
        const data = {
          newItem: true,
          type,
          position,
        };
        if (createMenuItemParent !== null && createMenuItemParent !== undefined) {
          data.parentId = createMenuItemParent;
        } else {
          data.menuId = this.selectedMenu.id;
        }

        // eslint-disable-next-line default-case
        switch (type) {
          case 'gallery':
            data.items = (await get('/api/media/gallery')).items;
            break;
          case 'page':
            data.items = (await get('/api/page')).items;
            break;
          case 'segment_page':
            data.items = (await get('/api/segment-page')).items;
            break;
          case 'form':
            data.items = (await get('/api/form')).items;
            break;
          case 'artist':
            data.items = (await get('/api/artist')).items;
            break;
          case 'blog_category':
            data.items = (await get('/api/blog/category')).items;
            break;
        }

        const dialog = new EditMenuItemDialog({
          ...data,
          onHide: async (item) => {
            await this.displaySelectedMenu();
            this.selectItem({ id: item.id });
          },
        });
        await dialog.show();
      },
      onUpdate: async (e) => {
        const dropIdx = e.newIndex > e.oldIndex ? e.newIndex + 1 : e.newIndex;
        const menuItemId = parseInt(e.item.getAttribute('data-item-id'), 10);
        const item = this.items[e.oldIndex];
        const targetItem = dropIdx === this.items.length ? this.items[e.newIndex] : this.items[dropIdx];
        const position = dropIdx === this.items.length ? this.items[e.newIndex].position + 1 : targetItem.position;
        const newParent = targetItem?.parent?.id;
        const dataParentId = item.parent?.id;
        let currentParent = this.items.find((i) => i.id === dataParentId);
        if (newParent) {
          let allowMove = true;
          while (currentParent) {
            if (currentParent?.id === menuItemId) {
              allowMove = false;
              break;
            }

            currentParent = currentParent.parent;
          }

          if (allowMove) {
            await put(`/api/menu-item/${menuItemId}/move/parent/to/item/${newParent}`);
            await put(`/api/menu-item/${menuItemId}`, {
              position,
            });
          }
        } else {
          await put(`/api/menu/${menuItemId}/move/parent/to/menu/${this.selectedMenu.id}`);
          await put(`/api/menu-item/${menuItemId}`, {
            position,
          });
        }
        await this.displaySelectedMenu();
        if (this.selectedItem) {
          this.selectItem({ id: this.selectedItem.id });
        }
      },
    });
  }

  displayMenuItems({ items, itemList, appendToParent = false }) {
    for (const item of items) {
      const type = this.getType(item);
      const itemElem = document.createElement('div');
      itemElem.classList.add('jinya-designer-item', 'jinya-designer-item--menu');
      itemElem.setAttribute('data-position', item.position.toString(10));
      itemElem.setAttribute('data-item-id', item.id.toString(10));
      itemElem.setAttribute('data-parent-id', item.parent?.id.toString(10) ?? '-1');
      itemElem.setAttribute('data-nesting-index', item.nestingIndex.toString(10));
      itemElem.style.marginLeft = `${item.nestingIndex * 16}px`;
      itemElem.style.width = `calc(100% - ${item.nestingIndex * 16}px)`;
      itemElem.menuItem = item;
      itemElem.parentItem = item.parent;
      itemElem.innerHTML = html`
          <span class="jinya-designer-item__title">
              ${localize({ key: `design.menus.designer.type_${type}` })}
          </span>
          <span>
              <span>${item.title}</span>
              <span class="jinya-menu-item__route">${item.route ?? ''}</span>
          </span>`;
      if (appendToParent) {
        itemList.parentElement.append(itemElem);
      } else {
        itemList.append(itemElem);
      }

      itemElem.addEventListener('click', () => {
        this.selectItem({ id: item.id });
      });
    }
  }

  // eslint-disable-next-line class-methods-use-this
  getType(item) {
    let type = 'group';
    if (item.artist) {
      type = 'artist';
    } else if (item.page) {
      type = 'page';
    } else if (item.segmentPage) {
      type = 'segment_page';
    } else if (item.form) {
      type = 'form';
    } else if (item.gallery) {
      type = 'gallery';
    } else if (item.category) {
      type = 'blog_category';
    } else if (item.blogHomePage) {
      type = 'blog_home_page';
    } else if (item.route) {
      type = 'external_link';
    }
    return type;
  }

  flattenMenuItems({ parent, nestingIndex, items }) {
    const result = [];
    for (const item of items) {
      item.parent = parent;
      item.nestingIndex = nestingIndex;
      item.type = this.getType(item);
      result.push(item);
      if (items.length > 0) {
        result.push(...this.flattenMenuItems({ parent: item, nestingIndex: nestingIndex + 1, items: item.items }));
      }
    }

    return result;
  }

  async displayed() {
    await super.displayed();
    const { items } = await get('/api/menu');
    this.menus = items;

    this.toolboxSortable = new Sortable(document.getElementById('item-toolbox'), {
      group: { name: 'menu_items', put: false, pull: 'clone' },
      sort: false,
      handle: '.jinya-designer__drag-handle',
      onEnd(e) {
        if (!e.to.classList.contains('jinya-designer__toolbox')) {
          e.item.remove();
        }
      },
    });

    this.displayMenus();
    if (this.menus.length > 0) {
      this.selectMenu({ id: this.menus[0].id });
      await this.displaySelectedMenu();
    }
  }

  bindEvents() {
    super.bindEvents();
    document.getElementById('edit-menu').addEventListener('click', async () => {
      const { default: EditMenuDialog } = await import('./menus/EditMenuDialog.js');
      const dialog = new EditMenuDialog({
        onHide: async (menu) => {
          const menuFromList = this.menus.find((m) => m.id === menu.id);
          menuFromList.name = menu.name;
          menuFromList.logo = menu.logo;
          this.selectedMenu.name = menu.name;
          this.selectedMenu.logo = menu.logo;

          this.displayMenus();
          this.selectMenu({ id: menu.id });
          await this.displaySelectedMenu();
        },
        id: this.selectedMenu.id,
        name: this.selectedMenu.name,
        logo: this.selectedMenu.logo?.id,
      });
      await dialog.show();
    });
    document.getElementById('delete-menu').addEventListener('click', async () => {
      const confirmation = await confirm({
        title: localize({ key: 'design.menus.delete.title' }),
        message: localize({ key: 'design.menus.delete.message', values: this.selectedMenu }),
        approveLabel: localize({ key: 'design.menus.delete.delete' }),
        declineLabel: localize({ key: 'design.menus.delete.keep' }),
      });
      if (confirmation) {
        try {
          await httpDelete(`/api/menu/${this.selectedMenu.id}`);
          this.menus = this.menus.filter((menu) => menu.id !== this.selectedMenu.id);
          this.displayMenus();
          if (this.menus.length > 0) {
            this.selectMenu({ id: this.menus[0].id });
            await this.displayItems();
          } else {
            this.selectedMenu = null;
            this.items = [];
            const itemList = document.getElementById('item-list');
            clearChildren({ parent: itemList });
            document.getElementById('menu-title').innerText = '';
          }
          document.getElementById('edit-item').setAttribute('disabled', 'disabled');
          document.getElementById('delete-item').setAttribute('disabled', 'disabled');
        } catch (e) {
          if (e.status === 409) {
            await alert({
              title: localize({ key: 'design.menus.delete.error.title' }),
              message: localize({ key: 'design.menus.delete.error.conflict' }),
            });
          } else {
            await alert({
              title: localize({ key: 'design.menus.delete.error.title' }),
              message: localize({ key: 'design.menus.delete.error.generic' }),
            });
          }
        }
      }
    });
    document.getElementById('edit-item').addEventListener('click', async () => {
      const { default: EditMenuItemDialog } = await import('./menus/EditMenuItemDialog.js');
      const type = this.getType(this.selectedItem);
      const data = {
        newItem: false,
        type,
        ...this.selectedItem,
        menuItemId: this.selectedItem.id,
      };
      // eslint-disable-next-line default-case
      switch (type) {
        case 'gallery':
          data.items = (await get('/api/media/gallery')).items;
          data.selectedItem = this.selectedItem.gallery.id;
          break;
        case 'page':
          data.items = (await get('/api/page')).items;
          data.selectedItem = this.selectedItem.page.id;
          break;
        case 'segment_page':
          data.items = (await get('/api/segment-page')).items;
          data.selectedItem = this.selectedItem.segmentPage.id;
          break;
        case 'form':
          data.items = (await get('/api/form')).items;
          data.selectedItem = this.selectedItem.form.id;
          break;
        case 'artist':
          data.items = (await get('/api/artist')).items;
          data.selectedItem = this.selectedItem.artist.id;
          break;
        case 'blog_category':
          data.items = (await get('/api/blog/category')).items;
          data.selectedItem = this.selectedItem.category.id;
          break;
      }

      const dialog = new EditMenuItemDialog({
        ...data,
        onHide: (item) => {
          for (const key of Object.keys(item)) {
            this.selectedItem[key] = item[key];
          }
          this.setMenuItem({ items: this.items, item });
          this.displayItems();
          this.selectItem({ id: item.id });
        },
      });
      await dialog.show();
    });
    document.getElementById('decrease-nesting').addEventListener('click', async () => {
      await put(`/api/menu/${this.selectedMenu.id}/item/${this.selectedItem.id}/move/parent/one/level/up`);
      await this.displaySelectedMenu();
      this.selectItem(this.selectedItem);
    });
    document.getElementById('increase-nesting').addEventListener('click', async () => {
      const previous = this.items[this.items.indexOf(this.selectedItem) - 1];
      if (previous.nestingIndex > 0 && this.selectedItem.nestingIndex === previous.nestingIndex) {
        await put(`/api/menu-item/${this.selectedItem.id}/move/parent/to/item/${previous.id}`);
      } else {
        await put(`/api/menu-item/${this.selectedItem.id}/move/parent/to/item/${previous.parent?.id ?? previous.id}`);
      }
      await this.displaySelectedMenu();
      this.selectItem(this.selectedItem);
    });
  }

  setMenuItem({ item, items }) {
    const result = items.find((f) => f.id === item.id);
    if (result) {
      for (const key of Object.keys(item)) {
        result[key] = item[key];
      }
      return;
    }

    // eslint-disable-next-line no-unused-vars
    for (const resultElement of result) {
      this.setMenuItem({ item, items: result.items });
    }
  }

  async displaySelectedMenu() {
    const items = await get(`/api/menu/${this.selectedMenu.id}/item`);
    this.items = this.flattenMenuItems({ parent: null, nestingIndex: 0, items });
    this.displayItems();
  }
}
