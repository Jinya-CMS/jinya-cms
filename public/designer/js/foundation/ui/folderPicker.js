import localize from '../utils/localize.js';
import { Alpine } from '../../../../lib/alpine.js';
import { getMediaDatabase } from '../database/media.js';

import './components/tag.js';
import './components/loader.js';

const mediaDatabase = getMediaDatabase();

Alpine.data('jinyaFolderPickerData', (selectedFolderId, ignoredFolders = []) => ({
  selectedFolderId,
  ignoredFolders,
  allFolders: [],
  folders: [],
  foldersWatcher: null,
  folderPath: [],
  loading: true,
  get filteredFolders() {
    return this.folders.filter((folder) => !ignoredFolders.includes(folder.id));
  },
  setupView() {
    this.selectedFolderId = null;

    const folderId = this.folderPath[this.folderPath.length - 1] ?? -1;

    this.foldersWatcher?.unsubscribe();

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
    this.loading = false;

    mediaDatabase.cacheMedia().then(() => {});
    this.$watch('folderPath', (path) => {
      this.setupView();
    });
  },
  getFolderById(folder) {
    return this.allFolders.find((f) => f.id === folder);
  },
  goBreadcrumb(index) {
    this.folderPath = this.folderPath.slice(0, index);
  },
  goFolder(id) {
    if (this.folderHasChildren(id)) {
      this.folderPath.push(id);
    }
  },
  folderHasChildren(id) {
    return this.allFolders.filter((folder) => folder.parentId === id).length > 0;
  },
  selectFolder(id) {
    this.selectedFolderId = id;
  },
}));

class FolderPickedEvent extends Event {
  constructor(folder) {
    super('pick', {
      bubbles: true,
      cancelable: false,
      composed: true,
    });
    this.folder = folder;
  }
}

class FolderPickerDismissedEvent extends Event {
  constructor() {
    super('dismiss', {
      bubbles: true,
      cancelable: false,
      composed: true,
    });
  }
}

class FolderPickerElement extends HTMLElement {
  constructor() {
    super();

    this.root = this.attachShadow({ mode: 'closed' });
  }

  get pickLabel() {
    if (this.hasAttribute('pick-label')) {
      return this.getAttribute('pick-label');
    }

    return localize({ key: 'folder_picker.pick' });
  }

  set pickLabel(value) {
    this.setAttribute('pick-label', value);
  }

  get cancelLabel() {
    if (this.hasAttribute('cancel-label')) {
      return this.getAttribute('cancel-label');
    }

    return localize({ key: 'folder_picker.dismiss' });
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

  get selectedFolder() {
    if (this.hasAttribute('selected-folder')) {
      return parseInt(this.getAttribute('selected-folder'));
    }

    return -1;
  }

  set selectedFolder(value) {
    this.setAttribute('selected-folder', value);
  }

  get ignoredFolders() {
    if (this.hasAttribute('ignored-folders')) {
      return this.getAttribute('ignored-folders');
    }

    return [];
  }

  set ignoredFolders(value) {
    this.setAttribute('selected-folders', value);
  }

  connectedCallback() {
    this.classList.add('cosmo-modal__container', 'is--folder-picker');
    this.root.innerHTML = `
      <style>
        @import "/lib/cosmo/modal.css";
        @import "/lib/cosmo/buttons.css";
        @import "/designer/css/media.css";
        
        .cosmo-modal__content.is--loading {
          display: flex;
          justify-content: center;
          align-items: center;
          min-width: 10rem;
          min-height: 10rem;
        }
      </style>
      <div class="cosmo-modal is--folder-picker">
        <h1 class="cosmo-modal__title">${this.title}</h1>
        <div class="cosmo-modal__content is--folder-picker" :class="{ 'is--loading': loading }" x-data="jinyaFolderPickerData(${this.selectedFolder}, [${this.ignoredFolders}])">
          <template x-if="loading">
            <cms-loader></cms-loader>
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
                    <a @click="goBreadcrumb(index + 1)" class="jinya-media-tile-path__entry" 
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
              <div class="jinya-media-tile__container is--modal" :data-selected-folder="selectedFolderId" id="folderList">
                <div class="jinya-media-tile__container is--folders">
                  <template :key="folder.id" x-for="folder in filteredFolders">
                    <figure @dblclick="goFolder(folder.id)" @click="selectFolder(folder.id)" class="jinya-media-tile is--folder">
                      <svg viewBox="0 0 24 24" class="jinya-media-tile__check is--folder is--unchecked"
                           :class="{ 'is--hidden': selectedFolderId === folder.id }">
                        <rect width="18" height="18" x="3" y="3" rx="2" />
                      </svg>
                      <svg viewBox="0 0 24 24" class="jinya-media-tile__check is--folder"
                           :class="{ 'is--checked': selectedFolderId === folder.id }">
                        <rect width="18" height="18" x="3" y="3" rx="2" />
                        <path d="m9 12 2 2 4-4" />
                      </svg>
                      <svg viewBox="0 0 24 18" class="jinya-media-tile__folder">
                        <path
                          d="m 19.986935,16.983667 c 1.102766,0 1.996734,-0.926429 1.996734,-2.069235 V 8.1894182 c 0,-1.142807 -0.893968,-2.0692351 -1.996734,-2.0692351 H 12.099836 C 11.421279,6.1270774 10.785788,5.7763622 10.412597,5.1890273 L 9.6039194,3.9474864 C 9.2346009,3.3663207 8.6081345,3.0164452 7.9366465,3.0163306 h -3.923582 c -1.1027657,0 -1.9967339,0.9264281 -1.9967339,2.069235 v 9.8288664 c 0,1.142806 0.8939682,2.069235 1.9967339,2.069235 z" />
                      </svg>
                      <figcaption class="jinya-media-tile__title" x-text="folder.name"></figcaption>
                    </figure>
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

      this.dispatchEvent(new FolderPickerDismissedEvent());
    });
    this.root.getElementById('pick').addEventListener('click', async (e) => {
      e.preventDefault();
      const selectedFolderId = this.root.getElementById('folderList').getAttribute('data-selected-folder');
      const folder = await mediaDatabase.getFolderById(parseInt(selectedFolderId));

      this.dispatchEvent(new FolderPickedEvent(folder));
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

if (!customElements.get('cms-folder-picker')) {
  customElements.define('cms-folder-picker', FolderPickerElement);
}

/**
 * Displays a folder picker modal dialog
 * @return {Promise<boolean|{}>}
 */
export default async function folderPicker({
  title = window.location.href,
  selectedFolderId = -1,
  ignoredFolders = [],
  cancelLabel,
  pickLabel,
}) {
  return new Promise((resolve) => {
    const container = document.createElement('div');
    document.body.appendChild(container);

    container.innerHTML = `<cms-folder-picker title="${title}" ${cancelLabel ? `cancel-label="${cancelLabel}"` : ''} ${pickLabel ? `pick-label="${pickLabel}"` : ''} selected-folder="${selectedFolderId ?? -1}" ignored-folders="${ignoredFolders.join(',')}"></cms-folder-picker>`;

    const folderPicker = container.querySelector('cms-folder-picker');
    folderPicker.addEventListener('dismiss', (e) => {
      e.preventDefault();
      container.remove();
      resolve(null);
    });
    folderPicker.addEventListener('pick', (e) => {
      e.preventDefault();
      container.remove();
      resolve(e.folder);
    });
  });
}
