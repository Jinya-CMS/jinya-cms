import { Alpine } from '../../../../lib/alpine.js';
import confirm from '../../foundation/ui/confirm.js';
import localize from '../../foundation/utils/localize.js';
import alert from '../../foundation/ui/alert.js';
import { getBlogCategories } from '../../foundation/api/blog-categories.js';
import { categoriesToTree, prepareCategories } from './utils.js';
import {
  createBlogPost,
  deleteBlogPost,
  getBlogPosts,
  publishBlogPost,
  unpublishBlogPost,
  updateBlogPost,
} from '../../foundation/api/blog-post.js';
import filePicker from '../../foundation/ui/filePicker.js';
import { slugify } from '../../foundation/utils/text.js';

Alpine.data('postsData', () => ({
  posts: [],
  categories: [],
  selectedCategory: null,
  selectedPost: null,
  hasMessage: false,
  get title() {
    if (this.selectedCategory) {
      return `#${this.selectedCategory.id} ${this.selectedCategory.name}`;
    }

    return localize({ key: 'blog.posts.overview.all' });
  },
  getPostTitle(post) {
    return `#${post.id} ${post.title}`;
  },
  getCategoryOptionLabel(category) {
    return `${'&nbsp;'.repeat(category.nesting * 4)}#${category.id} ${category.name}`;
  },
  async init() {
    const [posts, categories] = await Promise.all([getBlogPosts(), getBlogCategories()]);
    this.categories = prepareCategories(categoriesToTree(categories.items));
    this.posts = posts.items;
    this.$watch('create.title', (newValue, oldValue) => {
      if (slugify(oldValue) === this.create.slug) {
        this.create.slug = slugify(newValue);
      }
    });
    this.$watch('edit.title', (newValue, oldValue) => {
      if (slugify(oldValue) === this.edit.slug) {
        this.edit.slug = slugify(newValue);
      }
    });
  },
  openCreateDialog() {
    this.create.error.reset();
    this.create.title = '';
    this.create.slug = '';
    this.create.headerImage = null;
    this.create.categoryId = this.selectedCategory?.id;
    this.create.picker.selected = localize({ key: 'blog.posts.edit.no_header_image' });
    this.create.open = true;
  },
  openEditDialog() {
    this.edit.error.reset();
    this.edit.title = this.selectedPost.title;
    this.edit.slug = this.selectedPost.slug;
    this.edit.headerImage = this.selectedPost.headerImage;
    this.edit.picker.selected = this.selectedPost.headerImage?.name ?? localize({ key: 'blog.posts.edit.no_header_image' });
    this.edit.categoryId = this.selectedPost.category?.id;
    this.edit.open = true;
  },
  async selectCategory(category) {
    this.selectedCategory = category;
    this.posts = (await getBlogPosts(this.selectedCategory?.id)).items;
    this.selectedPost = null;
  },
  selectPost(post) {
    this.selectedPost = post;
  },
  async selectCreateHeaderImage() {
    const selectedFileId = this.create.headerImage?.id;
    const fileResult = await filePicker({
      title: localize({ key: 'blog.posts.create.header_image' }),
      selectedFileId,
    });
    if (fileResult) {
      this.create.headerImage = fileResult;
      this.create.picker.selected = fileResult.name;
    }
  },
  async selectEditHeaderImage() {
    const selectedFileId = this.edit.headerImage?.id;
    const fileResult = await filePicker({
      title: localize({ key: 'blog.posts.edit.header_image' }),
      selectedFileId,
    });
    if (fileResult) {
      this.edit.headerImage = fileResult;
      this.edit.picker.selected = fileResult.name;
    }
  },
  async createPost() {
    try {
      const savedPost = await createBlogPost(this.create.title, this.create.slug, this.create.categoryId, this.create.headerImage?.id);
      this.create.open = false;
      this.posts = (await getBlogPosts(this.selectedCategory?.id)).items;
      this.selectPost(savedPost);
    } catch (e) {
      this.create.error.hasError = true;
      this.create.error.title = localize({ key: 'blog.posts.create.error.title' });
      if (e.status === 409) {
        if (e.message.includes('slug')) {
          this.create.error.message = localize({ key: 'blog.posts.create.error.slug_exists' });
        } else {
          this.create.error.message = localize({ key: 'blog.posts.create.error.title_exists' });
        }
      } else {
        this.create.error.message = localize({ key: 'blog.posts.create.error.generic' });
      }
    }
  },
  async updatePost() {
    try {
      await updateBlogPost(this.selectedPost.id, this.edit.title, this.edit.slug, this.edit.categoryId, this.edit.headerImage?.id);
      this.edit.open = false;
      this.posts[this.posts.indexOf(this.selectedPost)].title = this.edit.title;
      this.posts[this.posts.indexOf(this.selectedPost)].slug = this.edit.slug;
      this.posts[this.posts.indexOf(this.selectedPost)].headerImage = this.edit.headerImage;
      this.posts[this.posts.indexOf(this.selectedPost)].category = this.edit.category;
    } catch (e) {
      this.edit.error.hasError = true;
      this.edit.error.title = localize({ key: 'blog.posts.edit.error.title' });
      if (e.status === 409) {
        if (e.message.includes('slug')) {
          this.edit.error.message = localize({ key: 'blog.posts.edit.error.slug_exists' });
        }else {
          this.edit.error.message = localize({ key: 'blog.posts.edit.error.title_exists' });
        }
      } else {
        this.edit.error.message = localize({ key: 'blog.posts.edit.error.generic' });
      }
    }
  },
  async deletePost() {
    const confirmation = await confirm({
      title: localize({ key: 'blog.posts.delete.title' }),
      message: localize({
        key: 'blog.posts.delete.message',
        values: this.selectedPost,
      }),
      declineLabel: localize({ key: 'blog.posts.delete.keep' }),
      approveLabel: localize({ key: 'blog.posts.delete.delete' }),
      negative: true,
    });
    if (confirmation) {
      try {
        await deleteBlogPost(this.selectedPost.id);
        this.posts = this.posts.filter((post) => post.id !== this.selectedPost.id);
        this.selectedPost = null;
      } catch (e) {
        await alert({
          title: localize({ key: 'blog.posts.delete.error.title' }),
          message: localize({ key: 'blog.posts.delete.error.generic' }),
          negative: true,
        });
      }
    }
  },
  async publishPost() {
    const confirmation = await confirm({
      title: localize({ key: 'blog.posts.publish.title' }),
      message: localize({
        key: 'blog.posts.publish.message',
        values: this.selectedPost,
      }),
      declineLabel: localize({ key: 'blog.posts.publish.dont' }),
      approveLabel: localize({ key: 'blog.posts.publish.publish' }),
      negative: false,
    });
    if (confirmation) {
      try {
        await publishBlogPost(this.selectedPost.id);
        this.posts[this.posts.findIndex((post) => post.id === this.selectedPost.id)].public = true;
        this.selectedPost = null;
      } catch (e) {
        await alert({
          title: localize({ key: 'blog.posts.publish.error.title' }),
          message: localize({ key: 'blog.posts.publish.error.generic' }),
          negative: true,
        });
      }
    }
  },
  async unpublishPost() {
    const confirmation = await confirm({
      title: localize({ key: 'blog.posts.unpublish.title' }),
      message: localize({
        key: 'blog.posts.unpublish.message',
        values: this.selectedPost,
      }),
      declineLabel: localize({ key: 'blog.posts.unpublish.dont' }),
      approveLabel: localize({ key: 'blog.posts.unpublish.unpublish' }),
      negative: true,
    });
    if (confirmation) {
      try {
        await unpublishBlogPost(this.selectedPost.id);
        this.posts[this.posts.findIndex((post) => post.id === this.selectedPost.id)].public = false;
        this.selectedPost = null;
      } catch (e) {
        await alert({
          title: localize({ key: 'blog.posts.unpublish.error.title' }),
          message: localize({ key: 'blog.posts.unpublish.error.generic' }),
          negative: true,
        });
      }
    }
  },
  create: {
    open: false,
    title: '',
    slug: '',
    headerImage: null,
    categoryId: null,
    picker: {
      label: localize({ key: 'blog.posts.edit.file_picker_label' }),
      selected: localize({ key: 'blog.posts.edit.no_header_image' }),
    },
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
    title: '',
    slug: '',
    headerImage: null,
    categoryId: null,
    picker: {
      label: localize({ key: 'blog.posts.edit.file_picker_label' }),
      selected: '',
    },
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
