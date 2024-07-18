import localize from '../utils/localize.js';
import { Alpine } from '../../../../lib/alpine.js';
import { getFileDatabase } from '../database/file.js';
import { getMediaDatabase } from '../database/media.js';

import './components/tag.js';
import './components/loader.js';

const fileDatabase = getFileDatabase();
const mediaDatabase = getMediaDatabase();

Alpine.data('jinyaFilePickerData', (selectedFileId) => ({
  selectedFileId,
  selectedTags: new Set(),
  tags: [],
  allFolders: [],
  folders: [],
  files: [],
  filesWatcher: null,
  foldersWatcher: null,
  folderPath: [],
  loading: true,
  get filteredFiles() {
    if (this.selectedTags.size === 0) {
      return this.files;
    }

    return this.files.filter((file) => file.tags.filter((tag) => this.selectedTags.has(tag.id)).length > 0);
  },
  setupView() {
    this.selectedFileId = null;

    const folderId = this.folderPath[this.folderPath.length - 1] ?? -1;

    this.filesWatcher?.unsubscribe();
    this.foldersWatcher?.unsubscribe();

    this.filesWatcher = mediaDatabase.watchFiles(folderId).subscribe({
      next: (files) => {
        this.files = files;
      },
    });
    this.foldersWatcher = mediaDatabase.watchFolders(folderId).subscribe({
      next: (folders) => {
        this.folders = folders;
      },
    });
    this.foldersWatcher = mediaDatabase.watchFolders().subscribe({
      next: (folders) => {
        this.allFolders = folders;
      },
    });
  },
  init() {
    this.setupView();

    mediaDatabase.watchTags().subscribe({
      next: (tags) => {
        this.tags = tags;
      },
    });
    this.loading = false;

    mediaDatabase.cacheMedia().then(() => {});
    this.$watch('folderPath', () => this.setupView());
  },
  getFolderById(folder) {
    return this.allFolders.find((f) => f.id === folder);
  },
  goBreadcrumb(index) {
    this.folderPath = this.folderPath.slice(0, index);
  },
  goFolder(id) {
    this.folderPath.push(id);
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
        <div class="cosmo-modal__content is--file-picker" :class="{ 'is--loading': loading }" x-data="jinyaFilePickerData(${this.selectedFile})">
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
            <div style="display: contents">   
              <nav class="jinya-media-tile-path">
                <a @click="goBreadcrumb(0)" class="jinya-media-tile-path__entry is--home"
                   :class="{ 'is--last': folderPath.length === 0 }">
                  <svg width="1.5rem" height="1.5rem" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                       stroke-linecap="round" stroke-linejoin="round">
                    <path d="M15 21v-8a1 1 0 0 0-1-1h-4a1 1 0 0 0-1 1v8" />
                    <path
                      d="M3 10a2 2 0 0 1 .709-1.528l7-5.999a2 2 0 0 1 2.582 0l7 5.999A2 2 0 0 1 21 10v9a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z" />
                  </svg>
                  <span x-localize:media-files-path-home></span>
                </a>
                <template x-if="folderPath.length > 0">
                  <span class="jinya-media-tile-path__entry is--separator">
                    <svg width="1rem" height="1rem" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                         stroke-linecap="round" stroke-linejoin="round"><path d="m9 18 6-6-6-6" /></svg>
                  </span>
                </template>
                <template x-for="(folder, index) in folderPath" :key="folder">
                  <div style="display: contents">
                    <a @click="goBreadcrumb(index)" class="jinya-media-tile-path__entry" 
                       :class="{ 'is--last': index + 1 === folderPath.length }" x-text="getFolderById(folder).name">
                    </a>
                    <template x-if="index < folderPath.length - 1">
                      <span class="jinya-media-tile-path__entry is--separator">
                        <svg width="1rem" height="1rem" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                             stroke-linecap="round" stroke-linejoin="round"><path d="m9 18 6-6-6-6" /></svg>
                      </span>
                    </template>
                  </div>
                </template>
              </nav>
              <div class="jinya-media-tile__container is--modal" :data-selected-file="selectedFileId" id="fileList">
                <template x-if="folders.length > 0">
                  <div class="jinya-media-tile__container is--folders">
                    <template :key="folder.id" x-for="folder in folders">
                      <figure @dblclick="goFolder(folder.id)" class="jinya-media-tile is--folder is--medium">
                        <svg viewBox="0 0 24 18" class="jinya-media-tile__folder is--medium">
                          <path
                            d="m 19.986935,16.983667 c 1.102766,0 1.996734,-0.926429 1.996734,-2.069235 V 8.1894182 c 0,-1.142807 -0.893968,-2.0692351 -1.996734,-2.0692351 H 12.099836 C 11.421279,6.1270774 10.785788,5.7763622 10.412597,5.1890273 L 9.6039194,3.9474864 C 9.2346009,3.3663207 8.6081345,3.0164452 7.9366465,3.0163306 h -3.923582 c -1.1027657,0 -1.9967339,0.9264281 -1.9967339,2.069235 v 9.8288664 c 0,1.142806 0.8939682,2.069235 1.9967339,2.069235 z" />
                        </svg>
                        <figcaption class="jinya-media-tile__title" x-text="folder.name"></figcaption>
                      </figure>
                    </template>
                  </div>
                </template>
                <div class="jinya-media-tile__container is--files">
                  <template :key="file.id" x-for="file in filteredFiles">
                    <div
                      :data-title="file.id + ' ' + file.name"
                      @click="selectFile(file.id)"
                      class="jinya-media-tile is--medium"
                    >
                      <svg viewBox="0 0 24 24" class="jinya-media-tile__check is--file is--unchecked"
                           :class="{ 'is--hidden': selectedFileId === file.id }">
                        <rect width="18" height="18" x="3" y="3" rx="2" />
                      </svg>
                      <svg viewBox="0 0 24 24" class="jinya-media-tile__check is--file"
                           :class="{ 'is--checked': selectedFileId === file.id }">
                        <rect width="18" height="18" x="3" y="3" rx="2" />
                        <path d="m9 12 2 2 4-4" />
                      </svg>
                      <div class="jinya-media-tile__img">
                        <img :alt="file.name" :src="file.path" />
                      </div>
                    </div>
                  </template>
                </div>
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
      const file = await mediaDatabase.getFileById(parseInt(selectedFileId));

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
