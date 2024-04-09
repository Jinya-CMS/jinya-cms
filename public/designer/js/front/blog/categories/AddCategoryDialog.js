import html from '../../../../lib/jinya-html.js';
import { post } from '../../../foundation/http/request.js';
import localize from '../../../foundation/localize.js';
import alert from '../../../foundation/ui/alert.js';

export default class AddCategoryDialog {
  /**
   * Shows the create dialog
   * @param categories {{id: number, name: string}[]}
   * @param id {number}
   * @param onHide {function({id: number, name: string, description: string, webhookUrl: string, parent: {id: number, name: string}, webhookEnabled: boolean})}
   */
  constructor({
                categories,
                onHide,
              }) {
    this.onHide = onHide;
    this.categories = categories;
  }

  show() {
    const content = html` <form class="cosmo-modal__container" id="create-dialog-form">
      <div class="cosmo-modal">
        <h1 class="cosmo-modal__title">${localize({ key: 'blog.categories.create.title' })}</h1>
        <div class="cosmo-modal__content">
          <div class="cosmo-input__group">
            <label for="createCategoryName" class="cosmo-label">
              ${localize({ key: 'blog.categories.create.name' })}
            </label>
            <input required type="text" id="createCategoryName" class="cosmo-input" />
            <label for="createCategoryParent" class="cosmo-label">
              ${localize({ key: 'blog.categories.create.parent' })}
            </label>
            <select required id="createCategoryParent" class="cosmo-select">
              <option selected value="null">${localize({ key: 'blog.categories.create.parent_none' })}</option>
              ${this.categories.map(
      (category) => html` <option value=${category.id}>#${category.id} ${category.name}</option>`,
    )}
            </select>
            <label for="createCategoryDescription" class="cosmo-label is--textarea">
              ${localize({ key: 'blog.categories.create.description' })}
            </label>
            <textarea rows="5" id="createCategoryDescription" class="cosmo-textarea"></textarea>
            <label for="createCategoryWebhookUrl" class="cosmo-label">
              ${localize({ key: 'blog.categories.create.webhook_url' })}
            </label>
            <input type="text" id="createCategoryWebhookUrl" class="cosmo-input" />
            <div class="cosmo-input__group is--checkbox">
              <input class="cosmo-checkbox" type="checkbox" id="createCategoryWebhookEnabled" />
              <label for="createCategoryWebhookEnabled">
                ${localize({ key: 'blog.categories.create.webhook_enabled' })}
              </label>
            </div>
          </div>
        </div>
        <div class="cosmo-modal__button-bar">
          <button type="button" class="cosmo-button" id="cancel-create-dialog">
            ${localize({ key: 'blog.categories.create.cancel' })}
          </button>
          <button type="submit" class="cosmo-button" id="save-create-dialog">
            ${localize({ key: 'blog.categories.create.create' })}
          </button>
        </div>
      </div>
    </form>`;
    const container = document.createElement('div');
    container.innerHTML = content;
    document.body.append(container);
    document.getElementById('cancel-create-dialog')
      .addEventListener('click', () => {
        container.remove();
      });
    document.getElementById('create-dialog-form')
      .addEventListener('submit', async (e) => {
        e.preventDefault();
        const name = document.getElementById('createCategoryName').value;
        const description = document.getElementById('createCategoryDescription').value;
        const webhookUrl = document.getElementById('createCategoryWebhookUrl').value;
        let parent = document.getElementById('createCategoryParent').value;
        const webhookEnabled = document.getElementById('createCategoryWebhookEnabled').checked;
        if (parent === 'null') {
          parent = null;
        }
        try {
          const saved = await post('/api/blog/category', {
            name,
            description,
            webhookUrl,
            parentId: parent,
            webhookEnabled,
          });
          this.onHide(saved);
          container.remove();
        } catch (err) {
          if (err.status === 409) {
            await alert({
              title: localize({ key: 'blog.categories.create.error.title' }),
              message: localize({ key: 'blog.categories.create.error.conflict' }),
              negative: true,
            });
          } else {
            await alert({
              title: localize({ key: 'blog.categories.create.error.title' }),
              message: localize({ key: 'blog.categories.create.error.generic' }),
              negative: true,
            });
          }
        }
      });
  }
}
