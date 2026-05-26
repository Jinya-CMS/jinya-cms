import { createJodit } from '../jodit.js';
import { getMutationObserver } from './editor-utils.js';

export class BaseEditorElement extends HTMLElement {
  constructor() {
    super();

    this.root = this;
    this.editor = null;
  }

  #internalChange = false;
  #observer = null;

  getAdditionalStyles() {
    return '';
  }

  getIsInline() {
    return false;
  }

  connectedCallback() {
    this.root.innerHTML = `
      ${this.getAdditionalStyles()}
      <div x-ignore><textarea></textarea></div>
    `;
    this.editor = createJodit(this.root.querySelector('textarea'), this.getIsInline(), this.height);
    this.editor.value = this.content;
    this.editor.events.on('change', (e) => {
      this.#internalChange = true;
    });

    this.#initObserver();
  }

  #initObserver() {
    if (this.#observer) {
      this.#observer.disconnect();
    }

    this.#observer = getMutationObserver(this.root, this.type);
    this.#observer.observe(this, { childList: true, subtree: true });
  }

  disconnectedCallback() {
    this.editor?.destruct();
  }

  static get observedAttributes() {
    return ['content', 'focused', 'type'];
  }

  get type() {
    return this.getAttribute('type');
  }

  set type(value) {
    this.setAttribute('type', value);
    this.#initObserver();
  }

  get content() {
    return this.getAttribute('content');
  }

  set content(value) {
    this.setAttribute('content', value);
    if (!this.#internalChange) {
      this.editor.value = value;
    }
    this.#internalChange = false;
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
