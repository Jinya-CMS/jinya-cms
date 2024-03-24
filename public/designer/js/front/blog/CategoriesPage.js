import html from '../../../lib/jinya-html.js';
import clearChildren from '../../foundation/html/clearChildren.js';
import { get, httpDelete } from '../../foundation/http/request.js';
import JinyaDesignerPage from '../../foundation/JinyaDesignerPage.js';
import localize from '../../foundation/localize.js';
import alert from '../../foundation/ui/alert.js';
import confirm from '../../foundation/ui/confirm.js';

export default class CategoriesPage extends JinyaDesignerPage {
  constructor({ layout }) {
    super({ layout });
    this.categories = [];
    this.selectedCategory = null;
  }

  // eslint-disable-next-line class-methods-use-this
  toString() {
    return html`
      <div class="cosmo-side-list">
        <nav class="cosmo-side-list__items" id="category-list"></nav>
        <div class="cosmo-side-list__content jinya-designer">
          <div class="jinya-designer__title">
            <span class="cosmo-title" id="category-title"></span>
          </div>
          <div class="cosmo-toolbar cosmo-toolbar--designer">
            <div class="cosmo-toolbar__group">
              <button class="cosmo-button" id="edit-category">${localize({ key: 'blog.categories.action.edit' })}
              </button>
              <button class="cosmo-button" id="delete-category">
                ${localize({ key: 'blog.categories.action.delete' })}
              </button>
            </div>
          </div>
          <div class="jinya-designer__content jinya-designer__content--key-value-list">
            <dl class="cosmo-list is--key-value">
              <dt>${localize({ key: 'blog.categories.details.name' })}</dt>
              <dd id="category-name"></dd>
              <dt>${localize({ key: 'blog.categories.details.description' })}</dt>
              <dd id="category-description"></dd>
              <dt>${localize({ key: 'blog.categories.details.parent' })}</dt>
              <dd id="category-parent"></dd>
              <dt hidden data-type="webhook">
                ${localize({ key: 'blog.categories.details.webhook' })}
              </dt>
              <dd hidden data-type="webhook" id="category-webhook"></dd>
            </dl>
          </div>
        </div>
      </div>`;
  }

  displayCategories() {
    document.getElementById('edit-category').disabled = true;
    document.getElementById('delete-category').disabled = true;
    let list = '';
    for (const category of this.categories) {
      list += `<a class="cosmo-side-list__item" data-id="${category.id}">#${category.id} ${category.name}</a>`;
    }
    clearChildren({ parent: document.getElementById('category-list') });
    document.getElementById('category-list').innerHTML = `${list}
                <button id="new-category-button" class="cosmo-button is--full-width">
                    ${localize({ key: 'blog.categories.action.new' })}
                </button>`;
    document.querySelectorAll('.cosmo-side-list__item')
      .forEach((item) => {
        item.addEventListener('click', async () => {
          this.selectCategory({ id: item.getAttribute('data-id') });
          this.displaySelectedCategory();
        });
      });
    document.getElementById('new-category-button')
      .addEventListener('click', async () => {
        const { default: AddCategoryDialog } = await import('./categories/AddCategoryDialog.js');
        const dialog = new AddCategoryDialog({
          onHide: async (category) => {
            this.categories.push(category);
            this.displayCategories();
            this.selectCategory({ id: category.id });
            await this.displaySelectedCategory();
          },
          categories: this.categories,
        });
        dialog.show();
      });
  }

  displaySelectedCategory() {
    document.getElementById('category-title').innerText = `#${this.selectedCategory.id} ${this.selectedCategory.name}`;
    document.getElementById('category-name').innerText = this.selectedCategory.name;
    document.getElementById('category-description').innerText = this.selectedCategory.description ?? localize({ key: 'blog.categories.details.description_none' });
    document.getElementById('category-parent').innerText = this.selectedCategory.parent?.name ?? localize({ key: 'blog.categories.details.parent_none' });
    if (this.selectedCategory.webhookEnabled) {
      document.getElementById('category-webhook').innerText = this.selectedCategory.webhookUrl;
      document.querySelectorAll('[data-type="webhook"]')
        .forEach((item) => item.removeAttribute('hidden'));
    } else {
      document.querySelectorAll('[data-type="webhook"]')
        .forEach((item) => item.setAttribute('hidden', 'hidden'));
    }
  }

  selectCategory({ id }) {
    document.getElementById('edit-category').disabled = false;
    document.getElementById('delete-category').disabled = false;
    this.selectedCategory = this.categories.find((f) => f.id === parseInt(id, 10));
    document
      .querySelectorAll('.cosmo-side-list__item.is--active')
      .forEach((item) => item.classList.remove('is--active'));
    document.querySelector(`[data-id="${id}"]`)
      .classList
      .add('is--active');
  }

  async displayed() {
    await super.displayed();
    const { items } = await get('/api/blog/category');
    this.categories = items;

    this.displayCategories();
    if (this.categories.length > 0) {
      this.selectCategory({ id: this.categories[0].id });
      this.displaySelectedCategory();
    }
  }

  bindEvents() {
    super.bindEvents();
    document.getElementById('delete-category')
      .addEventListener('click', async () => {
        const confirmation = await confirm({
          title: localize({ key: 'blog.categories.delete.title' }),
          message: localize({
            key: 'blog.categories.delete.message',
            values: this.selectedCategory,
          }),
          declineLabel: localize({ key: 'blog.categories.delete.keep' }),
          approveLabel: localize({ key: 'blog.categories.delete.delete' }),
          negative: true,
        });
        if (confirmation) {
          try {
            await httpDelete(`/api/blog/category/${this.selectedCategory.id}`);
            this.categories = this.categories.filter((category) => category.id !== this.selectedCategory.id);
            this.displayCategories();
            if (this.categories.length > 0) {
              this.selectCategory({ id: this.categories[0].id });
              await this.displaySelectedCategory();
            } else {
              this.selectedCategory = null;
              document.getElementById('page-title').innerText = '';
            }
          } catch (e) {
            if (e.status === 409) {
              await alert({
                title: localize({ key: 'blog.categories.delete.error.title' }),
                message: localize({ key: 'blog.categories.delete.error.conflict' }),
                negative: true,
              });
            } else {
              await alert({
                title: localize({ key: 'blog.categories.delete.error.title' }),
                message: localize({ key: 'blog.categories.delete.error.generic' }),
                negative: true,
              });
            }
          }
        }
      });
    document.getElementById('edit-category')
      .addEventListener('click', async () => {
        const { default: EditCategoryDialog } = await import('./categories/EditCategoryDialog.js');
        const dialog = new EditCategoryDialog({
          onHide: async ({
                           name,
                           description,
                           webhookUrl,
                           parent,
                           webhookEnabled,
                         }) => {
            const category = this.categories.find((c) => c.id === this.selectedCategory.id);
            category.name = name;
            category.description = description;
            category.webhookUrl = webhookUrl;
            if (parent) {
              category.parent = this.categories.find((c) => c.id === parent.id);
            } else {
              category.parent = null;
            }
            category.webhookEnabled = webhookEnabled;
            this.displayCategories();
            this.selectCategory({ id: this.selectedCategory.id });
            await this.displaySelectedCategory();
          },
          categories: this.categories,
          ...this.selectedCategory,
        });
        dialog.show();
      });
  }
}
