import { Alpine } from '../../../../lib/alpine.js';
import localize from '../../foundation/utils/localize.js';
import MimeTypes from '../../../lib/mime/types.js';
import {
  createFile,
  createTag,
  deleteFile,
  deleteTag,
  getFile,
  getFiles,
  getTags,
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

const fileDatabase = getFileDatabase();

Alpine.data('filesData', () => ({
  files: [],
  activeTags: new Set(),
  tags: [],
  selectedFile: null,
  loading: true,
  defaultTagName: localize({ key: 'media.files.action.show_all_tags' }),
  get filteredFiles() {
    if (this.activeTags.size === 0) {
      return this.files;
    }

    return this.files.filter((file) => file.tags.filter((tag) => this.activeTags.has(tag.id)).length > 0);
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
  get detailsFileType() {
    const { type } = this.selectedFile;
    if (type.startsWith('font')) {
      return localize({ key: 'media.files.details.types.font' });
    }

    const localizedType = localize({ key: `media.files.details.types.${type}` });
    if (localizedType === `media.files.details.types.${type}`) {
      return type;
    }

    return localizedType;
  },
  get detailsDownloadPath() {
    return `${this.selectedFile.path}.${MimeTypes[this.selectedFile.type]?.extensions[0] ?? ''}`;
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
    this.$watch('uploadSingleFile.file', (file) => {
      if (this.uploadSingleFile.name === '') {
        this.uploadSingleFile.name = file.name.split('.').reverse().slice(1).reverse().join('.');
      }
    });
    this.$watch('tags', fileDatabase.replaceTags);

    fileDatabase.replaceFiles(files.items);
    fileDatabase.replaceTags(tags.items);
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
    this.edit.name = this.selectedFile.name;
    this.edit.tags = new Set(this.selectedFile.tags.map((tag) => tag.name));
    this.edit.id = this.selectedFile.id;
  },
  openManageTagsDialog() {
    this.manageTags.error.reset();
    this.manageTags.open = true;
    this.manageTags.tags = [...this.tags];
  },
  openEditTag(id) {
    this.manageTags.error.reset();
    this.manageTags.editOpenId = id;
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
        this.tags = this.tags.filter((t) => t.id !== tag.id);

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
        values: { name: this.selectedFile.name },
      }),
      declineLabel: localize({ key: 'media.files.delete.decline' }),
      approveLabel: localize({ key: 'media.files.delete.approve' }),
      negative: true,
    });

    if (confirmed) {
      try {
        await deleteFile(this.selectedFile.id);
        await fileDatabase.deleteFile(this.selectedFile.id);
        this.selectedFile = null;
      } catch (e) {
        if (e.status === 409) {
          await alert({
            title: localize({ key: 'media.files.delete.error.title' }),
            message: localize({
              key: 'media.files.delete.error.conflict',
              values: { name: this.selectedFile.name },
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
  async uploadFile() {
    try {
      this.uploadSingleFile.error.reset();
      const savedFile = await createFile(
        this.uploadSingleFile.name,
        [...this.uploadSingleFile.tags],
        this.uploadSingleFile.file,
      );
      await fileDatabase.saveFile(savedFile);
      this.uploadSingleFile.open = false;
      this.selectedFile = savedFile;
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
      await fileDatabase.saveFile(savedFile);
      this.edit.open = false;
      this.selectedFile = savedFile;
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
      this.tags.push(tag);
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
      this.tags.push(tag);
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
      this.tags.push(tag);
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
      this.tags.push(tag);
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
  async saveTag(tag, event) {
    try {
      await updateTag(tag.id, event.name, event.emoji, event.color);
      const idx = this.tags.findIndex((t) => t.id === tag.id);
      this.tags[idx].name = event.name;
      this.tags[idx].emoji = event.emoji;
      this.tags[idx].color = event.color;

      this.manageTags.tags = this.tags;
      this.manageTags.editOpenId = null;

      for (const file of this.files) {
        if (file.tags.length > 0) {
          for (const t of file.tags) {
            if (t.id === tag.id) {
              t.name = event.name;
              t.emoji = event.emoji;
              t.color = event.color;
            }
          }
        }
      }
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
