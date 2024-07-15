import { Alpine } from '../../../../lib/alpine.js';
import localize from '../../foundation/utils/localize.js';
import MimeTypes from '../../../lib/mime/types.js';
import {
  createFile,
  createTag,
  deleteFile,
  deleteTag,
  getFile,
  tagFile,
  updateFile,
  updateTag,
  uploadFile,
} from '../../foundation/api/files.js';
import { getRandomEmoji } from '../../foundation/utils/text.js';
import confirm from '../../foundation/ui/confirm.js';
import alert from '../../foundation/ui/alert.js';
import { getRandomColor, lightenDarkenColor } from '../../foundation/utils/color.js';
import { getFileDatabase } from '../../foundation/database/file.js';

import '../../foundation/ui/components/tag.js';
import '../../foundation/ui/components/tag-popup.js';
import { getMediaDatabase } from '../../foundation/database/media.js';

const fileDatabase = getFileDatabase();
const mediaDatabase = getMediaDatabase();

Alpine.data('filesData', () => ({
  allFolders: [],
  folders: [],
  files: [],
  folderPath: [],
  activeTags: new Set(),
  tags: [],
  selectedFiles: new Set(),
  selectedFile: 0,
  filesWatcher: null,
  foldersWatcher: null,
  loading: true,
  uploadMenuOpen: false,
  defaultTagName: localize({ key: 'media.files.action.show_all_tags' }),
  get selectedFolderId() {
    let folderId = null;
    if (this.folderPath.length > 0) {
      folderId = this.folderPath[this.folderPath.length - 1];
    }

    return folderId;
  },
  get filteredFiles() {
    if (this.activeTags.size === 0) {
      return this.files;
    }

    return this.files.files.filter((file) => file.tags.filter((tag) => this.activeTags.has(tag.id)).length > 0);
  },
  get randomColor() {
    return getRandomColor();
  },
  get randomEmoji() {
    return getRandomEmoji();
  },
  get tagPopupTitle() {
    return localize({ key: 'media.files.tags.new.title' });
  },
  get tagPopupSaveLabel() {
    return localize({ key: 'media.files.tags.new.save' });
  },
  get tagPopupCancelLabel() {
    return localize({ key: 'media.files.tags.new.cancel' });
  },
  get selectedFilesDetails() {
    const files = [];
    for (const selectedFile of this.selectedFiles) {
      files.push(this.files.find((file) => file.id === selectedFile));
    }

    return files;
  },
  get detailsFileType() {
    const file = this.selectedFileDetails;
    if (!file) {
      return '';
    }

    const { type } = file;
    if (type.startsWith('font')) {
      return localize({ key: 'media.files.details.types.font' });
    }

    const localizedType = localize({ key: `media.files.details.types.${type}` });
    if (localizedType === `media.files.details.types.${type}`) {
      return type;
    }

    return localizedType;
  },
  get selectedFileDetails() {
    return this.selectedFilesDetails[this.selectedFile];
  },
  setupView() {
    this.selectedFiles.clear();
    this.selectedFile = 0;

    const folderId = this.selectedFolderId ?? -1;

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
  async init() {
    this.setupView();

    mediaDatabase.watchTags().subscribe({
      next: (tags) => {
        this.tags = tags;
      },
    });

    await mediaDatabase.cacheMedia();
    this.loading = false;

    this.$watch('uploadSingleFile.file', (file) => {
      if (this.uploadSingleFile.name === '') {
        this.uploadSingleFile.name = file.name.split('.').reverse().slice(1).reverse().join('.');
      }
    });
  },
  clearTags() {
    this.activeTags.clear();
  },
  toggleTag(tag) {
    if (this.activeTags.has(tag.id)) {
      this.activeTags.delete(tag.id);
    } else {
      this.activeTags.add(tag.id);
    }
  },
  getTagStyle(tag) {
    if (tag?.color) {
      return `--tag-color: ${tag.color}; --tag-after-color: #${lightenDarkenColor(tag.color, -20)}`;
    }

    return '--tag-color: var(--primary-color); --tag-after-color: var(--primary-color-dark)';
  },
  getFolderById(folder) {
    return this.allFolders.find((f) => f.id === folder);
  },
  openUploadSingleFileDialog() {
    this.uploadSingleFile.error.reset();
    this.uploadSingleFile.open = true;
    this.uploadSingleFile.name = '';
    this.uploadSingleFile.tags = new Set(this.tags.filter((tag) => this.activeTags.has(tag.id)).map((tag) => tag.name));
  },
  openUploadMultipleFilesDialog() {
    this.uploadMultipleFiles.error.reset();
    this.uploadMultipleFiles.open = true;
    this.uploadMultipleFiles.files = [];
    this.uploadMultipleFiles.tags = new Set(
      this.tags.filter((tag) => this.activeTags.has(tag.id)).map((tag) => tag.name),
    );
  },
  openEditFileDialog() {
    this.edit.error.reset();
    this.edit.open = true;
    this.edit.name = this.selectedFileDetails.name;
    this.edit.tags = new Set(this.selectedFileDetails.tags.map((tag) => tag.name));
    this.edit.id = this.selectedFileDetails.id;
  },
  openManageTagsDialog() {
    this.manageTags.error.reset();
    this.manageTags.open = true;
    this.manageTags.tags = [...this.tags];
  },
  openTagMultipleDialog() {
    this.tagMultiple.error.reset();
    this.tagMultiple.open = true;
    this.tagMultiple.tags.clear();
  },
  openEditTag(id) {
    this.manageTags.error.reset();
    this.manageTags.editOpenId = id;
  },
  downloadSelectedFiles() {
    for (const file of this.selectedFilesDetails) {
      const name = `${file.name}.${MimeTypes[file.type]?.extensions[0] ?? ''}`;
      const a = document.createElement('a');
      a.href = file.path;
      a.download = name;
      a.style.display = 'none';
      a.target = '_blank';
      document.body.append(a);
      a.click();
      a.remove();
    }
  },
  selectFile(evt, id) {
    const insert = evt.ctrlKey;
    if (insert) {
      if (this.selectedFiles.has(id)) {
        this.selectedFile = 0;
        this.selectedFiles.delete(id);
      } else {
        this.selectedFiles.add(id);
      }
    } else {
      this.selectedFile = 0;
      this.selectedFiles.clear();
      this.selectedFiles.add(id);
    }
  },
  goHome() {
    this.folderPath = [];
    this.setupView();
  },
  goBreadcrumb(index) {
    this.folderPath = this.folderPath.slice(0, index + 1);
    this.setupView();
  },
  goFolder(id) {
    this.folderPath.push(id);
    this.setupView();
  },
  async deleteTag(tag) {
    const confirmed = await confirm({
      title: localize({ key: 'media.files.tags.delete.title' }),
      message: localize({
        key: 'media.files.tags.delete.message',
        values: tag,
      }),
      declineLabel: localize({ key: 'media.files.tags.delete.decline' }),
      approveLabel: localize({ key: 'media.files.tags.delete.approve' }),
      negative: true,
    });

    if (confirmed) {
      try {
        await deleteTag(tag.id);
        await mediaDatabase.deleteTag(tag.id);

        this.activeTags.delete(tag.id);
        this.manageTags.tags = this.tags;
      } catch (e) {
        await alert({
          title: localize({ key: 'media.files.tags.delete.error.title' }),
          message: localize({ key: 'media.files.tags.delete.error.generic' }),
          buttonLabel: localize({ key: 'media.files.tags.delete.error.close' }),
          negative: true,
        });
      }
    }
  },
  async deleteFile() {
    const confirmed = await confirm({
      title: localize({ key: 'media.files.delete.title' }),
      message: localize({
        key: 'media.files.delete.message',
        values: { name: this.selectedFileDetails.name },
      }),
      declineLabel: localize({ key: 'media.files.delete.decline' }),
      approveLabel: localize({ key: 'media.files.delete.approve' }),
      negative: true,
    });

    if (confirmed) {
      try {
        await deleteFile(this.selectedFileDetails.id);
        await mediaDatabase.deleteFile(this.selectedFileDetails.id);
        this.selectedFiles.delete(this.selectedFileDetails.id);
      } catch (e) {
        if (e.status === 409) {
          await alert({
            title: localize({ key: 'media.files.delete.error.title' }),
            message: localize({
              key: 'media.files.delete.error.conflict',
              values: { name: this.selectedFileDetails.name },
            }),
            negative: true,
          });
        } else {
          await alert({
            title: localize({ key: 'media.files.delete.error.title' }),
            message: localize({ key: 'media.files.delete.error.generic' }),
            negative: true,
          });
        }
      }
    }
  },
  async deleteMultipleFiles() {
    const fileCount = this.selectedFilesDetails.length;
    const confirmed = await confirm({
      title: localize({ key: 'media.files.delete_multiple.title' }),
      message: localize({
        key: 'media.files.delete_multiple.message',
        values: { count: fileCount },
      }),
      declineLabel: localize({ key: 'media.files.delete_multiple.decline' }),
      approveLabel: localize({ key: 'media.files.delete_multiple.approve' }),
      negative: true,
    });

    if (confirmed) {
      try {
        const promises = this.selectedFilesDetails.map(async (file) => {
          await deleteFile(file.id);
          await mediaDatabase.deleteFile(file.id);
        });
        await Promise.all(promises);
        this.selectedFiles.clear();
      } catch (e) {
        await alert({
          title: localize({ key: 'media.files.delete_multiple.error.title' }),
          message: localize({
            key: 'media.files.delete_multiple.error.message',
            values: { count: fileCount },
          }),
          negative: true,
        });
      }
    }
  },
  async uploadFile() {
    try {
      this.uploadSingleFile.error.reset();
      const savedFile = await createFile(
        this.uploadSingleFile.name,
        [...this.uploadSingleFile.tags],
        this.selectedFolderId,
        this.uploadSingleFile.file,
      );
      await mediaDatabase.saveFile(savedFile);
      this.uploadSingleFile.open = false;
      if (this.selectedFiles.size > 1) {
        this.selectedFiles.add(savedFile.id);
      } else {
        this.selectedFiles.clear();
        this.selectedFiles.add(savedFile.id);
      }
    } catch (err) {
      this.uploadSingleFile.error.title = localize({ key: 'media.files.upload_single_file.error.title' });
      this.uploadSingleFile.error.hasError = true;
      if (err.status === 409) {
        this.uploadSingleFile.error.message = localize({ key: 'media.files.upload_single_file.error.conflict' });
      } else {
        this.uploadSingleFile.error.message = localize({ key: 'media.files.upload_single_file.error.generic' });
      }
    }
  },
  async enqueueFiles() {
    const tags = Alpine.raw(this.uploadMultipleFiles.tags);
    await fileDatabase.queueFilesForUpload(
      [...Alpine.raw(this.uploadMultipleFiles.files)].map((file) => ({
        data: file,
        name: file.name.split('.').reverse().slice(1).reverse().join('.'),
        tags: [...tags],
        folderId: this.selectedFolderId,
      })),
    );
    this.uploadMultipleFiles.open = false;
  },
  async updateFile() {
    try {
      this.edit.error.reset();
      await updateFile(this.edit.id, this.edit.name, [...this.edit.tags]);
      if (this.edit.file) {
        await uploadFile(this.edit.id, this.edit.file);
      }

      const savedFile = await getFile(this.edit.id);
      await mediaDatabase.saveFile(savedFile);
      this.edit.open = false;
      if (this.selectedFiles.size > 1) {
        this.selectedFiles.add(savedFile.id);
      } else {
        this.selectedFiles.clear();
        this.selectedFiles.add(savedFile.id);
      }
    } catch (err) {
      if (err.status === 409) {
        this.edit.error.title = localize({ key: 'media.files.edit.error.title' });
        this.edit.error.message = localize({ key: 'media.files.edit.error.conflict' });
        this.edit.error.hasError = true;
      } else {
        this.edit.error.title = localize({ key: 'media.files.edit.error.title' });
        this.edit.error.message = localize({ key: 'media.files.edit.error.generic' });
        this.edit.error.hasError = true;
      }
    }
  },
  async createUploadSingleFileTag(event) {
    try {
      this.uploadSingleFile.error.tagError = '';
      const tag = await createTag(event.name, event.emoji, event.color);
      await mediaDatabase.saveTag(tag);
      this.uploadSingleFile.toggleTag(tag);
      this.uploadSingleFile.tagPopupOpen = false;
    } catch (e) {
      if (e.status === 409) {
        this.uploadSingleFile.error.tagError = localize({ key: 'media.files.tags.new.error.exists' });
      } else {
        await alert({
          title: localize({ key: 'media.files.tags.new.error.title' }),
          message: localize({ key: 'media.files.tags.new.error.generic' }),
          buttonLabel: localize({ key: 'media.files.tags.new.error.close' }),
          negative: true,
        });
      }
    }
  },
  async createUploadMultipleFilesTag(event) {
    try {
      this.uploadMultipleFiles.error.tagError = '';
      const tag = await createTag(event.name, event.emoji, event.color);
      await mediaDatabase.saveTag(tag);
      this.uploadMultipleFiles.toggleTag(tag);
      this.uploadMultipleFiles.tagPopupOpen = false;
    } catch (e) {
      if (e.status === 409) {
        this.uploadMultipleFiles.error.tagError = localize({ key: 'media.files.tags.new.error.exists' });
      } else {
        await alert({
          title: localize({ key: 'media.files.tags.new.error.title' }),
          message: localize({ key: 'media.files.tags.new.error.generic' }),
          buttonLabel: localize({ key: 'media.files.tags.new.error.close' }),
          negative: true,
        });
      }
    }
  },
  async createManageTagsTag(event) {
    try {
      this.manageTags.error.tagError = '';
      const tag = await createTag(event.name, event.emoji, event.color);
      await mediaDatabase.saveTag(tag);
      this.manageTags.tags = this.tags;
      this.manageTags.tagPopupOpen = false;
    } catch (e) {
      if (e.status === 409) {
        this.manageTags.error.tagError = localize({ key: 'media.files.tags.new.error.exists' });
      } else {
        await alert({
          title: localize({ key: 'media.files.tags.new.error.title' }),
          message: localize({ key: 'media.files.tags.new.error.generic' }),
          buttonLabel: localize({ key: 'media.files.tags.new.error.close' }),
          negative: true,
        });
      }
    }
  },
  async createEditTag(event) {
    try {
      this.edit.error.tagError = '';
      const tag = await createTag(event.name, event.emoji, event.color);
      await mediaDatabase.saveTag(tag);
      this.edit.toggleTag(tag);
      this.edit.tagPopupOpen = false;
    } catch (e) {
      if (e.status === 409) {
        this.edit.error.tagError = localize({ key: 'media.files.tags.new.error.exists' });
      } else {
        await alert({
          title: localize({ key: 'media.files.tags.new.error.title' }),
          message: localize({ key: 'media.files.tags.new.error.generic' }),
          buttonLabel: localize({ key: 'media.files.tags.new.error.close' }),
          negative: true,
        });
      }
    }
  },
  async createTagMultipleTag(event) {
    try {
      this.tagMultiple.error.tagError = '';
      const tag = await createTag(event.name, event.emoji, event.color);
      await mediaDatabase.saveTag(tag);
      this.tagMultiple.toggleTag(tag);
      this.tagMultiple.tagPopupOpen = false;
    } catch (e) {
      if (e.status === 409) {
        this.edit.error.tagError = localize({ key: 'media.files.tags.new.error.exists' });
      } else {
        await alert({
          title: localize({ key: 'media.files.tags.new.error.title' }),
          message: localize({ key: 'media.files.tags.new.error.generic' }),
          buttonLabel: localize({ key: 'media.files.tags.new.error.close' }),
          negative: true,
        });
      }
    }
  },
  async saveTag(tag, event) {
    try {
      await updateTag(tag.id, event.name, event.emoji, event.color);
      await mediaDatabase.saveTag({
        id: tag.id,
        name: event.name,
        emoji: event.emoji,
        color: event.color,
      });

      this.manageTags.tags = this.tags;
      this.manageTags.editOpenId = null;
    } catch (e) {
      if (e.status === 409) {
        this.manageTags.error.tagError = localize({ key: 'media.files.tags.edit.error.exists' });
      } else {
        await alert({
          title: localize({ key: 'media.files.tags.edit.error.title' }),
          message: localize({ key: 'media.files.tags.edit.error.generic' }),
          buttonLabel: localize({ key: 'media.files.tags.edit.error.close' }),
          negative: true,
        });
      }
    }
  },
  async updateTagsOnMultiple() {
    const promises = this.selectedFilesDetails.map(async (file) => {
      await tagFile(file.id, [...this.tagMultiple.tags]);

      const savedFile = await getFile(file.id);
      await mediaDatabase.saveFile(savedFile);

      this.tagMultiple.error.reset();
      this.tagMultiple.tags.clear();
    });
    try {
      await Promise.all(promises);
      this.tagMultiple.open = false;
    } catch (e) {
      this.tagMultiple.error.title = localize({ key: 'media.files.tag_multiple.error.title' });
      this.tagMultiple.error.message = localize({ key: 'media.files.tag_multiple.error.generic' });
      this.tagMultiple.error.hasError = true;
    }
  },
  uploadSingleFile: {
    open: false,
    name: '',
    file: null,
    tags: new Set(),
    tagPopupOpen: false,
    error: {
      title: '',
      message: '',
      tagError: '',
      hasError: false,
      reset() {
        this.title = '';
        this.message = '';
        this.hasError = false;
      },
    },
    toggleTag(tag) {
      if (this.tags.has(tag.name)) {
        this.tags.delete(tag.name);
      } else {
        this.tags.add(tag.name);
      }
    },
    selectFile(files) {
      if (files.length >= 1) {
        this.file = files.item(0);
      }
    },
  },
  uploadMultipleFiles: {
    open: false,
    files: null,
    tags: new Set(),
    tagPopupOpen: false,
    error: {
      title: '',
      message: '',
      tagError: '',
      hasError: false,
      reset() {
        this.title = '';
        this.message = '';
        this.hasError = false;
      },
    },
    toggleTag(tag) {
      if (this.tags.has(tag.name)) {
        this.tags.delete(tag.name);
      } else {
        this.tags.add(tag.name);
      }
    },
    selectFiles(files) {
      this.files = [...files];
    },
  },
  edit: {
    open: false,
    id: 0,
    name: '',
    file: null,
    tags: new Set(),
    tagPopupOpen: false,
    error: {
      title: '',
      message: '',
      tagError: '',
      hasError: false,
      reset() {
        this.title = '';
        this.message = '';
        this.hasError = false;
      },
    },
    toggleTag(tag) {
      if (this.tags.has(tag.name)) {
        this.tags.delete(tag.name);
      } else {
        this.tags.add(tag.name);
      }
    },
    selectFile(files) {
      if (files.length >= 1) {
        this.file = files.item(0);
      }
    },
  },
  tagMultiple: {
    open: false,
    tags: new Set(),
    tagPopupOpen: false,
    error: {
      title: '',
      message: '',
      tagError: '',
      hasError: false,
      reset() {
        this.title = '';
        this.message = '';
        this.hasError = false;
      },
    },
    toggleTag(tag) {
      if (this.tags.has(tag.name)) {
        this.tags.delete(tag.name);
      } else {
        this.tags.add(tag.name);
      }
    },
  },
  manageTags: {
    open: false,
    editOpenId: null,
    tags: [],
    tagPopupOpen: false,
    get tagPopupUpdateTitle() {
      return localize({ key: 'media.files.tags.edit.title' });
    },
    get tagPopupUpdateSaveLabel() {
      return localize({ key: 'media.files.tags.edit.save' });
    },
    get tagPopupUpdateCancelLabel() {
      return localize({ key: 'media.files.tags.edit.cancel' });
    },
    error: {
      tagError: '',
      reset() {
        this.tagError = false;
      },
    },
  },
}));
