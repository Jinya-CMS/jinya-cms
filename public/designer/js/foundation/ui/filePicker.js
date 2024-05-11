import localize from '../utils/localize.js';
import { getFiles, getTags } from '../api/files.js';
import { Alpine } from '../../../../lib/alpine.js';
import { getFileDatabase } from '../database/file.js';

import './components/tag.js';
import './components/loader.js';

const fileDatabase = getFileDatabase();

Alpine.data('jinyaFilePickerData', (selectedFileId) => ({
  selectedFileId,
  selectedTags: new Set(),
  tags: [],
  files: [],
  loading: true,
  get filteredFiles() {
    if (this.selectedTags.size === 0) {
      return this.files;
    }

    return this.files.filter((file) => file.tags.filter((tag) => this.selectedTags.has(tag.id)).length > 0);
  },
  async init() {
    fileDatabase.watchFiles().subscribe({
      next: (files) => {
        this.files = files;
        this.loading = false;
      },
    });

    this.tags = await fileDatabase.getAllTags();

    const [files, tags] = await Promise.all([getFiles(), getTags()]);
    this.files = files.items;
    this.tags = tags.items;
    this.loading = false;

    fileDatabase.replaceFiles(files.items);
    fileDatabase.replaceTags(tags.items);
  },
  toggleTag(id) {
    if (this.selectedTags.has(id)) {
      this.selectedTags.delete(id);
    } else {
      this.selectedTags.add(id);
    }
  },
  clearTags() {
    this.selectedTags.clear();
  },
  selectFile(id) {
    this.selectedFileId = id;
  },
}));

class FilePickedEvent extends Event {
  constructor(file) {
    super('pick', {
      bubbles: true,
      cancelable: false,
      composed: true,
    });
    this.file = file;
  }
}

class FilePickerDismissedEvent extends Event {
  constructor() {
    super('dismiss', {
      bubbles: true,
      cancelable: false,
      composed: true,
    });
  }
}

class FilePickerElement extends HTMLElement {
  constructor() {
    super();

    this.root = this.attachShadow({ mode: 'closed' });
  }

  get pickLabel() {
    if (this.hasAttribute('pick-label')) {
      return this.getAttribute('pick-label');
    }

    return localize({ key: 'file_picker.pick' });
  }

  set pickLabel(value) {
    this.setAttribute('pick-label', value);
  }

  get cancelLabel() {
    if (this.hasAttribute('cancel-label')) {
      return this.getAttribute('cancel-label');
    }

    return localize({ key: 'file_picker.dismiss' });
  }

  set cancelLabel(value) {
    this.setAttribute('cancel-label', value);
  }

  get title() {
    if (this.hasAttribute('title')) {
      return this.getAttribute('title');
    }

    return window.location.href;
  }

  set title(value) {
    this.setAttribute('title', value);
  }

  get selectedFile() {
    if (this.hasAttribute('selected-file')) {
      return parseInt(this.getAttribute('selected-file'));
    }

    return -1;
  }

  set selectedFile(value) {
    this.setAttribute('selected-file', value);
  }

  connectedCallback() {
    this.classList.add('cosmo-modal__container', 'is--file-picker');
    this.root.innerHTML = `
      <style>
        @import "/lib/cosmo/modal.css";
        @import "/lib/cosmo/buttons.css";
        @import "/designer/css/media.css";
        @import "/designer/css/file-picker.css";
        
        .cosmo-modal__content.is--loading {
          display: flex;
          justify-content: center;
          align-items: center;
          min-width: 10rem;
          min-height: 10rem;
        }
      </style>
      <div class="cosmo-modal is--file-picker">
        <h1 class="cosmo-modal__title">${this.title}</h1>
        <div class="cosmo-modal__content" :class="{ 'is--loading': loading }" x-data="jinyaFilePickerData(${this.selectedFile})">
          <template x-if="loading">
            <cms-loader></cms-loader>
          </template>
          <template x-if="!loading">
            <div class="jinya-picker__tag-list">
              <cms-tag @click="clearTags" :active="selectedTags.size === 0" class="jinya-tag is--file" emoji="" name="${localize({ key: 'media.galleries.action.show_all_tags' })}" color="#19324c"></cms-tag>
              <template x-for="tag of tags">
                <cms-tag @click="toggleTag(tag.id)" :active="selectedTags.has(tag.id)" class="jinya-tag is--file" :emoji="tag.emoji" :name="tag.name" :color="tag.color" :tag-id="tag.id"></cms-tag>
              </template>
            </div>
          </template>
          <template x-if="!loading">
            <div class="jinya-media-tile__container is--modal" :data-selected-file="selectedFileId" id="fileList">
              <template x-for="file of filteredFiles">
                <div class="jinya-media-tile is--small" :class="{ 'is--selected': selectedFileId === file.id }">
                  <img @click="selectFile(file.id)" class="jinya-media-tile__img is--small" :src="file.path" :alt="file.name" />
                </div>
              </template>
            </div>
          </template>
        </div>
        <div class="cosmo-modal__button-bar">
          <button id="cancel" class="cosmo-button">${this.cancelLabel}</button>
          <button id="pick" class="cosmo-button">${this.pickLabel}</button>
        </div>
      </div>`;
    this.root.getElementById('cancel').addEventListener('click', (e) => {
      e.preventDefault();

      this.dispatchEvent(new FilePickerDismissedEvent());
    });
    this.root.getElementById('pick').addEventListener('click', async (e) => {
      e.preventDefault();
      const selectedFileId = this.root.getElementById('fileList').getAttribute('data-selected-file');
      const file = await fileDatabase.getFileById(parseInt(selectedFileId));

      this.dispatchEvent(new FilePickedEvent(file));
    });
    Alpine.initTree(this.root);
  }

  attributeChangedCallback(property, oldValue, newValue) {
    if (oldValue === newValue) {
      return;
    }

    const propertyName = property.replace(/-([a-z])/g, (m, w) => w.toUpperCase());
    this[propertyName] = newValue;
  }
}

if (!customElements.get('cms-file-picker')) {
  customElements.define('cms-file-picker', FilePickerElement);
}

/**
 * Displays a file picker modal dialog
 * @param title {string}
 * @param message {string}
 * @param cancelLabel {string}
 * @param pickLabel {string}
 * @return {Promise<boolean|{}>}
 */
export default async function filePicker({
  title = window.location.href,
  selectedFileId = -1,
  cancelLabel,
  pickLabel,
}) {
  return new Promise((resolve) => {
    const container = document.createElement('div');
    document.body.appendChild(container);

    container.innerHTML = `<cms-file-picker title="${title}" ${cancelLabel ? `cancel-label="${cancelLabel}"` : ''} ${pickLabel ? `pick-label="${pickLabel}"` : ''} selected-file="${selectedFileId ?? -1}"></cms-file-picker>`;

    const filePicker = container.querySelector('cms-file-picker');
    filePicker.addEventListener('dismiss', (e) => {
      e.preventDefault();
      container.remove();
      resolve(null);
    });
    filePicker.addEventListener('pick', (e) => {
      e.preventDefault();
      container.remove();
      resolve(e.file);
    });
  });
}
