<script>
  import { onMount } from "svelte";
  import { _ } from "svelte-i18n";
  import { get, getHost, post as httpPost, put } from "../../http/request";
  import { jinyaAlert } from "../../ui/alert";
  import { string_to_slug } from "../../utils/slugify";

  export let newPost = false;
  export let selectedPostId = null;

  let loading = true;
  let filesLoading = true;
  let categories = [];
  let files = [];
  let galleries = [];
  let tab = 'settings';
  let title = '';
  let slug = '';
  let postPublic = false;
  let headerImageId = null;
  let categoryId = null;

  let post;
  let slugModifiedByHand = false;

  $: if (title && !slugModifiedByHand) {
    slug = string_to_slug(title);
  }

  function discard() {
    if (newPost) {
      title = '';
      slug = '';
      postPublic = false;
      headerImageId = null;
      categoryId = null;
      slugModifiedByHand = false;
    } else {
      title = post.title;
      slug = post.slug;
      postPublic = post.public;
      headerImageId = post.headerImage.id;
      categoryId = post.category.id;
      slugModifiedByHand = true;
    }
  }

  async function save() {
    if (title && slug) {
      try {
        if (newPost) {
          const data = {
            title,
            slug,
            public: postPublic,
          };
          if (headerImageId) {
            data.headerImageId = headerImageId;
          }
          if (categoryId) {
            data.categoryId = categoryId;
          }
          post = await httpPost('/api/blog/post', data);
          selectedPostId = post.id;
        } else {
          const data = {
            title,
            slug,
            public: postPublic,
          };
          if (headerImageId) {
            data.headerImageId = headerImageId;
          }
          if (categoryId) {
            data.categoryId = categoryId;
          }
          await put(`/api/blog/post/${selectedPostId}`, data);
        }
        newPost = false;
        await jinyaAlert($_('blog.posts.edit.success.title'), $_(`blog.posts.edit.success.message`), $_('alert.dismiss'));
      } catch (e) {
        let msg = 'generic';
        if (e.status === 409 && e.message.startsWith('Slug')) {
          msg = 'slug_exists';
        } else if (e.status === 409 && e.message.startsWith('Title')) {
          msg = 'title_exists';
        }
        await jinyaAlert($_('blog.posts.edit.error.title'), $_(`blog.posts.edit.error.${msg}`), $_('alert.dismiss'));
      }
    }
  }

  onMount(async () => {
    if (!newPost) {
      post = await get(`/api/blog/post/${selectedPostId}`);
      title = post.title;
      slug = post.slug;
      postPublic = post.public;
      headerImageId = post.headerImage.id;
      categoryId = post.category.id;
      slugModifiedByHand = true;
    }

    const cats = await get('/api/blog/category');
    categories = cats.items;

    files = (await get('/api/media/file')).items;
    galleries = (await get('/api/media/gallery')).items;

    loading = false;
    filesLoading = false;
  });
</script>

<div>
    <span class="cosmo-title">
        {#if newPost}
            {$_('blog.posts.edit.title_new')}
        {:else}
            {$_('blog.posts.edit.title_edit', {values: {title}})}
        {/if}
    </span>
    <div class="cosmo-tab-control">
        <div class="cosmo-tab-control__tabs">
            <a class="cosmo-tab-control__tab-link" class:cosmo-tab-control__tab-link--active={tab === 'settings'}
               on:click={() => tab = 'settings'}>{$_('blog.posts.edit.tabs.settings')}</a>
            <a class="cosmo-tab-control__tab-link" class:cosmo-tab-control__tab-link--active={tab === 'segments'}
               on:click={() => tab = 'segments'}>{$_('blog.posts.edit.tabs.segments')}</a>
        </div>
        {#if tab === 'settings'}
            <div class="cosmo-tab-control__content">
                <div class="cosmo-input__group">
                    <label for="title" class="cosmo-label">{$_('blog.posts.edit.title')}</label>
                    <input required bind:value={title} type="text" id="title" class="cosmo-input">
                    <label for="slug" class="cosmo-label">{$_('blog.posts.edit.slug')}</label>
                    <input on:change={() => slugModifiedByHand = true} required bind:value={slug} type="text" id="slug"
                           class="cosmo-input">
                    <label for="category" class="cosmo-label">{$_('blog.posts.edit.category')}</label>
                    <select required bind:value={categoryId} id="category" class="cosmo-select">
                        <option value={null}>{$_('blog.posts.edit.no_category')}</option>
                        {#each categories as cat}
                            <option value={cat.id}>{cat.name}</option>
                        {/each}
                    </select>
                    <div class="cosmo-checkbox__group">
                        <input class="cosmo-checkbox" type="checkbox" id="public" bind:checked={postPublic}>
                        <label for="public">{$_('blog.posts.edit.public')}</label>
                    </div>
                </div>
                <h4>{$_('blog.posts.edit.header_image')}</h4>
                <div class="jinya-media-tile__container--modal">
                    {#each files as file (file.id)}
                        <div class="jinya-media-tile jinya-media-tile--medium" on:click={() => headerImageId = file.id}
                             class:jinya-media-tile--selected={file.id === headerImageId}>
                            <img class="jinya-media-tile__img jinya-media-tile__img--small"
                                 src={`${getHost()}${file.path}`}>
                        </div>
                    {/each}
                </div>
            </div>
        {/if}
        <div class="cosmo-button__container">
            <button class="cosmo-button"
                    on:click={discard}>{$_(`blog.posts.edit.action.discard_${newPost ? 'new' : 'edit'}`)}</button>
            <button class="cosmo-button"
                    on:click={save}>{$_(`blog.posts.edit.action.save_${newPost ? 'new' : 'edit'}`)}</button>
        </div>
    </div>
</div>