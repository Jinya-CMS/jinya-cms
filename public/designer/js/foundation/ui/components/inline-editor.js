import { BaseEditorElement } from './base-editor.js';

class InlineEditorElement extends BaseEditorElement {
  getAdditionalStyles() {
    return `
      <style>
        :host {
          min-height: 11rem;
          display: block;
        }
        
        .jodit-wysiwyg,
        .jodit-workplace,
        .jodit-container.jodit.jodit_inline.jodit-wysiwyg_mode {
          min-height: 11rem;
        }
      </style>`;
  }

  getIsInline() {
    return true;
  }
}

if (!customElements.get('cms-inline-editor')) {
  customElements.define('cms-inline-editor', InlineEditorElement);
}
