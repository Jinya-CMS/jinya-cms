import { BaseEditorElement } from './base-editor.js';

class ToolbarEditorElement extends BaseEditorElement {
  get height() {
    return this.getAttribute('height');
  }

  set height(value) {
    this.setAttribute('height', value);
  }
}

if (!customElements.get('cms-toolbar-editor')) {
  customElements.define('cms-toolbar-editor', ToolbarEditorElement);
}
