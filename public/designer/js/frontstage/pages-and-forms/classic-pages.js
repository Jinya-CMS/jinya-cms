import { Alpine } from '../../../../lib/alpine.js';
import {
  createClassicPage,
  deleteClassicPage,
  getClassicPage,
  getClassicPages,
  updateClassicPage,
} from '../../foundation/api/classic-pages.js';
import localize from '../../foundation/utils/localize.js';
import alert from '../../foundation/ui/alert.js';
import confirm from '../../foundation/ui/confirm.js';
import { getClassicPageDatabase } from '../../foundation/database/classic-page.js';

import '../../foundation/ui/components/toolbar-editor.js';
import '../../foundation/ui/components/diagrams/sparkline.js';

const classicPageDatabase = getClassicPageDatabase();

Alpine.data('classicPagesData', () => ({
  pages: [],
  selectedPage: null,
  content: null,
  hasMessage: false,
  messageTitle: '',
  messageContent: '',
  messageError: false,
  get title() {
    return `#${this.selectedPage.id} ${this.selectedPage.title}`;
  },
  async init() {
    const pages = await getClassicPages();
    this.pages = pages.items;
    if (this.pages.length > 0) {
      await this.selectPage(this.pages[0]);
    }

    this.$watch('content', async (value) => await this.savePageContent(value));
  },
  openCreateDialog() {
    this.create.error.reset();
    this.create.title = '';
    this.create.open = true;
  },
  openEditDialog() {
    this.edit.error.reset();
    this.edit.title = this.selectedPage.title;
    this.edit.open = true;
  },
  async selectPage(page) {
    const savedPage = await this.getSavedPage(page.id);
    this.selectedPage = page;
    this.content = page.content;
    const savedPageUpdatedAt = Date.parse(savedPage?.updated?.at) ?? 0;
    const pageUpdatedAt = Date.parse(page.updated.at);

    if (savedPageUpdatedAt < pageUpdatedAt) {
      await this.deleteSavedPage(page.id);
    } else if (savedPage && savedPage.content !== page.content && savedPage.content) {
      const confirmed = await confirm({
        title: localize({ key: 'pages_and_forms.classic.load.title' }),
        message: localize({ key: 'pages_and_forms.classic.load.message' }),
        declineLabel: localize({ key: 'pages_and_forms.classic.load.decline' }),
        approveLabel: localize({ key: 'pages_and_forms.classic.load.approve' }),
      });
      if (confirmed) {
        this.content = savedPage.content;
      } else {
        await this.deleteSavedPage(page.id);
      }
    }
  },
  getSavedPage(id) {
    return classicPageDatabase.getChangedPage(id);
  },
  deleteSavedPage(id) {
    return classicPageDatabase.deleteChangedPage(id);
  },
  async savePageContent() {
    await classicPageDatabase.saveChangedPage(Alpine.raw(this.selectedPage.id), Alpine.raw(this.content));
  },
  async savePage() {
    try {
      await updateClassicPage(this.selectedPage.id, this.selectedPage.title, this.content);
      this.hasMessage = true;
      this.messageTitle = localize({ key: 'pages_and_forms.classic.edit.saved.title' });
      this.messageError = false;
      this.messageContent = localize({ key: 'pages_and_forms.classic.edit.saved.content' });
      setTimeout(() => this.hasMessage = false, 5_000);
    } catch (e) {
      this.hasMessage = true;
      this.messageTitle = localize({ key: 'pages_and_forms.classic.edit.error.title' });
      this.messageError = true;
      this.messageContent = localize({ key: 'pages_and_forms.classic.edit.error.generic' });
      setTimeout(() => this.hasMessage = false, 15_000);
    }
  },
  async updatePage() {
    try {
      await updateClassicPage(this.selectedPage.id, this.edit.title, this.selectedPage.content);
      const savedPage = await getClassicPage(this.selectedPage.id);
      this.edit.open = false;
      this.pages[this.pages.indexOf(this.selectedPage)].title = savedPage.title;
      this.pages[this.pages.indexOf(this.selectedPage)].created = savedPage.created;
      this.pages[this.pages.indexOf(this.selectedPage)].updated = savedPage.updated;
      await this.selectPage(savedPage);
    } catch (e) {
      this.edit.error.hasError = true;
      this.edit.error.title = localize({ key: 'pages_and_forms.classic.edit.error.title' });
      if (e.status === 409) {
        this.edit.error.message = localize({ key: 'pages_and_forms.classic.edit.error.conflict' });
      } else {
        this.edit.error.message = localize({ key: 'pages_and_forms.classic.edit.error.generic' });
      }
    }
  },
  async createPage() {
    try {
      const savedPage = await createClassicPage(this.create.title, '');
      this.create.open = false;
      this.pages.push(savedPage);
      await this.selectPage(savedPage);
    } catch (e) {
      this.create.error.hasError = true;
      this.create.error.title = localize({ key: 'pages_and_forms.classic.create.error.title' });
      if (e.status === 409) {
        this.create.error.message = localize({ key: 'pages_and_forms.classic.create.error.conflict' });
      } else {
        this.create.error.message = localize({ key: 'pages_and_forms.classic.create.error.generic' });
      }
    }
  },
  async deletePage() {
    const confirmation = await confirm({
      title: localize({ key: 'pages_and_forms.classic.delete.title' }),
      message: localize({
        key: 'pages_and_forms.classic.delete.message',
        values: this.selectedPage,
      }),
      declineLabel: localize({ key: 'pages_and_forms.classic.delete.keep' }),
      approveLabel: localize({ key: 'pages_and_forms.classic.delete.delete' }),
      negative: true,
    });
    if (confirmation) {
      try {
        await deleteClassicPage(this.selectedPage.id);
        this.pages = this.pages.filter((page) => page.id !== this.selectedPage.id);
        if (this.pages.length > 0) {
          await this.selectPage(this.pages[0]);
        } else {
          this.selectedPage = null;
        }
      } catch (e) {
        let message = '';
        if (e.status === 409) {
          message = localize({ key: 'pages_and_forms.classic.delete.error.conflict' });
        } else {
          message = localize({ key: 'pages_and_forms.classic.delete.error.generic' });
        }
        await alert({
          title: localize({ key: 'pages_and_forms.classic.delete.error.title' }),
          message,
          negative: true,
        });
      }
    }
  },
  create: {
    open: false,
    title: '',
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
  },
  edit: {
    open: false,
    title: '',
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
  },
}));
