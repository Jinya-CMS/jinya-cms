import html from '../../../../lib/jinya-html.js';
import TagEvent from './events/TagEvent.js';

class TagElement extends HTMLElement {
  constructor() {
    super();

    this.root = this.attachShadow({ mode: 'closed' });
  }

  get tagId() {
    return this.getAttribute('tag-id');
  }

  set tagId(value) {
    this.setAttribute('tag-id', value);
  }

  get emoji() {
    return this.getAttribute('emoji');
  }

  set emoji(value) {
    this.setAttribute('emoji', value);
    if (this.root.getElementById('emoji')) {
      this.root.getElementById('emoji').emoji = value;
    }
  }

  get name() {
    return this.getAttribute('name');
  }

  set name(value) {
    this.setAttribute('name', value);
    if (this.root.getElementById('name')) {
      this.root.getElementById('name').value = value;
    }
  }

  get color() {
    return this.getAttribute('color');
  }

  set color(value) {
    this.setAttribute('color', value);
    if (this.root.getElementById('color')) {
      this.root.getElementById('color').value = value;
    }

    this.style.setProperty('--primary-color', this.color);
    this.style.setProperty('--control-border-color', this.color);
  }

  get editable() {
    return this.getAttribute('editable');
  }

  set editable(value) {
    this.setAttribute('editable', value);
    if (this.root.getElementById('editable')) {
      this.root.getElementById('edit-button').hidden = !value;
    }
  }

  get deletable() {
    return this.getAttribute('deletable');
  }

  set deletable(value) {
    this.setAttribute('deletable', value);
    if (this.root.getElementById('deletable')) {
      this.root.getElementById('delete-button').hidden = !value;
    }
  }

  connectedCallback() {
    this.root.innerHTML = html` <style>
        :host {
          display: inline-flex;
        }

        button {
          cursor: pointer;
          font-size: 16px;
          padding: 4px 4px;
          box-sizing: border-box;
          border: 1px solid var(--control-border-color);
          background: var(--white);
          line-height: 19px;
          text-decoration: none;
          font-weight: normal;
          border-left: none;
          color: var(--primary-color);
          display: inline-flex;
          place-content: center;
          place-items: center;
        }

        button:hover {
          background: var(--primary-color);
          color: var(--white);
          outline: none;
          box-shadow: none;
        }

        ::part(name) {
          border: 1px solid var(--control-border-color);
          display: inline-flex;
          place-content: center;
          place-items: center;
          padding: 0 4px;
          color: var(--black);
        }

        ::part(arrow) {
          border: 10px solid transparent;
          border-right-color: var(--control-border-color);
        }

        span::selection {
          background: var(--primary-color);
          color: var(--white);
        }
      </style>
      <span part="arrow"></span>
      <span part="name">${this.emoji} ${this.name}</span>
      <button id="edit-button">
        <svg
          width="16"
          height="16"
          viewBox="0 0 24 24"
          stroke="currentColor"
          stroke-width="2"
          fill="none"
          stroke-linecap="round"
          stroke-linejoin="round"
        >
          <path d="M17 3a2.85 2.83 0 1 1 4 4L7.5 20.5 2 22l1.5-5.5Z" />
          <path d="m15 5 4 4" />
        </svg>
      </button>
      <button id="delete-button">
        <svg
          width="16"
          height="16"
          viewBox="0 0 24 24"
          stroke="currentColor"
          stroke-width="2"
          fill="none"
          stroke-linecap="round"
          stroke-linejoin="round"
        >
          <path d="M3 6h18" />
          <path d="M19 6v14c0 1-1 2-2 2H7c-1 0-2-1-2-2V6" />
          <path d="M8 6V4c0-1 1-2 2-2h4c1 0 2 1 2 2v2" />
        </svg>
      </button>`;
    this.style.setProperty('--primary-color', this.color);
    this.style.setProperty('--control-border-color', this.color);
    this.root.getElementById('edit-button').addEventListener('click', () => {
      this.dispatchEvent(new TagEvent('edit', this.tagId, this.name, this.color, this.emoji));
    });
    this.root.getElementById('delete-button').addEventListener('click', () => {
      this.dispatchEvent(new TagEvent('delete', this.tagId, this.name, this.color, this.emoji));
    });
  }

  attributeChangedCallback(property, oldValue, newValue) {
    if (oldValue === newValue) {
      return;
    }

    this[property] = newValue;
  }
}

customElements.define('cms-tag', TagElement);
