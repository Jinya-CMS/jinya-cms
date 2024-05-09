import { createJodit } from '../jodit.js';
import { EditorChangeEvent } from './events/EditorChangeEvent.js';

class InlineEditorElement extends HTMLElement {
  constructor() {
    super();

    this.root = this.attachShadow({ mode: 'closed' });
    this.editor = null;
  }

  connectedCallback() {
    this.root.innerHTML = `
      <style>
        @import "/lib/cosmo/typography.css";
        @import "/designer/lib/jodit/jodit.css";
        @import "/designer/css/jodit.css";
      </style>
      <textarea></textarea>
    `;
    this.editor = createJodit(this.root.querySelector('textarea'), true);
    this.editor.value = this.content;
    this.editor.events.on('change', (e) => {
      this.dispatchEvent(new EditorChangeEvent(e));
    });
  }

  disconnectedCallback() {
    this.editor?.destruct();
  }

  static get observedAttributes() {
    return ['content', 'focused'];
  }

  get content() {
    return this.getAttribute('content');
  }

  set content(value) {
    this.setAttribute('content', value);
    this.editor.value = value;
  }

  get focused() {
    return this.hasAttribute('focused');
  }

  set focused(value) {
    if (value) {
      this.setAttribute('focused', value);
      this.editor.focus();
    } else {
      this.removeAttribute('focused');
    }
  }

  attributeChangedCallback(property, oldValue, newValue) {
    if (oldValue === newValue) {
      return;
    }

    const propertyName = property.replace(/-([a-z])/g, (m, w) => w.toUpperCase());
    this[propertyName] = newValue;
  }
}

if (!customElements.get('cms-inline-editor')) {
  customElements.define('cms-inline-editor', InlineEditorElement);
}
