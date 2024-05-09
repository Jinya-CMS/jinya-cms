import { Alpine } from '../../../../lib/alpine.js';
import localize from '../../foundation/utils/localize.js';
import confirm from '../../foundation/ui/confirm.js';
import { getModernPages, getModernPageSections, updateModernPageSections } from '../../foundation/api/modern-pages.js';
import { getFilesByGallery, getGalleries } from '../../foundation/api/galleries.js';
import { Dexie } from '../../../lib/dexie.js';
import isEqual from '../../../lib/lodash/isEqual.js';
import filePicker from '../../foundation/ui/filePicker.js';

import '../../foundation/ui/components/inline-editor.js';

const dexie = new Dexie('modernPages');

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
    this.savePageSections();
  },
  updateHtmlSection(index, value) {
    this.sections[index].html = value;
    this.savePageSections();
  },
  async init() {
    dexie.version(1)
      .stores({
        sections: `++id,pageId`,
      });

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
    await this.clearPageSections();
    await dexie.sections.bulkAdd(this.cleanSections(Alpine.raw(this.sections), this.selectedPage.id));
  },
  async clearPageSections() {
    await dexie.sections.where('pageId')
      .equals(this.selectedPage.id)
      .delete();
  },
  async getPageSections(id) {
    return this.cleanSections(await dexie.sections.where('pageId')
      .equals(id)
      .toArray());
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
      html: '',
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
        file,
        link: null,
        html: '',
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
      await updateModernPageSections(this.selectedPage.id, this.sections.map((item) => ({
        ...item,
        gallery: item.gallery?.id,
        file: item.file?.id,
      })));
      this.message.hasMessage = true;
      this.message.title = localize({ key: 'pages_and_forms.modern.designer.success.title' });
      this.message.error = false;
      this.message.content = localize({ key: 'pages_and_forms.modern.designer.success.message' });
    } catch (e) {
      this.message.hasMessage = true;
      this.message.title = localize({ key: 'pages_and_forms.modern.designer.error.title' });
      this.message.error = true;
      this.message.content = localize({ key: 'pages_and_forms.modern.designer.error.message' });
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
