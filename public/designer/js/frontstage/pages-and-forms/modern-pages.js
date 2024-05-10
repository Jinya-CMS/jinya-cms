import { Alpine } from '../../../../lib/alpine.js';
import localize from '../../foundation/utils/localize.js';
import confirm from '../../foundation/ui/confirm.js';
import {
  createModernPage,
  deleteModernPage,
  getModernPage,
  getModernPages,
  getModernPageSections,
  updateModernPage,
  updateModernPageSections,
} from '../../foundation/api/modern-pages.js';
import { getFilesByGallery, getGalleries } from '../../foundation/api/galleries.js';
import isEqual from '../../../lib/lodash/isEqual.js';
import filePicker from '../../foundation/ui/filePicker.js';
import alert from '../../foundation/ui/alert.js';

import '../../foundation/ui/components/inline-editor.js';
import { getModernPageDatabase } from '../../foundation/database/modern-page.js';

const modernPageDatabase = getModernPageDatabase();

Alpine.data('modernPagesData', () => ({
  pages: [],
  sections: [],
  selectedPage: null,
  galleries: [],
  get title() {
    return `#${this.selectedPage.id} ${this.selectedPage.name}`;
  },
  getGalleryText(gallery) {
    return `#${gallery.id} ${gallery.name}`;
  },
  changeSectionGallery(index, e) {
    this.sections[index].gallery = this.galleries[parseInt(e.target.value, 10)];
  },
  updateHtmlSection(index, value) {
    this.sections[index].html = value;
  },
  async init() {
    this.galleries = (await getGalleries()).items;
    const pages = await getModernPages();
    this.pages = pages.items;
    if (this.pages.length > 0) {
      await this.selectPage(this.pages[0]);
    }

    this.$watch('sections', () => {
      this.savePageSections();
    });
  },
  openCreateDialog() {
    this.create.error.reset();
    this.create.title = '';
    this.create.open = true;
  },
  openEditDialog() {
    this.edit.error.reset();
    this.edit.name = this.selectedPage.name;
    this.edit.open = true;
  },
  async selectPage(page) {
    if (page) {
      this.sections = this.cleanSections(await getModernPageSections(page.id), page.id);
    } else {
      this.sections = [];
    }
    this.selectedPage = page;

    const savedSections = await this.getPageSections(page.id);
    if (savedSections.length === 0) {
      await this.savePageSections();
    } else if (savedSections.length !== this.sections.length || !isEqual(savedSections, Alpine.raw(this.sections))) {
      const confirmed = await confirm({
        title: localize({ key: 'pages_and_forms.modern.designer.load.title' }),
        message: localize({ key: 'pages_and_forms.modern.designer.load.message' }),
        declineLabel: localize({ key: 'pages_and_forms.modern.designer.load.decline' }),
        approveLabel: localize({ key: 'pages_and_forms.modern.designer.load.approve' }),
      });
      if (confirmed) {
        this.sections = savedSections;
      } else {
        await this.savePageSections();
      }
    }
  },
  getPositions(gallery) {
    return getFilesByGallery(gallery.id);
  },
  async selectFile(section, index) {
    const file = await filePicker({
      title: localize({ key: 'pages_and_forms.modern.designer.pick_file' }),
      selectedFileId: section.file.id,
    });
    if (file) {
      this.sections[index].file = file;
      await this.savePageSections();
    }
  },
  async savePageSections() {
    await modernPageDatabase.saveSections(this.selectedPage.id, this.cleanSections(Alpine.raw(this.sections), this.selectedPage.id));
  },
  async clearPageSections() {
    await modernPageDatabase.deleteSections(this.selectedPage.id);
  },
  async getPageSections(id) {
    return this.cleanSections(await modernPageDatabase.getSections(id));
  },
  cleanSections(sections, pageId = null) {
    return sections.map((item) => ({
      pageId: pageId ?? item.pageId,
      gallery: item.gallery ? Alpine.raw(item.gallery) : null,
      file: item.file ? Alpine.raw(item.file) : null,
      link: item.link ?? null,
      html: item.html ?? null,
      type: item.file ? 'file' : item.gallery ? 'gallery' : 'html',
    }));
  },
  insertHtmlSection(index) {
    this.sections.splice(index, 0, {
      pageId: this.selectedPage.id,
      gallery: null,
      file: null,
      link: null,
      html: '',
      type: 'html',
    });
  },
  insertGallerySection(index) {
    this.sections.splice(index, 0, {
      pageId: this.selectedPage.id,
      gallery: this.galleries[0],
      file: null,
      link: null,
      html: null,
      type: 'gallery',
    });
  },
  async insertImageSection(index) {
    const file = await filePicker({
      title: localize({ key: 'pages_and_forms.modern.designer.pick_file' }),
    });
    if (file) {
      this.sections.splice(index, 0, {
        pageId: this.selectedPage.id,
        gallery: null,
        file: {
          id: file.id,
          name: file.name,
          type: file.type,
          path: file.path,
        },
        link: null,
        html: null,
        type: 'file',
      });
    }
  },
  async deleteItem(item, index) {
    const confirmed = await confirm({
      title: localize({ key: 'pages_and_forms.modern.delete_item.title' }),
      message: localize({
        key: 'pages_and_forms.modern.delete_item.message',
        values: item,
      }),
      approveLabel: localize({ key: 'pages_and_forms.modern.delete_item.delete' }),
      declineLabel: localize({ key: 'pages_and_forms.modern.delete_item.keep' }),
      negative: true,
    });
    if (confirmed) {
      this.items.splice(index, 1);
      await this.savePageSections();
    }
  },
  async savePage() {
    try {
      await updateModernPageSections(
        this.selectedPage.id,
        this.sections.map((item) => ({
          ...item,
          gallery: item.gallery?.id,
          file: item.file?.id,
        })),
      );
      this.message.hasMessage = true;
      this.message.title = localize({ key: 'pages_and_forms.modern.designer.success.title' });
      this.message.error = false;
      this.message.content = localize({ key: 'pages_and_forms.modern.designer.success.message' });
      setTimeout(() => {
        this.message.hasMessage = false;
      }, 30000);
    } catch (e) {
      this.message.hasMessage = true;
      this.message.title = localize({ key: 'pages_and_forms.modern.designer.error.title' });
      this.message.error = true;
      this.message.content = localize({ key: 'pages_and_forms.modern.designer.error.message' });
    }
  },
  async createPage() {
    try {
      const newPage = await createModernPage(this.create.name);
      this.pages.push(newPage);
      this.create.open = false;
      this.create.name = '';
      await this.selectPage(newPage);
    } catch (e) {
      this.create.error.hasError = true;
      this.create.error.title = localize({ key: 'pages_and_forms.modern.create.error.title' });
      if (e.status === 409) {
        this.create.error.message = localize({ key: 'pages_and_forms.modern.create.error.conflict' });
      } else {
        this.create.error.message = localize({ key: 'pages_and_forms.modern.create.error.generic' });
      }
    }
  },
  async updatePage() {
    try {
      await updateModernPage(this.selectedPage.id, this.edit.name);
      const savedPage = await getModernPage(this.selectedPage.id);
      this.edit.open = false;
      this.pages[this.pages.indexOf(this.selectedPage)].name = savedPage.name;
      this.pages[this.pages.indexOf(this.selectedPage)].created = savedPage.created;
      this.pages[this.pages.indexOf(this.selectedPage)].updated = savedPage.updated;
    } catch (e) {
      this.create.error.hasError = true;
      this.create.error.title = localize({ key: 'pages_and_forms.modern.create.error.title' });
      if (e.status === 409) {
        this.create.error.message = localize({ key: 'pages_and_forms.modern.create.error.conflict' });
      } else {
        this.create.error.message = localize({ key: 'pages_and_forms.modern.create.error.generic' });
      }
    }
  },
  async deletePage() {
    const confirmation = await confirm({
      title: localize({ key: 'pages_and_forms.modern.delete.title' }),
      message: localize({
        key: 'pages_and_forms.modern.delete.message',
        values: this.selectedPage,
      }),
      declineLabel: localize({ key: 'pages_and_forms.modern.delete.keep' }),
      approveLabel: localize({ key: 'pages_and_forms.modern.delete.delete' }),
      negative: true,
    });
    if (confirmation) {
      try {
        await deleteModernPage(this.selectedPage.id);
        this.pages = this.pages.filter((page) => page.id !== this.selectedPage.id);
        if (this.pages.length > 0) {
          await this.selectPage(this.pages[0]);
        } else {
          this.selectedPage = null;
        }
      } catch (e) {
        let message = '';
        if (e.status === 409) {
          message = localize({ key: 'pages_and_forms.modern.delete.error.conflict' });
        } else {
          message = localize({ key: 'pages_and_forms.modern.delete.error.generic' });
        }
        await alert({
          title: localize({ key: 'pages_and_forms.modern.delete.error.title' }),
          message,
          negative: true,
        });
      }
    }
  },
  create: {
    open: false,
    name: '',
    error: {
      title: '',
      message: '',
      hasError: false,
      reset() {
        this.title = '';
        this.message = '';
        this.hasError = false;
      },
    },
  },
  edit: {
    open: false,
    name: '',
    error: {
      title: '',
      message: '',
      hasError: false,
      reset() {
        this.title = '';
        this.message = '';
        this.hasError = false;
      },
    },
  },
  message: {
    title: '',
    content: '',
    isNegative: '',
    hasMessage: false,
    reset() {
      this.title = '';
      this.content = '';
      this.isNegative = '';
      this.hasMessage = false;
    },
  },
}));
