import html from '../../../../lib/jinya-html.js';
import localize from '../../../foundation/localize.js';
import getEditor from '../../../foundation/ui/tiny.js';

export default class EditFileSegmentDialog {
  /**
   * Shows the edit dialog
   * @param onHide {function({position: number, html: string, segment: any})}
   * @param html {string}
   * @param position {number}
   */
  constructor({
                onHide, html: content, position,
              }) {
    this.onHide = onHide;
    this.position = position;
    this.html = content ?? '';
  }

  async show() {
    const content = html`
        <div class="cosmo-modal__backdrop"></div>
        <form class="cosmo-modal__container" id="edit-dialog-form">
            <div class="cosmo-modal">
                <h1 class="cosmo-modal__title">${localize({ key: 'blog.posts.designer.edit.title' })}</h1>
                <div class="cosmo-modal__content">
                    <div class="cosmo-input__group">
                        <textarea id="editHtml" hidden></textarea>
                    </div>
                </div>
                <div class="cosmo-modal__button-bar">
                    <button type="button" class="cosmo-button" id="cancel-edit-dialog">
                        ${localize({ key: 'blog.posts.designer.edit.cancel' })}
                    </button>
                    <button type="submit" class="cosmo-button" id="save-edit-dialog">
                        ${localize({ key: 'blog.posts.designer.edit.update' })}
                    </button>
                </div>
            </div>
        </form>`;
    const container = document.createElement('div');
    container.innerHTML = content;
    document.body.append(container);
    const tiny = await getEditor({ element: document.getElementById('editHtml') });
    tiny.setContent(this.html);

    document.getElementById('cancel-edit-dialog').addEventListener('click', () => {
      container.remove();
    });
    document.getElementById('edit-dialog-form').addEventListener('submit', async (e) => {
      e.preventDefault();
      this.onHide({
        position: this.position, html: tiny.getContent(),
      });
      tinymce.remove();
      container.remove();
    });
  }
}
