import html from '../../../../lib/jinya-html.js';
import { post, put } from '../../../foundation/http/request.js';
import localize from '../../../foundation/localize.js';
import alert from '../../../foundation/ui/alert.js';

export default class EditMenuItemDialog {
  /**
   * Shows the edit dialog
   * @param onHide {function({gallery: any, position: number, segment: any})}
   * @param menuItemId {number}
   * @param position {number}
   * @param items {any[]}
   * @param selectedItem {any}
   * @param type {string}
   * @param route {string}
   * @param highlighted {boolean}
   * @param title {string}
   * @param parentId {string}
   * @param menuId {string}
   * @param newItem {boolean}
   */
  constructor({
                onHide,
                id: menuItemId,
                position,
                items,
                selectedItem,
                type,
                route,
                title,
                highlighted,
                parentId = null,
                menuId = null,
                newItem = false,
              }) {
    this.onHide = onHide;
    this.position = position;
    this.newItem = newItem;
    this.menuItemId = menuItemId;
    this.items = items;
    this.selectedItem = selectedItem;
    this.type = type;
    this.route = route;
    this.title = title;
    this.highlighted = highlighted;
    this.parentId = parentId;
    this.menuId = menuId;
  }

  async show() {
    const content = html`
        <div class="cosmo-modal__backdrop"></div>
        <form class="cosmo-modal__container" id="edit-dialog-form">
            <div class="cosmo-modal">
                <h1 class="cosmo-modal__title">${localize({ key: 'pages_and_forms.segment.designer.edit.title' })}</h1>
                <div class="cosmo-modal__content">
                    <div class="cosmo-input__group">
                        <label for="editMenuItemTitle" class="cosmo-label">
                            ${localize({ key: 'design.menus.designer.edit.item_title' })}
                        </label>
                        <input value="${this.title ?? ''}" required type="text" id="editMenuItemTitle"
                               class="cosmo-input">
                        ${this.type === 'group' || this.type === 'blog_home_page' ? '' : html`
                            <label for="editMenuItemRoute" class="cosmo-label">
                                ${localize({ key: 'design.menus.designer.edit.route' })}
                            </label>
                            <input value="${this.route ?? ''}" type="text" id="editMenuItemRoute" class="cosmo-input">`}
                        ${this.type !== 'group' && this.type !== 'blog_home_page' && this.type !== 'external_link' ? html`
                            <label for="editMenuElement" class="cosmo-label">
                                ${localize({ key: `design.menus.designer.type_${this.type}` })}
                            </label>
                            <select required id="editMenuElement" class="cosmo-select">
                                ${this.items.map((item) => html`
                                    <option ${this.selectedItem === item.id ? 'selected' : ''} value="${item.id}">
                                            #${item.id} ${item.name ?? item.title ?? item.artistName}
                                    </option>`)}
                            </select>` : ''}
                        <div class="cosmo-checkbox__group">
                            <input ${this.highlighted ? 'checked' : ''} type="checkbox" id="editMenuItemIsHighlighted"
                                   class="cosmo-checkbox">
                            <label for="editMenuItemIsHighlighted">
                                ${localize({ key: 'design.menus.designer.edit.is_highlighted' })}
                            </label>
                        </div>
                    </div>
                    <div class="cosmo-modal__button-bar">
                        <button type="button" class="cosmo-button" id="cancel-edit-dialog">
                            ${localize({ key: 'pages_and_forms.segment.designer.edit.cancel' })}
                        </button>
                        <button type="submit" class="cosmo-button" id="save-edit-dialog">
                            ${localize({ key: 'pages_and_forms.segment.designer.edit.update' })}
                        </button>
                    </div>
                </div>
        </form>`;
    const container = document.createElement('div');
    container.innerHTML = content;
    document.body.append(container);
    document.getElementById('cancel-edit-dialog').addEventListener('click', () => {
      container.remove();
    });
    document.getElementById('edit-dialog-form').addEventListener('submit', async (e) => {
      e.preventDefault();
      const title = document.getElementById('editMenuItemTitle').value;
      const route = document.getElementById('editMenuItemRoute')?.value;
      const selectedItem = parseInt(document.getElementById('editMenuElement')?.value ?? '-1', 10);
      const highlighted = document.getElementById('editMenuItemIsHighlighted').checked;
      const data = {
        title,
        route,
        highlighted,
      };

      // eslint-disable-next-line default-case
      switch (this.type) {
        case 'gallery':
          data.gallery = selectedItem;
          break;
        case 'page':
          data.page = selectedItem;
          break;
        case 'segment_page':
          data.segmentPage = selectedItem;
          break;
        case 'form':
          data.form = selectedItem;
          break;
        case 'artist':
          data.artist = selectedItem;
          break;
        case 'blog_home_page':
          data.blogHomePage = true;
          break;
        case 'blog_category':
          data.category = selectedItem;
          break;
      }

      if (!data.blogHomePage) {
        data.blogHomePage = false;
      }
      try {
        if (this.newItem) {
          let url = '';
          if (Number.isInteger(this.parentId)) {
            url = `/api/menu-item/${this.parentId}/item`;
          } else {
            url = `/api/menu/${this.menuId}/item`;
          }
          const item = await post(url, {
            ...data,
            position: this.position,
          });
          this.onHide(item);
        } else {
          await put(`/api/menu-item/${this.menuItemId}`, data);
          const result = {
            id: this.menuItemId,
            position: this.position,
            title,
            route,
            highlighted,
          };

          const selectedItemByType = this.items.find((g) => g.id === selectedItem);
          // eslint-disable-next-line default-case
          switch (this.type) {
            case 'gallery':
              result.gallery = selectedItemByType;
              break;
            case 'page':
              result.page = selectedItemByType;
              break;
            case 'segment_page':
              result.segmentPage = selectedItemByType;
              break;
            case 'form':
              result.form = selectedItemByType;
              break;
            case 'artist':
              result.artist = selectedItemByType;
              break;
            case 'blog_home_page':
              result.blogHomePage = true;
              break;
            case 'blog_category':
              result.category = selectedItemByType;
              break;
          }
          this.onHide(result);
        }
        container.remove();
      } catch (err) {
        if (err.status === 409) {
          await alert({
            title: localize({ key: 'pages_and_forms.segment.create.error.title' }),
            message: localize({ key: 'pages_and_forms.segment.create.error.conflict' }),
          });
        } else {
          await alert({
            title: localize({ key: 'pages_and_forms.segment.create.error.title' }),
            message: localize({ key: 'pages_and_forms.segment.create.error.generic' }),
          });
        }
      }
    });
  }
}
