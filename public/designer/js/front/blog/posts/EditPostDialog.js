import html from '../../../../lib/jinya-html.js';
import { get, put } from '../../../foundation/http/request.js';
import localize from '../../../foundation/localize.js';
import alert from '../../../foundation/ui/alert.js';
import filePicker from '../../../foundation/ui/filePicker.js';

export default class EditPostDialog {
  /**
   * Shows the edit dialog
   * @param id {number}
   * @param category {number}
   * @param categories {{id: number, name: string}[]}
   * @param title {string}
   * @param slug {string}
   * @param postPublic {boolean}
   * @param headerImage {number}
   * @param onHide {function()}
   */
  constructor({
                id,
                category,
                title,
                slug,
                postPublic,
                categories,
                headerImage,
                onHide,
              }) {
    this.category = category;
    this.categories = categories;
    this.onHide = onHide;
    this.title = title;
    this.slug = slug;
    this.headerImage = headerImage;
    this.postPublic = postPublic;
    this.id = id;
  }

  async show() {
    const { items: files } = await get('/api/file');
    const header = files.find((file) => file.id === this.headerImage);
    const content = html` <form class="cosmo-modal__container" id="edit-dialog-form">
      <div class="cosmo-modal">
        <h1 class="cosmo-modal__title">${localize({ key: 'blog.posts.edit.title' })}</h1>
        <div class="cosmo-modal__content">
          <div class="cosmo-input__group">
            <label for="editPostTitle" class="cosmo-label"> ${localize({ key: 'blog.posts.edit.post_title' })} </label>
            <input required type="text" id="editPostTitle" class="cosmo-input" value="${this.title}" />
            <label for="editPostSlug" class="cosmo-label"> ${localize({ key: 'blog.posts.edit.slug' })} </label>
            <input required type="text" id="editPostSlug" class="cosmo-input" value="${this.slug}" />
            <label for="editPostCategory" class="cosmo-label"> ${localize({ key: 'blog.posts.edit.category' })} </label>
            <select required id="editPostCategory" class="cosmo-select">
              ${this.categories.map(
      (cat) =>
        `<option ${cat.id === this.category ? 'selected' : ''} value="${cat.id}">#${cat.id} ${cat.name}</option>`,
    )}
            </select>
            <label for="editPostHeaderImage" class="cosmo-label">
              ${localize({ key: 'blog.posts.edit.header_image' })}
            </label>
            <button
              class="cosmo-input is--picker"
              id="editPostHeaderImagePicker"
              data-picker="${localize({ key: 'blog.posts.edit.file_picker_label' })}"
              type="button"
            >
              ${header?.name ?? localize({ key: 'blog.posts.edit.no_header_image' })}
            </button>
            <input type="hidden" value="${header?.id}" id="editPostHeaderImage" />
            <img src="${header?.path}" alt="${header?.name}" id="selectedFile" class="jinya-picker__selected-file" />
            <div class="cosmo-checkbox__group">
              <input ${this.postPublic ? 'checked' : ''} class="cosmo-checkbox" type="checkbox" id="editPostPublic" />
              <label for="editPostPublic">${localize({ key: 'blog.posts.edit.public' })}</label>
            </div>
          </div>
        </div>
        <div class="cosmo-modal__button-bar">
          <button type="button" class="cosmo-button" id="cancel-edit-dialog">
            ${localize({ key: 'blog.posts.edit.cancel' })}
          </button>
          <button type="submit" class="cosmo-button" id="save-edit-dialog">
            ${localize({ key: 'blog.posts.edit.update' })}
          </button>
        </div>
      </div>
    </form>`;
    const container = document.createElement('div');
    container.innerHTML = content;
    document.body.append(container);
    document.getElementById('editPostHeaderImagePicker')
      .addEventListener('click', async (e) => {
        e.preventDefault();
        const selectedFileId = parseInt(document.getElementById('editPostHeaderImage').value, 10);
        const fileResult = await filePicker({
          title: localize({ key: 'blog.posts.edit.header_image' }),
          selectedFileId,
        });
        if (fileResult) {
          document.getElementById('selectedFile').src = fileResult.path;
          document.getElementById('selectedFile').alt = fileResult.name;

          document.getElementById('editPostHeaderImage').value = fileResult.id;
          document.getElementById('editPostHeaderImagePicker').innerText = fileResult.name;
        }
      });

    document.getElementById('cancel-edit-dialog')
      .addEventListener('click', () => {
        container.remove();
      });
    document.getElementById('edit-dialog-form')
      .addEventListener('submit', async (e) => {
        e.preventDefault();
        const title = document.getElementById('editPostTitle').value;
        const slug = document.getElementById('editPostSlug').value;
        const postPublic = document.getElementById('editPostPublic').checked;
        const headerImageId = parseInt(document.getElementById('editPostHeaderImage').value, 10);
        const categoryId = parseInt(document.getElementById('editPostCategory').value, 10);
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
          await put(`/api/blog/post/${this.id}`, data);
          this.onHide({
            ...data,
            headerImage: files.find((f) => f.id === headerImageId),
            category: this.categories.find((c) => c.id === categoryId),
          });
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
  }
}
