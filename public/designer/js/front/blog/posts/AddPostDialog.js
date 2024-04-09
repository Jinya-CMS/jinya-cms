import html from '../../../../lib/jinya-html.js';
import { post } from '../../../foundation/http/request.js';
import localize from '../../../foundation/localize.js';
import alert from '../../../foundation/ui/alert.js';
import filePicker from '../../../foundation/ui/filePicker.js';

export default class AddPostDialog {
  /**
   * Shows the create dialog
   * @param category {{id: number, name: string}}
   * @param categories {{id: number, name: string}[]}
   * @param onHide {function()}
   */
  constructor({
                category,
                categories,
                onHide,
              }) {
    this.category = category;
    this.categories = categories;
    this.onHide = onHide;
  }

  async show() {
    let slugManuallyEdited = false;
    const content = html` <form class="cosmo-modal__container" id="create-dialog-form">
      <div class="cosmo-modal">
        <h1 class="cosmo-modal__title">${localize({ key: 'blog.posts.create.title' })}</h1>
        <div class="cosmo-modal__content">
          <div class="cosmo-input__group">
            <label for="createPostTitle" class="cosmo-label">
              ${localize({ key: 'blog.posts.create.post_title' })}
            </label>
            <input required type="text" id="createPostTitle" class="cosmo-input" />
            <label for="createPostSlug" class="cosmo-label"> ${localize({ key: 'blog.posts.create.slug' })} </label>
            <input required type="text" id="createPostSlug" class="cosmo-input" />
            <label for="createPostCategory" class="cosmo-label">
              ${localize({ key: 'blog.posts.create.category' })}
            </label>
            <select required id="createPostCategory" class="cosmo-select">
              ${this.categories.map(
      (cat) =>
        `<option ${cat.id === this.category ? 'selected' : ''} value="${cat.id}">#${cat.id} ${cat.name}</option>`,
    )}
            </select>
            <label for="createPostHeaderImage" class="cosmo-label">
              ${localize({ key: 'blog.posts.create.header_image' })}
            </label>
            <button
              class="cosmo-input is--picker"
              id="createPostHeaderImagePicker"
              data-picker="${localize({ key: 'blog.posts.create.file_picker_label' })}"
              type="button"
            >
              ${localize({ key: 'blog.posts.create.no_header_image' })}
            </button>
            <input type="hidden" id="createPostHeaderImage" />
            <img src="" alt="" id="selectedFile" class="jinya-picker__selected-file" hidden />
            <div class="cosmo-input__group is--checkbox">
              <input class="cosmo-checkbox" type="checkbox" id="createPostPublic" />
              <label for="createPostPublic">${localize({ key: 'blog.posts.create.public' })}</label>
            </div>
          </div>
        </div>
        <div class="cosmo-modal__button-bar">
          <button type="button" class="cosmo-button" id="cancel-create-dialog">
            ${localize({ key: 'blog.posts.create.cancel' })}
          </button>
          <button type="submit" class="cosmo-button" id="save-create-dialog">
            ${localize({ key: 'blog.posts.create.create' })}
          </button>
        </div>
      </div>
    </form>`;
    const container = document.createElement('div');
    container.innerHTML = content;
    document.body.append(container);
    document.getElementById('createPostHeaderImagePicker')
      .addEventListener('click', async (e) => {
        e.preventDefault();
        const selectedFileId = parseInt(document.getElementById('createPostHeaderImage').value, 10);
        const fileResult = await filePicker({
          title: localize({ key: 'blog.posts.create.header_image' }),
          selectedFileId,
        });
        if (fileResult) {
          document.getElementById('selectedFile').src = fileResult.path;
          document.getElementById('selectedFile').alt = fileResult.name;
          document.getElementById('selectedFile').hidden = false;

          document.getElementById('createPostHeaderImage').value = fileResult.id;
          document.getElementById('createPostHeaderImagePicker').innerText = fileResult.name;
        }
      });

    document.getElementById('cancel-create-dialog')
      .addEventListener('click', () => {
        container.remove();
      });
    document.getElementById('create-dialog-form')
      .addEventListener('submit', async (e) => {
        e.preventDefault();
        const title = document.getElementById('createPostTitle').value;
        const slug = document.getElementById('createPostSlug').value;
        const postPublic = document.getElementById('createPostPublic').checked;
        const headerImageId = parseInt(document.getElementById('createPostHeaderImage').value, 10);
        const categoryId = parseInt(document.getElementById('createPostCategory').value, 10);
        try {
          const data = {
            title,
            slug,
            public: postPublic,
            categoryId,
          };
          if (headerImageId !== -1) {
            data.headerImageId = headerImageId;
          }
          const saved = await post('/api/blog/post', data);
          this.onHide(saved);
          container.remove();
        } catch (err) {
          let msg = 'generic';
          if (err.status === 409 && err.message.includes('slug')) {
            msg = 'slug_exists';
          } else if (err.status === 409 && err.message.includes('title')) {
            msg = 'title_exists';
          }
          await alert({
            title: localize({ key: 'blog.posts.edit.error.title' }),
            message: localize({ key: `blog.posts.edit.error.${msg}` }),
            negative: true,
          });
        }
      });
    document.getElementById('createPostSlug')
      .addEventListener('input', () => {
        slugManuallyEdited = true;
      });
    document.getElementById('createPostTitle')
      .addEventListener('input', (e) => {
        if (!slugManuallyEdited) {
          document.getElementById('createPostSlug').value = e.currentTarget.value
            .toLowerCase()
            .trim()
            .replace(/[^\w\s-]/g, '')
            .replace(/[\s_-]+/g, '-')
            .replace(/^-+|-+$/g, '');
        }
      });
  }
}
