import localize from '../../utils/localize.js';
import './emoji-picker.js';
import TagPopupSubmitEvent from './events/TagPopupSubmitEvent.js';
import TagPopupCloseEvent from './events/TagPopupCloseEvent.js';

class TagPopupElement extends HTMLElement {
  constructor() {
    super();

    this.root = this.attachShadow({ mode: 'closed' });
  }

  static get observedAttributes() {
    return ['popup-title', 'name', 'emoji', 'color', 'open', 'save-label', 'cancel-label', 'target', 'error'];
  }

  get popupTitle() {
    return this.getAttribute('popup-title');
  }

  set popupTitle(value) {
    this.setAttribute('popup-title', value);
    if (this.root.querySelector('legend')) {
      this.root.querySelector('legend').textContent = value;
    }
  }

  get error() {
    return this.getAttribute('error');
  }

  set error(value) {
    this.setAttribute('error', value);
    if (this.root.getElementById('error')) {
      this.root.getElementById('error').textContent = value;
    }
  }

  get target() {
    return this.getAttribute('target');
  }

  set target(value) {
    this.setAttribute('target', value);
    this.#recalculatePosition();
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
  }

  get saveLabel() {
    return this.getAttribute('save-label');
  }

  set saveLabel(value) {
    this.setAttribute('save-label', value);
    if (this.root.getElementById('save-button')) {
      this.root.getElementById('save-button').textContent = value;
    }
  }

  get cancelLabel() {
    return this.getAttribute('cancel-label');
  }

  set cancelLabel(value) {
    this.setAttribute('cancel-label', value);
    if (this.root.getElementById('cancel-button')) {
      this.root.getElementById('cancel-button').textContent = value;
    }
  }

  get open() {
    return this.hasAttribute('open');
  }

  set open(value) {
    if (value) {
      this.#recalculatePosition();
      this.setAttribute('open', value);
    } else {
      this.removeAttribute('open');
    }
  }

  #recalculatePosition() {
    /** @type HTMLElement */
    const target = document.querySelector(this.target);

    if (target) {
      const top = target.offsetTop + target.offsetHeight + 16;
      const left = target.offsetLeft + target.offsetWidth / 2 - this.offsetWidth / 2;
      if (this.root.getElementById('target-position')) {
        this.root.getElementById('target-position').innerHTML = `
:host {
    left: ${left}px;
    top: ${top}px;
}`;
      }
    }
  }

  connectedCallback() {
    this.root.innerHTML = `
      <style id="target-position"></style>
      <style>
        @import "/lib/cosmo/form.css";
        @import "/lib/cosmo/buttons.css";

        :host *::selection {
          background: var(--primary-color);
          color: var(--white);
        }

        :host {
          display: none;
          background: var(--modal-background);
          backdrop-filter: var(--modal-backdrop-filter);
          position: fixed;
          border: 0.0625rem solid var(--primary-color);
          padding: 1rem;
          color: var(--primary-color);
          border-radius: var(--border-radius);
        }

        :host:hover {
          color: var(--primary-color);
        }

        :host::before {
          content: '';
          position: absolute;
          left: 50%;
          transform: translate(-50%);
          border: 1rem solid transparent;
          border-bottom-color: var(--primary-color);
          top: -2rem;
        }

        :host([open]) {
          display: block;
        }

        fieldset {
          min-width: 0;
          padding: 0;
          margin: 0;
          border: 0;
          grid-column: span 2;
        }

        legend {
          font-size: 1.5rem;
          height: 1.5rem;
          font-weight: var(--font-weight-light);
          text-transform: uppercase;
          margin-top: 0.75rem;
          margin-bottom: 0.75rem;
        }

        .is--emoji::part(popup) {
          margin-left: -0.5rem;
          margin-bottom: 0.25rem;
        }
      </style>
      <form>
        <fieldset>
          <legend>${this.popupTitle}</legend>
          <div class="cosmo-input__group">
            <label class="cosmo-label"
                   for="name">${localize({ key: 'media.files.tags.popup.name' })}</label>
            <input type="text" class="cosmo-input" required id="name">
            <span class="cosmo-input__message is--negative" id="error">${this.error ?? ''}</span>
            <label class="cosmo-label"
                   for="color">${localize({ key: 'media.files.tags.popup.color' })}</label>
            <input type="color" value="${this.color}" class="cosmo-input" id="color">
            <label class="cosmo-label"
                   for="emoji">${localize({ key: 'media.files.tags.popup.emoji' })}</label>
            <cms-emoji-picker emoji="${this.emoji}" class="cosmo-input is--emoji" id="emoji">
          </div>
          <div class="cosmo-button__container">
            <button id="cancel-button" class="cosmo-button" type="button">${this.cancelLabel}</button>
            <button id="save-button" class="cosmo-button is--primary" type="submit">${this.saveLabel}</button>
          </div>
        </fieldset>
      </form>`;
    this.root.querySelector('form').addEventListener('submit', (evt) => {
      evt.preventDefault();
      this.dispatchEvent(new TagPopupSubmitEvent(this.name, this.color, this.emoji));
    });
    this.root.getElementById('cancel-button').addEventListener('click', (evt) => {
      evt.preventDefault();
      this.dispatchEvent(new TagPopupCloseEvent());
    });
    this.root.getElementById('name').addEventListener('input', (evt) => {
      this.name = evt.currentTarget.value;
      this.error = '';
    });
    this.root.getElementById('color').addEventListener('input', (evt) => {
      this.color = evt.currentTarget.value;
      this.error = '';
    });
    this.root.getElementById('emoji').addEventListener('input', (evt) => {
      this.emoji = evt.currentTarget.emoji;
      this.error = '';
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

if (!customElements.get('cms-tag-popup')) {
  customElements.define('cms-tag-popup', TagPopupElement);
}
