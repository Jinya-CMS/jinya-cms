import html from '../../../lib/jinya-html.js';
import clearChildren from '../../foundation/html/clearChildren.js';
import { get, httpDelete } from '../../foundation/http/request.js';
import JinyaDesignerPage from '../../foundation/JinyaDesignerPage.js';
import localize from '../../foundation/localize.js';
import confirm from '../../foundation/ui/confirm.js';

export default class PostOverviewPage extends JinyaDesignerPage {
  constructor({ layout }) {
    super({ layout });
    this.categories = [];
    this.selectedCategory = null;
    this.posts = [];
  }

  // eslint-disable-next-line class-methods-use-this
  toString() {
    return html` <div class="cosmo-side-list">
      <nav class="cosmo-side-list__items" id="category-list"></nav>
      <div class="cosmo-side-list__content jinya-designer">
        <div class="jinya-designer__title">
          <span class="cosmo-title" id="category-title"></span>
        </div>
        <div class="cosmo-toolbar cosmo-toolbar--designer">
          <div class="cosmo-toolbar__group">
            <button class="cosmo-button" id="new-post">${localize({ key: 'blog.posts.overview.action.new' })}</button>
          </div>
          <div class="cosmo-toolbar__group">
            <button class="cosmo-button" id="edit-post" disabled>
              ${localize({ key: 'blog.posts.overview.action.edit' })}
            </button>
            <button class="cosmo-button" id="designer-post" disabled>
              ${localize({ key: 'blog.posts.overview.action.designer' })}
            </button>
            <button class="cosmo-button" id="delete-post" disabled>
              ${localize({ key: 'blog.posts.overview.action.delete' })}
            </button>
          </div>
        </div>
        <div class="jinya-blog-tile__container"></div>
      </div>
    </div>`;
  }

  displayCategories() {
    let list = `<a class="cosmo-side-list__item" data-id="-1">${localize({ key: 'blog.posts.overview.all' })}</a>`;
    for (const category of this.categories) {
      list += `<a class="cosmo-side-list__item" data-id="${category.id}">#${category.id} ${category.name}</a>`;
    }
    clearChildren({ parent: document.getElementById('category-list') });
    document.getElementById('category-list').innerHTML = list;
    document.querySelectorAll('.cosmo-side-list__item').forEach((item) => {
      item.addEventListener('click', async () => {
        await this.selectCategory({ id: parseInt(item.getAttribute('data-id'), 10) });
        this.displaySelectedCategory();
      });
    });
  }

  displaySelectedCategory() {
    if (this.selectedCategory) {
      document.getElementById('category-title').innerText =
        `#${this.selectedCategory.id} ${this.selectedCategory.name}`;
    } else {
      document.getElementById('category-title').innerText = localize({ key: 'blog.posts.overview.all' });
    }
    const tileContainer = document.querySelector('.jinya-blog-tile__container');
    clearChildren({ parent: tileContainer });
    for (const post of this.posts) {
      const container = document.createElement('div');
      container.classList.add('jinya-blog-tile');
      container.setAttribute('data-post-id', post.id);
      container.innerHTML = html` <span class="jinya-blog-tile__title">#${post.id} ${post.title}</span>
        <img class="jinya-blog-tile__img" src="${post.headerImage?.path}" alt="${post.headerImage?.name}" />`;

      tileContainer.append(container);
      container.addEventListener('click', () => {
        this.selectPost(post);
      });
    }
  }

  async selectCategory({ id }) {
    this.selectedPost = null;
    if (id === -1) {
      document
        .querySelectorAll('.cosmo-side-list__item.is--active')
        .forEach((item) => item.classList.remove('is--active'));
      document.querySelector('[data-id="-1"]').classList.add('is--active');
      this.posts = (await get('/api/blog/post')).items;
    } else {
      this.selectedCategory = this.categories.find((f) => f.id === parseInt(id, 10));
      document
        .querySelectorAll('.cosmo-side-list__item.is--active')
        .forEach((item) => item.classList.remove('is--active'));
      document.querySelector(`[data-id="${id}"]`).classList.add('is--active');
      this.posts = (await get(`/api/blog/category/${this.selectedCategory.id}/post`)).items;
      document.getElementById('edit-post').disabled = !this.selectedPost;
      document.getElementById('designer-post').disabled = !this.selectedPost;
      document.getElementById('delete-post').disabled = !this.selectedPost;
    }
  }

  async displayed() {
    await super.displayed();
    const { items } = await get('/api/blog/category');
    this.categories = items;

    this.displayCategories();
    await this.selectCategory({ id: -1 });
    this.displaySelectedCategory();
  }

  bindEvents() {
    super.bindEvents();
    document.getElementById('delete-post').addEventListener('click', async () => {
      const confirmation = await confirm({
        title: localize({ key: 'blog.posts.overview.delete.title' }),
        message: localize({
          key: 'blog.posts.overview.delete.message',
          values: this.selectedPost,
        }),
        declineLabel: localize({ key: 'blog.posts.overview.delete.keep' }),
        approveLabel: localize({ key: 'blog.posts.overview.delete.delete' }),
        negative: true,
      });
      if (confirmation) {
        await httpDelete(`/api/blog/post/${this.selectedPost.id}`);
        this.posts = this.posts.filter((category) => category.id !== this.selectedPost.id);
        document.querySelector(`[data-post-id="${this.selectedPost.id}"]`).remove();
        this.selectedPost = null;
        document.getElementById('edit-post').disabled = !this.selectedPost;
        document.getElementById('designer-post').disabled = !this.selectedPost;
        document.getElementById('delete-post').disabled = !this.selectedPost;
      }
    });
    document.getElementById('new-post').addEventListener('click', async () => {
      const { default: AddPostDialog } = await import('./posts/AddPostDialog.js');
      const dialog = new AddPostDialog({
        category: this.selectedCategory?.id ?? -1,
        categories: this.categories,
        onHide: async (post) => {
          await this.selectCategory({ id: post.category?.id ?? -1 });
          this.displaySelectedCategory();
          this.selectPost(post);
        },
      });
      await dialog.show();
    });
    document.getElementById('edit-post').addEventListener('click', async () => {
      const { default: EditPostDialog } = await import('./posts/EditPostDialog.js');
      const dialog = new EditPostDialog({
        ...this.selectedPost,
        category: this.selectedPost.category.id,
        categories: this.categories,
        onHide: async (post) => {
          const { id } = this.selectedPost;
          const savedPost = this.posts.find((p) => p.id === id);
          savedPost.category = post.category;
          savedPost.title = post.title;
          savedPost.slug = post.slug;
          savedPost.public = post.postPublic;
          savedPost.headerImage = post.headerImage;
          await this.selectCategory({ id: post.category?.id ?? -1 });
          this.displaySelectedCategory();
          this.selectPost({ id });
        },
        headerImage: this.selectedPost.headerImage?.id,
      });
      await dialog.show();
    });
    document.getElementById('designer-post').addEventListener('click', async () => {
      const segments = await get(`/api/blog/post/${this.selectedPost.id}/segment`);
      const { default: PostDesignerDialog } = await import('./posts/PostDesignerDialog.js');
      const dialog = new PostDesignerDialog({
        post: this.selectedPost,
        segments,
      });
      await dialog.show();
    });
  }

  selectPost(post) {
    document
      .querySelectorAll('.jinya-blog-tile--selected')
      .forEach((tile) => tile.classList.remove('jinya-blog-tile--selected'));
    document.querySelector(`[data-post-id="${post.id}"]`).classList.add('jinya-blog-tile--selected');
    this.selectedPost = this.posts.find((p) => p.id === post.id);
    document.getElementById('edit-post').disabled = !this.selectedPost;
    document.getElementById('designer-post').disabled = !this.selectedPost;
    document.getElementById('delete-post').disabled = !this.selectedPost;
  }
}
