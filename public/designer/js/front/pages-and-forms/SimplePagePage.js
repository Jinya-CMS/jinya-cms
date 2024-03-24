import html from '../../../lib/jinya-html.js';
import clearChildren from '../../foundation/html/clearChildren.js';
import { get, httpDelete, put } from '../../foundation/http/request.js';
import JinyaDesignerPage from '../../foundation/JinyaDesignerPage.js';
import localize from '../../foundation/localize.js';
import alert from '../../foundation/ui/alert.js';
import confirm from '../../foundation/ui/confirm.js';
import getEditor from '../../foundation/ui/tiny.js';

export default class SimplePagePage extends JinyaDesignerPage {
  constructor({ layout }) {
    super({ layout });
    this.pages = [];
    this.selectedPage = {};
    this.tiny = null;
  }

  selectPage({ id }) {
    document.getElementById('edit-page').disabled = false;
    document.getElementById('delete-page').disabled = false;
    this.selectedPage = this.pages.find((f) => f.id === parseInt(id, 10));
    document
      .querySelectorAll('.cosmo-side-list__item.is--active')
      .forEach((item) => item.classList.remove('is--active'));
    document.querySelector(`[data-id="${id}"]`)
      .classList
      .add('is--active');
  }

  displaySelectedPage() {
    document.getElementById('page-title').innerText = `#${this.selectedPage.id} ${this.selectedPage.title}`;
    this.updateTiny();
  }

  displayPages() {
    document.getElementById('edit-page').disabled = true;
    document.getElementById('delete-page').disabled = true;
    let list = '';
    for (const page of this.pages) {
      list += `<a class="cosmo-side-list__item" data-id="${page.id}">${page.title}</a>`;
    }
    clearChildren({ parent: document.getElementById('page-list') });
    document.getElementById('page-list').innerHTML = `${list}
                <button id="new-page-button" class="cosmo-button is--full-width">
                    ${localize({ key: 'pages_and_forms.simple.action.new' })}
                </button>`;
    document.querySelectorAll('.cosmo-side-list__item')
      .forEach((item) => {
        item.addEventListener('click', async () => {
          this.selectPage({ id: item.getAttribute('data-id') });
          this.displaySelectedPage();
        });
      });
    document.getElementById('new-page-button')
      .addEventListener('click', async () => {
        const { default: AddSimplePageDialog } = await import('./simple-pages/AddSimplePageDialog.js');
        const dialog = new AddSimplePageDialog({
          onHide: async (page) => {
            this.pages.push(page);
            this.displayPages();
            this.selectPage({ id: page.id });
            await this.displaySelectedPage();
          },
        });
        dialog.show();
      });
  }

  updateTiny() {
    this.tiny.setContent(this.selectedPage.content);
  }

  // eslint-disable-next-line class-methods-use-this
  toString() {
    return html`
      <div class="cosmo-side-list">
        <nav class="cosmo-side-list__items" id="page-list"></nav>
        <div class="cosmo-side-list__content jinya-designer">
          <div class="jinya-designer__title">
            <span class="cosmo-title" id="page-title"></span>
          </div>
          <div class="cosmo-toolbar cosmo-toolbar--designer">
            <div class="cosmo-toolbar__group">
              <button class="cosmo-button" id="edit-page">
                ${localize({ key: 'pages_and_forms.simple.action.edit' })}
              </button>
              <button class="cosmo-button" id="delete-page">
                ${localize({ key: 'pages_and_forms.simple.action.delete' })}
              </button>
            </div>
          </div>
          <div class="jinya-designer__content jinya-designer__content--simple-pages">
            <div id="page-editor"></div>
            <div class="cosmo-button__container">
              <button id="discard-changes" class="cosmo-button">
                ${localize({ key: 'pages_and_forms.simple.action.discard_content' })}
              </button>
              <button id="save-changes" class="cosmo-button is--primary">
                ${localize({ key: 'pages_and_forms.simple.action.save_content' })}
              </button>
            </div>
          </div>
        </div>
      </div>`;
  }

  bindEvents() {
    super.bindEvents();
    document
      .getElementById('discard-changes')
      .addEventListener('click', () => this.tiny.setContent(this.selectedPage.content));
    document.getElementById('save-changes')
      .addEventListener('click', async () => {
        const content = this.tiny.getContent();
        try {
          await put(`/api/simple-page/${this.selectedPage.id}`, { content });
          this.selectedPage.content = content;
          this.pages.find((p) => p.id === this.selectedPage.id).content = content;
        } catch (e) {
          if (e.status === 409) {
            await alert({
              title: localize({ key: 'pages_and_forms.simple.edit.error.title' }),
              message: localize({ key: 'pages_and_forms.simple.edit.error.conflict' }),
              negative: true,
            });
          } else {
            await alert({
              title: localize({ key: 'pages_and_forms.simple.edit.error.title' }),
              message: localize({ key: 'pages_and_forms.simple.edit.error.generic' }),
              negative: true,
            });
          }
        }
      });
    document.getElementById('edit-page')
      .addEventListener('click', async () => {
        const { default: EditSimplePageDialog } = await import('./simple-pages/EditSimplePageDialog.js');
        const dialog = new EditSimplePageDialog({
          onHide: async ({
                           id,
                           title,
                         }) => {
            this.pages.find((p) => p.id === id).title = title;
            this.displayPages();
            this.selectPage({ id });
            await this.displaySelectedPage();
          },
          title: this.selectedPage.title,
          id: this.selectedPage.id,
        });
        dialog.show();
      });
    document.getElementById('delete-page')
      .addEventListener('click', async () => {
        const confirmation = await confirm({
          title: localize({ key: 'pages_and_forms.simple.delete.title' }),
          message: localize({
            key: 'pages_and_forms.simple.delete.message',
            values: this.selectedPage,
          }),
          declineLabel: localize({ key: 'pages_and_forms.simple.delete.keep' }),
          approveLabel: localize({ key: 'pages_and_forms.simple.delete.delete' }),
          negative: true,
        });
        if (confirmation) {
          try {
            await httpDelete(`/api/simple-page/${this.selectedPage.id}`);
            this.pages = this.pages.filter((page) => page.id !== this.selectedPage.id);
            this.displayPages();
            if (this.pages.length > 0) {
              this.selectPage({ id: this.pages[0].id });
              await this.displaySelectedPage();
            } else {
              this.selectedPage = null;
              await this.displaySegments();
              document.getElementById('page-title').innerText = '';
            }
          } catch (e) {
            if (e.status === 409) {
              await alert({
                title: localize({ key: 'pages_and_forms.simple.delete.error.title' }),
                message: localize({ key: 'pages_and_forms.simple.delete.error.conflict' }),
                negative: true,
              });
            } else {
              await alert({
                title: localize({ key: 'pages_and_forms.simple.delete.error.title' }),
                message: localize({ key: 'pages_and_forms.simple.delete.error.generic' }),
                negative: true,
              });
            }
          }
        }
      });
  }

  async displayed() {
    await super.displayed();
    this.tiny = await getEditor({
      element: document.getElementById('page-editor'),
      height: 'calc(100% - 2.5rem)',
    });
    const { items } = await get('/api/simple-page');
    this.pages = items;

    this.displayPages();
    if (this.pages.length > 0) {
      this.selectPage({ id: this.pages[0].id });
      this.displaySelectedPage();
    }
  }
}
