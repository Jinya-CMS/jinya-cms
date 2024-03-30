import Emojis from '../../../../lib/emojis.js';
import html from '../../../../lib/jinya-html.js';

class EmojiPickerElement extends HTMLElement {
  constructor() {
    super();
    this.setActiveTabAndEmoji = this.setActiveTabAndEmoji.bind(this);
    this.clickOutside = this.clickOutside.bind(this);

    this.internals = this.attachInternals();
    this.root = this.attachShadow({ mode: 'closed' });
  }

  static get formAssociated() {
    return true;
  }

  static get observedAttributes() {
    return ['emoji', 'open'];
  }

  get emoji() {
    return this.getAttribute('emoji');
  }

  set emoji(value) {
    this.internals.setFormValue(value);
    this.setAttribute('emoji', value);
    if (this.root?.getElementById('emoji-picker-open-button')) {
      this.root.getElementById('emoji-picker-open-button').textContent = value;
    }
    this.setActiveTabAndEmoji(value);
  }

  get open() {
    return this.hasAttribute('open');
  }

  set open(value) {
    if (value) {
      this.setAttribute('open', value);
    } else {
      this.removeAttribute('open');
    }
  }

  connectedCallback() {
    this.renderButton();
  }

  disconnectedCallback() {
    document.removeEventListener('click', this.clickOutside);
  }

  attributeChangedCallback(property, oldValue, newValue) {
    if (oldValue === newValue) {
      return;
    }

    this[property] = newValue;
  }

  setActiveTabAndEmoji(emoji) {
    const activeInput = this.root.querySelector(`input[value=${emoji}]`);
    if (activeInput) {
      activeInput.checked = true;
    }

    const category = Emojis.find((item) => item.emojis.find((e) => e === emoji));
    if (category) {
      this.root
        .querySelectorAll('.emoji-picker__tab-item--active')
        .forEach((tab) => tab.classList.remove('emoji-picker__tab-item--active'));
      this.root
        .querySelectorAll('.emoji-picker__tab-content--active')
        .forEach((tab) => tab.classList.remove('emoji-picker__tab-content--active'));
      this.root.querySelector(`[data-tab="${category.name}"]`)
        ?.classList
        .add('emoji-picker__tab-content--active');
      this.root.querySelector(`[title="${category.name}"]`)
        ?.classList
        .add('emoji-picker__tab-item--active');
    }
  }

  renderButton() {
    this.root.innerHTML = html`
      <style>
        :host {
          height: auto;
        }

        :host([open]) .emoji-picker__popup {
          display: grid;
        }

        span::selection {
          background: var(--primary-color);
        }

        .emoji-picker__popup {
          display: none;
          grid-template-rows: [tab-bar] auto [content] 1fr;
          height: 9.5rem;
          position: fixed;
          background: var(--white);
          overflow-y: hidden;
          border: 0.0625rem solid var(--primary-color);
          backdrop-filter: var(--modal-backdrop-filter);
          border-radius: var(--border-radius);
        }

        .emoji-picker__tab-bar {
          display: flex;
          flex-flow: row nowrap;
          margin-bottom: 1rem;
        }

        .emoji-picker__tab-item {
          flex: 1 1 1.5rem;
          cursor: pointer;
          position: relative;
          font-size: 1rem;
          text-align: center;
          height: 1.5rem;
          color: var(--primary-color);
        }

        .emoji-picker__tab-item:hover:after,
        .emoji-picker__tab-item--active::after {
          content: '';
          position: absolute;
          bottom: 0;
          left: 0.125rem;
          right: 0.125rem;
          height: 0.125rem;
          background: var(--primary-color);
          color: var(--white);
        }

        .emoji-picker__tab-content {
          display: none;
          grid-template-columns: repeat(10, 1.25rem);
          grid-auto-rows: 1.25rem;
          grid-auto-flow: row;
          gap: 0.25rem;
          height: 100%;
          overflow-y: auto;
          padding: 0.5rem;
          box-sizing: border-box;
        }

        .emoji-picker__tab-content--active {
          display: grid;
        }

        input[type='radio'] {
          appearance: none;
          margin: 0;
          display: grid;
          place-content: center;
          cursor: pointer;
        }

        input[type='radio']::before {
          content: attr(value);
          font-size: 1rem;
          width: 1.5rem;
          height: 1.5rem;
          color: var(--primary-color);
          text-align: center;
        }

        input[type='radio']:hover::before {
          background: var(--primary-color);
          color: var(--white);
        }

        input[type='radio']:checked::before {
          background: var(--primary-color);
          color: var(--white);
        }
      </style>
      <span id="emoji-picker-open-button">${this.emoji ?? ''}</span>
      <div class="emoji-picker__popup" part="popup">
        <div class="emoji-picker__tab-bar">
          ${Emojis.map((item, idx) => html`
            <a
              class="emoji-picker__tab-item ${idx === 0 ? 'emoji-picker__tab-item--active' : ''}"
              title="${item.name}"
            >${item.emojis[0]}</a
            >
          `)}
        </div>
        ${Emojis.map((item, idx) => html`
          <div
            data-tab="${item.name}"
            class="emoji-picker__tab-content ${idx === 0 ? 'emoji-picker__tab-content--active' : ''}"
          >
            ${item.emojis.map((emoji) => html` <input value="${emoji}" type="radio" name="emoji"
                                                      class="emoji-picker__button" /> `)}
          </div>
        `)}
      </div>
    `;
    this.addEventListener('click', (evt) => {
      evt.stopPropagation();
      this.open = !this.open;
    });
    this.root.querySelectorAll('input')
      .forEach((elem) => {
        elem.addEventListener('click', (evt) => {
          const newValue = evt.currentTarget.value;
          this.internals.setFormValue(newValue);
          this.dispatchEvent(new InputEvent('input'));
          this.emoji = newValue;
        });
      });
    this.root.querySelectorAll('.emoji-picker__tab-item')
      .forEach((elem) => {
        elem.addEventListener('click', (evt) => {
          evt.stopPropagation();
          this.root
            .querySelectorAll('.emoji-picker__tab-item--active')
            .forEach((tab) => tab.classList.remove('emoji-picker__tab-item--active'));
          this.root
            .querySelectorAll('.emoji-picker__tab-content--active')
            .forEach((tab) => tab.classList.remove('emoji-picker__tab-content--active'));
          evt.currentTarget.classList.add('emoji-picker__tab-item--active');
          this.root
            .querySelector(`[data-tab="${evt.currentTarget.title}"]`)
            .classList
            .add('emoji-picker__tab-content--active');
        });
      });
    document.addEventListener('click', this.clickOutside);
  }

  clickOutside(evt) {
    if (this.open && !evt?.composedPath()
      .includes(this)) {
      this.open = false;
    }
  }
}

customElements.define('cms-emoji-picker', EmojiPickerElement);
