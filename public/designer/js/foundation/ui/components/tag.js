import TagEvent from './events/TagEvent.js';

class TagElement extends HTMLElement {
  constructor() {
    super();

    this.root = this.attachShadow({ mode: 'closed' });
  }

  static get observedAttributes() {
    return ['name', 'emoji', 'color', 'tagId', 'editable', 'deletable'];
  }

  get tagId() {
    return parseInt(this.getAttribute('tag-id'), 10);
  }

  set tagId(value) {
    this.setAttribute('tag-id', value);
  }

  get emoji() {
    return this.getAttribute('emoji');
  }

  set emoji(value) {
    this.setAttribute('emoji', value);
    if (this.root.getElementById('name')) {
      this.root.getElementById('name').textContent = `${value} ${this.name}`;
    }
  }

  get name() {
    return this.getAttribute('name');
  }

  set name(value) {
    this.setAttribute('name', value);
    if (this.root.getElementById('name')) {
      this.root.getElementById('name').textContent = `${this.emoji} ${value}`;
    }
  }

  get color() {
    return this.getAttribute('color');
  }

  set color(value) {
    this.setAttribute('color', value);
    this.style.setProperty('--primary-color', this.color);
    this.style.setProperty('--control-border-color', this.color);
  }

  get editable() {
    return this.hasAttribute('editable');
  }

  set editable(value) {
    if (value) {
      this.setAttribute('editable', value);
    } else {
      this.removeAttribute('editable');
    }
  }

  get deletable() {
    return this.hasAttribute('deletable');
  }

  set deletable(value) {
    if (value) {
      this.setAttribute('deletable', value);
    } else {
      this.removeAttribute('deletable');
    }
  }

  get active() {
    return this.hasAttribute('active');
  }

  set active(value) {
    if (value) {
      this.setAttribute('active', value);
    } else {
      this.removeAttribute('active');
    }
  }

  connectedCallback() {
    this.root.innerHTML = `
      <style>
        :host {
          display: inline-flex;
        }

        button {
          cursor: pointer;
          font-size: 1rem;
          padding: 0.25rem;
          box-sizing: border-box;
          border: 0.0625rem solid var(--control-border-color);
          background: transparent;
          line-height: 1.25rem;
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
          border: 0.0625rem solid var(--control-border-color);
          display: flex;
          place-content: center;
          place-items: center;
          padding: 0 0.25rem;
          color: var(--black);
        }

        ::part(arrow) {
          border: 0.75rem solid transparent;
          border-right-color: var(--control-border-color);
        }

        span::selection {
          background: var(--primary-color);
          color: var(--white);
        }

        :host(:not([deletable])) #name,
        :host(:not([editable])) #name {
          border-bottom-right-radius: var(--border-radius);
          border-top-right-radius: var(--border-radius);
        }

        :host(:not([deletable])) #delete-button {
          display: none;
        }

        :host(:not([editable])) #edit-button {
          display: none;
        }

        :host([deletable]) #delete-button {
          border-bottom-right-radius: var(--border-radius);
          border-top-right-radius: var(--border-radius);
        }

        :host(:not([deletable])[editable]) #edit-button {
          border-bottom-right-radius: var(--border-radius);
          border-top-right-radius: var(--border-radius);
        }

        :host([active]) #name {
          background: var(--primary-color);
          color: #ffffff;
        }
      </style>
      <span part="arrow"></span>
      <span part="name" id="name">${this.emoji} ${this.name}</span>
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
    this.root.getElementById('edit-button')
      .addEventListener('click', () => {
        this.dispatchEvent(new TagEvent('edit', this.tagId, this.name, this.color, this.emoji));
      });
    this.root.getElementById('delete-button')
      .addEventListener('click', () => {
        this.dispatchEvent(new TagEvent('delete', this.tagId, this.name, this.color, this.emoji));
      });
  }

  attributeChangedCallback(property, oldValue, newValue) {
    if (oldValue === newValue) {
      return;
    }

    const propertyName = property.replace(/-([a-z])/g, (m, w) => w.toUpperCase());
    this[propertyName] = newValue;
  }
}

if (!customElements.get('cms-tag')) {
  customElements.define('cms-tag', TagElement);
}
