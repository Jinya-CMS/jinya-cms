<script>
  import page from "page";
  import { onMount } from "svelte";
  import { _ } from "svelte-i18n";
  import { get, getHost, httpDelete } from '../../http/request';
  import { jinyaConfirm } from "../../ui/confirm";

  let categories = [];
  let posts = [];
  let selectedCategory = null;
  let selectedPost = null;
  let loading = true;

  async function selectCategory(category) {
    loading = true;
    let loadedPosts;
    selectedCategory = category;
    if (selectedCategory === null) {
      loadedPosts = await get(`/api/blog/post`);
    } else {
      loadedPosts = await get(`/api/blog/category/${selectedCategory.id}/post`);
    }
    selectedPost = null;
    posts = loadedPosts.items;
    loading = false;
  }

  async function loadCategories() {
    const response = await get('/api/blog/category');
    categories = response.items;
    await selectCategory(null)
  }

  async function deletePost() {
    const result = await jinyaConfirm($_('blog.posts.overview.delete.title'), $_('blog.posts.overview.delete.message', {values: selectedCategory}), $_('blog.posts.overview.delete.delete'), $_('blog.posts.overview.delete.keep'));
    if (result) {
      await httpDelete(`/api/blog/post/${selectedPost.id}`);
      await selectCategory(selectedCategory);
    }
  }

  function editPost() {
    page(`/designer/blog/posts/edit/${selectedPost.id}`);
  }

  onMount(async () => {
    await loadCategories();
  });
</script>

<div class="cosmo-list">
    <nav class="cosmo-list__items">
        <a class="cosmo-list__item" class:cosmo-list__item--active={selectedCategory === null}
           on:click={() => selectCategory(null)}>{$_('blog.posts.overview.all')}</a>
        {#each categories as category (category.id)}
            <a class:cosmo-list__item--active={category.id === selectedCategory?.id} class="cosmo-list__item"
               on:click={() => selectCategory(category)}>{category.name}</a>
        {/each}
    </nav>
    <div class="cosmo-list__content jinya-designer">
        <div class="jinya-designer__title">
            {#if selectedCategory === null}
                <span class="cosmo-title">{$_('blog.posts.overview.all')}</span>
            {:else}
                <span class="cosmo-title">#{selectedCategory.id} {selectedCategory.name}</span>
            {/if}
        </div>
        <div class="cosmo-toolbar cosmo-toolbar--designer">
            <div class="cosmo-toolbar__group">
                <a class="cosmo-button" href="/designer/blog/posts/edit/new">{$_('blog.posts.overview.action.new')}</a>
            </div>
            <div class="cosmo-toolbar__group">
                <button class="cosmo-button" disabled={!selectedPost}
                        on:click={editPost}>{$_('blog.posts.overview.action.edit')}</button>
                <button class="cosmo-button" disabled={!selectedPost}
                        on:click={deletePost}>{$_('blog.posts.overview.action.delete')}</button>
            </div>
        </div>
        {#if loading}
            <div class="jinya-blog-view__loader jinya-loader__container">
                <div class="jinya-loader"></div>
            </div>
        {:else}
            <div class="jinya-blog-tile__container">
                {#each posts as post (post.id)}
                    <div on:click={() => selectedPost = post} class:jinya-blog-tile--selected={selectedPost === post}
                         class="jinya-blog-tile">
                        <span class="jinya-blog-tile__title">#{post.id} {post.title}</span>
                        <img class="jinya-blog-tile__img" src={`${getHost()}${post.headerImage?.path}`}
                             alt={post.headerImage?.name}>
                    </div>
                {/each}
            </div>
        {/if}
    </div>
</div>
