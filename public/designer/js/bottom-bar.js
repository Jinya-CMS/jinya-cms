import { Alpine } from '../../lib/alpine.js';
import { setColorScheme } from './foundation/api/my-jinya.js';
import { getRandomColor } from './foundation/utils/color.js';
import { getRandomEmoji } from './foundation/utils/text.js';
import localize from './foundation/utils/localize.js';
import { createTag, getTags } from './foundation/api/files.js';
import alert from './foundation/ui/alert.js';
import { getFileDatabase } from './foundation/database/file.js';
import { clearCache } from './foundation/api/cache.js';

const fileDatabase = getFileDatabase();

Alpine.data('indexBottomBarData', () => ({
  get changeColorThemeButton() {
    const { colorScheme } = Alpine.store('artist');
    switch (colorScheme) {
      case 'light':
        return '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="4"/><path d="M12 3v1"/><path d="M12 20v1"/><path d="M3 12h1"/><path d="M20 12h1"/><path d="m18.364 5.636-.707.707"/><path d="m6.343 17.657-.707.707"/><path d="m5.636 5.636.707.707"/><path d="m17.657 17.657.707.707"/></svg>';
      case 'dark':
        return '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 3a6 6 0 0 0 9 9 9 9 0 1 1-9-9Z"/><path d="M19 3v4"/><path d="M21 5h-4"/></svg>';
      default:
        return '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><path d="M12 2a7 7 0 1 0 10 10"/></svg>';
    }
  },
  async updateColorTheme() {
    const { colorScheme } = Alpine.store('artist');
    let newScheme = '';
    switch (colorScheme) {
      case 'light':
        newScheme = 'dark';
        break;
      case 'dark':
        newScheme = 'auto';
        break;
      default:
        newScheme = 'light';
        break;
    }
    await setColorScheme(newScheme);
    Alpine.store('artist').colorScheme = newScheme;
  },
  openFileUpload(e) {
    this.uploadMultipleFiles.error.reset();
    this.uploadMultipleFiles.open = true;
    this.uploadMultipleFiles.files = [];
    this.uploadMultipleFiles.tags = new Set();
  },
  async clearCache() {
    try {
      await clearCache();
      console.log('Cache cleared successfully');
    } catch (e) {
      console.error('Failed to clear the cache');
      console.error(e);
    }
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
  async enqueueFiles() {
    const tags = Alpine.raw(this.uploadMultipleFiles.tags);
    await fileDatabase.queueFilesForUpload(
      [...Alpine.raw(this.uploadMultipleFiles.files)].map((file) => ({
        data: file,
        name: file.name.split('.')
          .reverse()
          .slice(1)
          .reverse()
          .join('.'),
        tags: [...tags],
      })),
    );
    this.uploadMultipleFiles.open = false;
  },
  async init() {
    this.tags = await fileDatabase.getAllTags();

    getTags()
      .then((tags) => {
        fileDatabase.replaceTags(tags.items);

        this.tags = tags.items;
        this.loading = false;
      });
  },
  tags: [],
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
}));
