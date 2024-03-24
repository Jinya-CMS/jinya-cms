import html from '../../../../lib/jinya-html.js';
import { put } from '../../../foundation/http/request.js';
import localize from '../../../foundation/localize.js';
import alert from '../../../foundation/ui/alert.js';

export default class EditCategoryDialog {
  /**
   Shows the edit dialog
   * @param categories {{id: number, name: string}[]}
   * @param id {number}
   * @param name {string}
   * @param description {string}
   * @param webhookUrl {string}
   * @param parent {{id: number, name: string}}
   * @param webhookEnabled {boolean}
   * @param onHide {function({id: number, name: string, description: string, webhookUrl: string, parent: {id: number, name: string}, webhookEnabled: boolean})}
   */
  constructor({
                categories,
                id,
                name,
                description,
                webhookUrl,
                parent,
                webhookEnabled,
                onHide,
              }) {
    this.onHide = onHide;
    this.categories = categories;
    this.id = id;
    this.name = name;
    this.description = description;
    this.webhookUrl = webhookUrl;
    this.parent = parent;
    this.webhookEnabled = webhookEnabled;
  }

  show() {
    const content = html`
      <form class="cosmo-modal__container" id="edit-dialog-form">
        <div class="cosmo-modal">
          <h1 class="cosmo-modal__title">${localize({ key: 'blog.categories.edit.title' })}</h1>
          <div class="cosmo-modal__content">
            <div class="cosmo-input__group">
              <label for="editCategoryName" class="cosmo-label">
                ${localize({ key: 'blog.categories.edit.name' })}
              </label>
              <input required type="text" id="editCategoryName" class="cosmo-input" value="${this.name}" />
              <label for="editCategoryParent" class="cosmo-label">
                ${localize({ key: 'blog.categories.edit.parent' })}
              </label>
              <select required id="editCategoryParent" class="cosmo-select">
                <option ${this.parent ? '' : 'selected'} value="null">
                  ${localize({ key: 'blog.categories.edit.parent_none' })}
                </option>
                ${this.categories.map((category) => html`
                  <option
                    ${this.parent && this.parent?.id === category.id ? 'selected' : ''}
                    value=${category.id}
                  >
                      #${category.id} ${category.name}
                  </option>`)}
              </select>
              <label for="editCategoryDescription" class="cosmo-label is--textarea">
                ${localize({ key: 'blog.categories.edit.description' })}
              </label>
              <textarea rows="5" id="editCategoryDescription" class="cosmo-textarea">${this.description}</textarea>
              <label for="editCategoryWebhookUrl" class="cosmo-label">
                ${localize({ key: 'blog.categories.edit.webhook_url' })}
              </label>
              <input type="text" id="editCategoryWebhookUrl" class="cosmo-input" value="${this.webhookUrl}" />
              <div class="cosmo-input__group is--checkbox">
                <input
                  class="cosmo-checkbox"
                  type="checkbox"
                  id="editCategoryWebhookEnabled"
                  ${this.webhookEnabled ? 'checked' : ''}
                />
                <label for="editCategoryWebhookEnabled">
                  ${localize({ key: 'blog.categories.edit.webhook_enabled' })}
                </label>
              </div>
            </div>
          </div>
          <div class="cosmo-modal__button-bar">
            <button type="button" class="cosmo-button" id="cancel-edit-dialog">
              ${localize({ key: 'blog.categories.edit.cancel' })}
            </button>
            <button type="submit" class="cosmo-button" id="save-edit-dialog">
              ${localize({ key: 'blog.categories.edit.edit' })}
            </button>
          </div>
        </div>
      </form>`;
    const container = document.createElement('div');
    container.innerHTML = content;
    document.body.append(container);
    document.getElementById('cancel-edit-dialog')
      .addEventListener('click', () => {
        container.remove();
      });
    document.getElementById('edit-dialog-form')
      .addEventListener('submit', async (e) => {
        e.preventDefault();
        const name = document.getElementById('editCategoryName').value;
        const description = document.getElementById('editCategoryDescription').value;
        const webhookUrl = document.getElementById('editCategoryWebhookUrl').value;
        let parent = document.getElementById('editCategoryParent').value;
        const webhookEnabled = document.getElementById('editCategoryWebhookEnabled').checked;
        if (parent === 'null') {
          parent = null;
        }
        try {
          await put(`/api/blog/category/${this.id}`, {
            name,
            description,
            webhookUrl,
            parentId: parent,
            webhookEnabled,
          });
          this.onHide({
            name,
            description,
            webhookUrl,
            parent: this.categories.find((c) => c.id === parseInt(parent, 10)),
            webhookEnabled,
          });
          container.remove();
        } catch (err) {
          if (err.status === 409) {
            await alert({
              title: localize({ key: 'blog.categories.edit.error.title' }),
              message: localize({ key: 'blog.categories.edit.error.conflict' }),
            });
          } else {
            await alert({
              title: localize({ key: 'blog.categories.edit.error.title' }),
              message: localize({ key: 'blog.categories.edit.error.generic' }),
            });
          }
        }
      });
  }
}
