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
  getBlogPostSections,
  publishBlogPost,
  unpublishBlogPost,
  updateBlogPost,
  updateBlogPostSections,
} from '../../foundation/api/blog-post.js';
import filePicker from '../../foundation/ui/filePicker.js';
import { slugify } from '../../foundation/utils/text.js';
import { getFilesByGallery, getGalleries } from '../../foundation/api/galleries.js';
import { getBlogPostDatabase } from '../../foundation/database/blog-post.js';
import isEqual from '../../../lib/lodash/isEqual.js';

import '../../foundation/ui/components/inline-editor.js';
import '../../foundation/ui/components/diagrams/sparkline.js';

const postDatabase = getBlogPostDatabase();

Alpine.data('postsData', () => ({
  posts: [],
  categories: [],
  selectedCategory: null,
  selectedPost: null,
  sections: [],
  galleries: [],
  hasMessage: false,
  get title() {
    if (this.selectedCategory) {
      return `#${this.selectedCategory.id} ${this.selectedCategory.name}`;
    }

    return localize({ key: 'blog.posts.overview.all' });
  },
  getGalleryText(gallery) {
    return `#${gallery.id} ${gallery.name}`;
  },
  getPostTitle(post) {
    return `#${post.id} ${post.title}`;
  },
  getCategoryOptionLabel(category) {
    return `${'&nbsp;'.repeat(category.nesting * 4)}#${category.id} ${category.name}`;
  },
  getPositions(gallery) {
    return getFilesByGallery(gallery.id);
  },
  changeSectionGallery(index, e) {
    this.sections[index].gallery = this.galleries[parseInt(e.target.value, 10)];
  },
  updateHtmlSection(index, value) {
    this.sections[index].html = value;
  },
  async selectFile(section, index) {
    const file = await filePicker({
      title: localize({ key: 'blog.posts.designer.pick_file' }),
      selectedFileId: section.file.id,
    });
    if (file) {
      this.sections[index].file = file;
      await this.savePostSections();
    }
  },
  async savePostSections() {
    if (this.selectedPost) {
      await postDatabase.saveSections(
        this.selectedPost.id,
        this.cleanSections(Alpine.raw(this.sections), this.selectedPost.id),
      );
    }
  },
  async clearPostSections() {
    if (this.selectedPost) {
      await postDatabase.deleteSections(this.selectedPost.id);
    }
  },
  async getPostSections(id) {
    return this.cleanSections(await postDatabase.getSections(id));
  },
  cleanSections(sections, postId = null) {
    return sections.map((item) => ({
      postId: postId ?? item.postId,
      gallery: item.gallery ? Alpine.raw(item.gallery) : null,
      file: item.file ? Alpine.raw(item.file) : null,
      link: item.link ?? null,
      html: item.html ?? null,
      type: item.file ? 'file' : item.gallery ? 'gallery' : 'html',
    }));
  },
  insertHtmlSection(index) {
    this.sections.splice(index, 0, {
      postId: this.selectedPost.id,
      gallery: null,
      file: null,
      link: null,
      html: '',
      type: 'html',
    });
  },
  insertGallerySection(index) {
    this.sections.splice(index, 0, {
      postId: this.selectedPost.id,
      gallery: this.galleries[0],
      file: null,
      link: null,
      html: null,
      type: 'gallery',
    });
  },
  async insertImageSection(index) {
    const file = await filePicker({
      title: localize({ key: 'blog.posts.designer.pick_file' }),
    });
    if (file) {
      this.sections.splice(index, 0, {
        postId: this.selectedPost.id,
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
  async deleteSection(section, index) {
    const confirmed = await confirm({
      title: localize({ key: 'blog.posts.delete_section.title' }),
      message: localize({
        key: 'blog.posts.delete_section.message',
        values: section,
      }),
      approveLabel: localize({ key: 'blog.posts.delete_section.delete' }),
      declineLabel: localize({ key: 'blog.posts.delete_section.keep' }),
      negative: true,
    });
    if (confirmed) {
      this.sections.splice(index, 1);
      await this.savePostSections();
    }
  },
  async init() {
    const [posts, categories, galleries] = await Promise.all([getBlogPosts(), getBlogCategories(), getGalleries()]);
    this.categories = prepareCategories(categoriesToTree(categories.items));
    this.posts = posts.items;
    this.galleries = galleries.items;
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
    this.$watch('sections', () => {
      this.savePostSections();
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
    this.edit.picker.selected =
      this.selectedPost.headerImage?.name ?? localize({ key: 'blog.posts.edit.no_header_image' });
    this.edit.categoryId = this.selectedPost.category?.id;
    this.edit.open = true;
  },
  async selectCategory(category) {
    this.selectedCategory = category;
    this.posts = (await getBlogPosts(this.selectedCategory?.id)).items;
    this.selectedPost = null;
  },
  async selectPost(index, post) {
    this.selectedPost = post;
    if (post) {
      this.$nextTick(() => {
        let height = 15;
        if (window.matchMedia('screen and (width <= 1920px)').matches) {
          height = 10;
        }

        const rootFontSize = parseFloat(getComputedStyle(document.documentElement).fontSize);
        let top = 0;
        if (index > 0) {
          top = (index * height + index * 1.5) * rootFontSize;
        }

        this.$refs.tileContainer.scrollTo({
          top,
        });
      });

      this.sections = this.cleanSections(await getBlogPostSections(post.id), post.id);

      const savedPost = await postDatabase.getChangedPost(post.id);
      const savedSections = await this.getPostSections(post.id);
      const savedPageUpdatedAt = Date.parse(savedPost?.updated?.at) ?? 0;
      const pageUpdatedAt = Date.parse(post.updated.at);

      if (savedSections.length === 0 || savedPageUpdatedAt < pageUpdatedAt) {
        await this.savePostSections();
      } else if (savedSections.length !== this.sections.length || !isEqual(savedSections, Alpine.raw(this.sections))) {
        const confirmed = await confirm({
          title: localize({ key: 'blog.posts.designer.load.title' }),
          message: localize({ key: 'blog.posts.designer.load.message' }),
          declineLabel: localize({ key: 'blog.posts.designer.load.decline' }),
          approveLabel: localize({ key: 'blog.posts.designer.load.approve' }),
        });
        if (confirmed) {
          this.sections = savedSections;
        } else {
          await this.savePostSections();
        }
      }
    } else {
      this.sections = [];
    }
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
      const savedPost = await createBlogPost(
        this.create.title,
        this.create.slug,
        this.create.categoryId,
        this.create.headerImage?.id,
      );
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
      await updateBlogPost(
        this.selectedPost.id,
        this.edit.title,
        this.edit.slug,
        this.edit.categoryId,
        this.edit.headerImage?.id,
      );
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
        } else {
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
      } catch (e) {
        await alert({
          title: localize({ key: 'blog.posts.unpublish.error.title' }),
          message: localize({ key: 'blog.posts.unpublish.error.generic' }),
          negative: true,
        });
      }
    }
  },
  async savePost() {
    try {
      await updateBlogPostSections(
        this.selectedPost.id,
        this.sections.map((item) => ({
          ...item,
          gallery: item.gallery?.id,
          file: item.file?.id,
        })),
      );
      if (!this.selectedPost.public) {
        const confirmation = await confirm({
          title: localize({ key: 'blog.posts.designer.publish.title' }),
          message: localize({
            key: 'blog.posts.designer.publish.message',
            values: this.selectedPost,
          }),
          declineLabel: localize({ key: 'blog.posts.designer.publish.dont' }),
          approveLabel: localize({ key: 'blog.posts.designer.publish.publish' }),
          negative: false,
        });
        if (confirmation) {
          try {
            await publishBlogPost(this.selectedPost.id);
            this.posts[this.posts.findIndex((post) => post.id === this.selectedPost.id)].public = true;
            this.selectedPost = null;
          } catch (e) {
            await alert({
              title: localize({ key: 'blog.posts.publish.designer.error.title' }),
              message: localize({ key: 'blog.posts.publish.designer.error.generic' }),
              negative: true,
            });
          }
        }
      }
      await this.selectPost(null, null);
    } catch (e) {
      this.message.hasMessage = true;
      this.message.title = localize({ key: 'blog.posts.designer.error.title' });
      this.message.isNegative = true;
      this.message.content = localize({ key: 'blog.posts.designer.error.message' });
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
  message: {
    title: '',
    content: '',
    isNegative: false,
    hasMessage: false,
    reset() {
      this.title = '';
      this.content = '';
      this.isNegative = false;
      this.hasMessage = false;
    },
  },
}));
