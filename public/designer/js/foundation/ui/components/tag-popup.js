import html from '../../../../lib/jinya-html.js';
import localize from '../../localize.js';
import './emoji-picker.js';
import TagPopupSubmitEvent from './events/TagPopupSubmitEvent.js';

class TagPopupElement extends HTMLElement {
  constructor() {
    super();

    this.root = this.attachShadow({ mode: 'closed' });
  }

  static get observedAttributes() {
    return ['popup-title', 'name', 'emoji', 'color', 'open', 'save-label', 'cancel-label', 'target'];
  }

  get title() {
    return this.getAttribute('popup-title');
  }

  set title(value) {
    this.setAttribute('popup-title', value);
    if (this.root.querySelector('legend')) {
      this.root.querySelector('legend').textContent = value;
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
    if (this.root?.getElementById('save-button')) {
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
    this.root.innerHTML = html`
            <style id="target-position"></style>
            <style>
                @import "/designer/cosmo/form.css";
                @import "/designer/css/form-addons.css";

                :host::selection {
                    background: var(--primary-color);
                }

                :host {
                    display: none;
                    background: var(--white);
                    position: fixed;
                    border: 1px solid var(--primary-color);
                    padding: 16px;
                    color: var(--primary-color);
                }

                :host:hover {
                    color: var(--primary-color);
                }

                :host::before {
                    content: '';
                    position: absolute;
                    left: 50%;
                    transform: translate(-50%);
                    border: 16px solid transparent;
                    border-bottom-color: var(--primary-color);
                    top: -32px;
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
                    font-size: 24px;
                    height: 24px;
                    font-weight: var(--font-weight-light);
                    text-transform: uppercase;
                    margin-top: 10px;
                    margin-bottom: 10px;
                }

                .cosmo-input--emoji::part(popup) {
                    margin-left: -8px;
                    margin-bottom: 4px;
                }
            </style>
            <form>
                <fieldset>
                    <legend>${this.title}</legend>
                    <div class="cosmo-input__group">
                        <label class="cosmo-label"
                               for="name">${localize({ key: 'media.files.tags.popup.name' })}</label>
                        <input type="text" class="cosmo-input" required id="name">
                        <label class="cosmo-label"
                               for="color">${localize({ key: 'media.files.tags.popup.color' })}</label>
                        <input type="color" value="${this.color}" class="cosmo-input" id="color">
                        <label class="cosmo-label"
                               for="emoji">${localize({ key: 'media.files.tags.popup.emoji' })}</label>
                        <cms-emoji-picker emoji="${this.emoji}" class="cosmo-input cosmo-input--emoji" id="emoji">
                    </div>
                    <div class="cosmo-button__container">
                        <button id="save-button" class="cosmo-button" type="submit">${this.saveLabel}</button>
                        <button id="cancel-button" class="cosmo-button" type="button">${this.cancelLabel}</button>
                    </div>
                </fieldset>
            </form>`;
    this.root.querySelector('form').addEventListener('submit', (evt) => {
      evt.preventDefault();
      this.dispatchEvent(new TagPopupSubmitEvent(this.name, this.color, this.emoji));
    });
    this.root.getElementById('cancel-button').addEventListener('click', (evt) => {
      evt.preventDefault();
      this.open = false;
    });
    this.root.getElementById('name').addEventListener('input', (evt) => {
      this.name = evt.currentTarget.value;
    });
    this.root.getElementById('color').addEventListener('input', (evt) => {
      this.color = evt.currentTarget.value;
    });
    this.root.getElementById('emoji').addEventListener('input', (evt) => {
      this.emoji = evt.currentTarget.emoji;
    });
  }

  attributeChangedCallback(property, oldValue, newValue) {
    if (oldValue === newValue) {
      return;
    }

    this[property] = newValue;
  }
}

customElements.define('cms-tag-popup', TagPopupElement);
