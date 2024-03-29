import html from '../../../../lib/jinya-html.js';
import { post, put } from '../../../foundation/http/request.js';
import localize from '../../../foundation/localize.js';

export default class EditDropdownItemDialog {
  /**
   * Shows the edit dialog
   * @param onHide {function({item: any, label: string, placeholder: string, helpText: string, isRequired: string, options: string[]})}
   * @param id {number}
   * @param formId {number}
   * @param position {number}
   * @param label {string}
   * @param placeholder {string}
   * @param options {string[]}
   * @param helpText {string}
   * @param isRequired {boolean}
   * @param newItem {boolean}
   */
  constructor({ onHide, id, formId, label, position, placeholder, helpText, isRequired, options, newItem = false }) {
    this.onHide = onHide;
    this.position = position;
    this.newItem = newItem;
    this.id = id;
    this.formId = formId;
    this.label = label;
    this.placeholder = placeholder;
    this.helpText = helpText;
    this.isRequired = isRequired;
    this.options = options;
  }

  show() {
    const content = html` <div class="cosmo-modal__backdrop"></div>
      <form class="cosmo-modal__container" id="edit-dialog-form">
        <div class="cosmo-modal">
          <h1 class="cosmo-modal__title">${localize({ key: 'pages_and_forms.form.designer.edit.title' })}</h1>
          <div class="cosmo-modal__content">
            <div class="cosmo-input__group">
              <label for="editItemLabel" class="cosmo-label">
                ${localize({ key: 'pages_and_forms.form.designer.edit.label' })}
              </label>
              <input
                value="${this.newItem ? '' : this.label}"
                required
                type="text"
                id="editItemLabel"
                class="cosmo-input"
              />
              <label for="editItemPlaceholder" class="cosmo-label">
                ${localize({ key: 'pages_and_forms.form.designer.edit.placeholder' })}
              </label>
              <input
                value="${this.newItem ? '' : this.placeholder}"
                type="text"
                id="editItemPlaceholder"
                class="cosmo-input"
              />
              <label for="editItemHelpText" class="cosmo-label">
                ${localize({ key: 'pages_and_forms.form.designer.edit.help_text' })}
              </label>
              <input
                value="${this.newItem ? '' : this.helpText}"
                type="text"
                id="editItemHelpText"
                class="cosmo-input"
              />
              <label for="editItemOptions" class="cosmo-label cosmo-label--textarea">
                ${localize({ key: 'pages_and_forms.form.designer.edit.items' })}
              </label>
              <textarea type="text" id="editItemOptions" class="cosmo-textarea" rows="5">
${this.newItem ? '' : this.options.join('\n')}</textarea
              >
              <div class="cosmo-checkbox__group">
                <input
                  type="checkbox"
                  id="editItemIsRequired"
                  class="cosmo-checkbox"
                  ${this.isRequired ? 'checked' : ''}
                />
                <label for="editItemIsRequired">
                  ${localize({ key: 'pages_and_forms.form.designer.edit.is_required' })}
                </label>
              </div>
            </div>
          </div>
          <div class="cosmo-modal__button-bar">
            <button class="cosmo-button" id="cancel-edit-dialog">
              ${localize({ key: 'pages_and_forms.form.designer.edit.cancel' })}
            </button>
            <button class="cosmo-button" id="update-edit-dialog">
              ${localize({ key: 'pages_and_forms.form.designer.edit.update' })}
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
      const label = document.getElementById('editItemLabel').value;
      const placeholder = document.getElementById('editItemPlaceholder').value;
      const helpText = document.getElementById('editItemHelpText').value;
      const isRequired = document.getElementById('editItemIsRequired').checked;
      const options = document.getElementById('editItemOptions').value.split(/\n|\r|\r\n/gm);
      if (this.newItem) {
        const item = await post(`/api/form/${this.formId}/item`, {
          label,
          placeholder,
          helpText,
          isRequired,
          options,
          position: this.position,
          type: 'select',
        });
        this.onHide({ item });
      } else {
        await put(`/api/form/${this.formId}/item/${this.position}`, {
          label,
          placeholder,
          helpText,
          isRequired,
          options,
        });
        this.onHide({
          label,
          placeholder,
          helpText,
          isRequired,
          options,
        });
      }
      container.remove();
    });
  }
}
