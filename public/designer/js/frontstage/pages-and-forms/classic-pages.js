import { Alpine } from '../../../../lib/alpine.js';
import {
  createClassicPage, deleteClassicPage,
  getClassicPage,
  getClassicPages,
  updateClassicPage,
} from '../../foundation/api/classic-pages.js';
import getEditor from '../../foundation/ui/tiny.js';
import localize from '../../foundation/localize.js';
import alert from '../../foundation/ui/alert.js';
import confirm from '../../foundation/ui/confirm.js';

Alpine.data('classicPagesData', () => ({
  pages: [],
  selectedPage: null,
  tiny: null,
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
  },
  destroy() {
    this.tiny?.destroy();
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
    this.selectedPage = page;
    this.$nextTick(async () => {
      if (!this.tiny) {
        this.tiny = await getEditor({
          element: this.$refs.tiny,
          height: '100%',
        });
      }
      this.tiny.setContent(page.content);
    });
  },
  async savePage() {
    try {
      await updateClassicPage(this.selectedPage.id, this.selectedPage.title, this.tiny.getContent());
      this.hasMessage = true;
      this.messageTitle = localize({ key: 'pages_and_forms.classic.edit.saved.title' });
      this.messageError = false;
      this.messageContent = localize({ key: 'pages_and_forms.classic.edit.saved.content' });
    } catch (e) {
      this.hasMessage = true;
      this.messageTitle = localize({ key: 'pages_and_forms.classic.edit.error.title' });
      this.messageError = true;
      this.messageContent = localize({ key: 'pages_and_forms.classic.edit.error.generic' });
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
