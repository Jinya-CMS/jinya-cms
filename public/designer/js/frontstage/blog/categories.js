import { Alpine } from '../../../../lib/alpine.js';
import confirm from '../../foundation/ui/confirm.js';
import localize from '../../foundation/utils/localize.js';
import alert from '../../foundation/ui/alert.js';
import {
  createBlogCategory,
  deleteBlogCategory,
  getBlogCategories,
  updateBlogCategory,
} from '../../foundation/api/blog-categories.js';
import { categoriesToTree, prepareCategories } from './utils.js';
import { getBlogPosts } from '../../foundation/api/blog-post.js';

Alpine.data('categoriesData', () => ({
  categories: [],
  selectedCategory: null,
  hasMessage: false,
  get title() {
    return `#${this.selectedCategory.id} ${this.selectedCategory.name}`;
  },
  async canDelete() {
    if (this.categories.filter(c => c.parent?.id === this.selectedCategory.id).length > 0) {
      return false;
    }

    const posts = await getBlogPosts(this.selectedCategory.id);

    return posts.totalCount === 0;
  },
  async init() {
    const categories = await getBlogCategories();
    this.categories = prepareCategories(categoriesToTree(categories.items));
    if (this.categories.length > 0) {
      await this.selectCategory(this.categories[0]);
    }
  },
  openCreateDialog(parent) {
    this.create.error.reset();
    this.create.name = '';
    this.create.description = '';
    this.create.webhookEnabled = false;
    this.create.webhookUrl = '';
    this.create.parent = parent;
    this.create.open = true;
  },
  openEditDialog() {
    this.edit.error.reset();
    this.edit.name = this.selectedCategory.name;
    this.edit.description = this.selectedCategory.description;
    this.edit.webhookEnabled = this.selectedCategory.webhookEnabled;
    this.edit.webhookUrl = this.selectedCategory.webhookUrl;
    this.edit.open = true;
  },
  async selectCategory(category) {
    this.selectedCategory = category;
  },
  async updateCategory() {
    try {
      await updateBlogCategory(this.selectedCategory.id, this.edit.name, this.edit.description, this.edit.webhookEnabled, this.edit.webhookUrl);
      this.edit.open = false;
      this.categories[this.categories.indexOf(this.selectedCategory)].name = this.edit.name;
      this.categories[this.categories.indexOf(this.selectedCategory)].description = this.edit.description;
      this.categories[this.categories.indexOf(this.selectedCategory)].webhookEnabled = this.edit.webhookEnabled;
      this.categories[this.categories.indexOf(this.selectedCategory)].webhookUrl = this.edit.webhookUrl;
    } catch (e) {
      this.edit.error.hasError = true;
      this.edit.error.title = localize({ key: 'blog.categories.edit.error.title' });
      if (e.status === 409) {
        this.edit.error.message = localize({ key: 'blog.categories.edit.error.conflict' });
      } else {
        this.edit.error.message = localize({ key: 'blog.categories.edit.error.generic' });
      }
    }
  },
  async createCategory() {
    try {
      const savedCat = await createBlogCategory(this.create.name, this.create.description, this.create.parent?.id, this.create.webhookEnabled, this.create.webhookUrl);
      this.create.open = false;
      let nesting = 0;
      if (this.create.parent) {
        nesting = this.create.parent.nesting + 1;
        const lastParentChild = this.categories.findLastIndex((item) => item.parent?.id === this.create.parent.id);
        if (lastParentChild === -1) {
          nesting = this.create.parent.nesting + 1;
          this.categories.splice(this.categories.findIndex((item) => item.id === this.create.parent.id) + 1, 0, {
            ...savedCat,
            nesting,
          });
        } else {
          this.categories.splice(lastParentChild + 1, 0, {
            ...savedCat,
            nesting,
          });
        }
      } else {
        this.categories.push({
          ...savedCat,
          nesting,
        });
      }
      await this.selectCategory({
        ...savedCat,
        nesting,
      });
    } catch (e) {
      this.create.error.hasError = true;
      this.create.error.title = localize({ key: 'blog.categories.create.error.title' });
      if (e.status === 409) {
        this.create.error.message = localize({ key: 'blog.categories.create.error.conflict' });
      } else {
        this.create.error.message = localize({ key: 'blog.categories.create.error.generic' });
      }
    }
  },
  async deleteCategory() {
    const confirmation = await confirm({
      title: localize({ key: 'blog.categories.delete.title' }),
      message: localize({
        key: 'blog.categories.delete.message',
        values: this.selectedCategory,
      }),
      declineLabel: localize({ key: 'blog.categories.delete.keep' }),
      approveLabel: localize({ key: 'blog.categories.delete.delete' }),
      negative: true,
    });
    if (confirmation) {
      try {
        await deleteBlogCategory(this.selectedCategory.id);
        this.categories = this.categories.filter((category) => category.id !== this.selectedCategory.id);
        if (this.categories.length > 0) {
          await this.selectCategory(this.categories[0]);
        } else {
          this.selectedCategory = null;
        }
      } catch (e) {
        let message = '';
        if (e.status === 409) {
          message = localize({ key: 'blog.categories.delete.error.conflict' });
        } else {
          message = localize({ key: 'blog.categories.delete.error.generic' });
        }
        await alert({
          title: localize({ key: 'blog.categories.delete.error.title' }),
          message,
          negative: true,
        });
      }
    }
  },
  create: {
    open: false,
    name: '',
    description: '',
    webhookEnabled: false,
    webhookUrl: '',
    parent: null,
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
    description: '',
    webhookEnabled: false,
    webhookUrl: '',
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
}));
