<script>
  import Sortable from "sortablejs";
  import { onMount, tick } from "svelte";
  import { _ } from "svelte-i18n";
  import { get, getHost, post as httpPost, put } from "../../http/request";
  import { jinyaAlert } from "../../ui/alert";
  import { jinyaConfirm } from "../../ui/confirm";
  import { createTiny } from "../../ui/tiny";
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
  let segments = [];

  let editSegmentOpen;
  let editSegmentFile;
  let editSegmentIsLink;
  let editSegmentTarget;
  let editSegmentGallery;
  let editSegmentHtml;
  let editSegmentHtmlElement;
  let selectedSegment = null;
  let selectedSegmentId = null;
  let selectedSegmentType = 'html';
  let segmentToolboxElement;
  let segmentListElement;
  let segmentEditorTiny;
  let createNewSegment = false;
  let createPosition;
  let createNewSegmentType;

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
        await put(`/api/blog/post/${selectedPostId}/segment`, {segments});
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

  $: if (segmentToolboxElement instanceof HTMLElement) {
    new Sortable(segmentToolboxElement, {
      group: {name: 'segment_page', put: false, pull: 'clone'},
      sort: false,
      handle: '.jinya-designer__drag-handle',
      onEnd(e) {
        if (!e.to.classList.contains('jinya-designer__toolbox')) {
          e.item.remove();
        }
      }
    });
  }

  $: if (segmentListElement instanceof HTMLElement) {
    new Sortable(segmentListElement, {
      group: {name: 'segment_page', put: true, pull: false},
      sort: true,
      async onAdd(e) {
        createNewSegment = true;
        createPosition = e.newIndex;
        createNewSegmentType = e.item.getAttribute('data-type');
        switch (createNewSegmentType) {
          case 'gallery':
            segments.splice(createPosition, 0, {gallery: galleries[0].id, id: Date.now()});
            break;
          case 'file':
            segments.splice(createPosition, 0, {file: files[0].id, id: Date.now()});
            break;
          case 'html':
            segments.splice(createPosition, 0, {html: '<p></p>', id: Date.now()});
            break;
        }
        selectedSegmentId = createPosition;
        selectedSegment = segments[selectedSegmentId];
        if (selectedSegment.gallery) {
          selectedSegmentType = 'gallery';
        } else if (selectedSegment.file) {
          selectedSegmentType = 'file';
        } else if (selectedSegment.html) {
          selectedSegmentType = 'html';
        }
        await editSegment();
      },
      async onUpdate(e) {
        const dropIdx = e.newIndex;
        const oldIndex = e.oldIndex;
        const segment = segments[oldIndex];
        segments.splice(segments.findIndex(item => item.id === segment.id), 1);
        segments.splice(dropIdx, 0, segment);
      },
    });
  }

  async function editSegment() {
    editSegmentOpen = true;
    editSegmentFile = selectedSegment?.file;
    editSegmentIsLink = selectedSegment?.link;
    editSegmentTarget = selectedSegment?.link;
    editSegmentGallery = selectedSegment?.gallery;
    if (editSegmentGallery) {
      selectedSegmentType = 'gallery';
    } else if (editSegmentFile) {
      selectedSegmentType = 'file';
    } else {
      selectedSegmentType = 'html';
    }
    if (selectedSegment?.html) {
      await tick();
      segmentEditorTiny = await createTiny(editSegmentHtmlElement);
      segmentEditorTiny.setContent(selectedSegment.html);
    }
  }

  async function deleteSegment() {
    const result = await jinyaConfirm($_('blog.posts.edit.segment.delete_segment.title'), $_('blog.posts.edit.segment.delete_segment.message'), $_('blog.posts.edit.segment.delete_segment.delete'), $_('blog.posts.edit.segment.delete_segment.keep'));
    if (result) {
      segments.splice(selectedSegmentId, 1);
      selectedSegmentType = '';
      selectedSegmentId = null;
      selectedSegment = null;
    }
  }

  function selectSegment(segment) {
    selectedSegment = segment;
    selectedSegmentId = segments.findIndex(s => s.id === segment.id);
  }

  function cancelEditSegment() {
    if (createNewSegment) {
      segments.splice(selectedSegmentId, 1);
    }

    createNewSegment = false;
    editSegmentOpen = false;
    editSegmentFile = null;
    editSegmentIsLink = false;
    editSegmentTarget = null;
    editSegmentGallery = null;
    editSegmentHtml = null;
    segmentEditorTiny?.destroy();
    segmentEditorTiny = null;
  }

  async function updateSegment() {
    if (selectedSegmentType === 'file') {
      const file = files.find(f => f.id === editSegmentFile);
      segments[selectedSegmentId].file = editSegmentFile;
      segments[selectedSegmentId].link = editSegmentTarget;
    } else if (selectedSegmentType === 'gallery') {
      segments[selectedSegmentId].gallery = editSegmentGallery;
    } else if (selectedSegmentType === 'html') {
      segments[selectedSegmentId].html = segmentEditorTiny.getContent();
      segmentEditorTiny.destroy();
    }

    createNewSegment = false;
    editSegmentOpen = false;
    editSegmentFile = null;
    editSegmentIsLink = false;
    editSegmentTarget = null;
    editSegmentGallery = null;
    editSegmentHtml = null;
    segmentEditorTiny = null;
    selectedSegmentType = '';
    selectedSegmentId = null;
    selectedSegment = null;
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
      const loadedSegments = (await get(`/api/blog/post/${selectedPostId}/segment`));
      for (const loadedSegment of loadedSegments) {
        if (loadedSegment.file) {
          segments.push({...loadedSegment, file: loadedSegment.file.id});
        } else if (loadedSegment.gallery) {
          segments.push({...loadedSegment, gallery: loadedSegment.gallery.id});
        } else {
          segments.push(loadedSegment);
        }
      }
    }

    const cats = await get('/api/blog/category');
    categories = cats.items;

    files = (await get('/api/media/file')).items;
    galleries = (await get('/api/media/gallery')).items;

    loading = false;
    filesLoading = false;
  });
</script>

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
                <input on:change={() => slugModifiedByHand = true} required bind:value={slug} type="text"
                       id="slug"
                       class="cosmo-input">
                <label for="category" class="cosmo-label">{$_('blog.posts.edit.category')}</label>
                <select required bind:value={categoryId} id="category" class="cosmo-select">
                    <option value={null}>{$_('blog.posts.edit.no_category')}</option>
                    {#each categories as cat}
                        <option value={cat.id}>#{cat.id} {cat.name}</option>
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
                    <div class="jinya-media-tile jinya-media-tile--medium"
                         on:click={() => headerImageId = file.id}
                         class:jinya-media-tile--selected={file.id === headerImageId}>
                        <img class="jinya-media-tile__img jinya-media-tile__img--small"
                             src={`${getHost()}${file.path}`}>
                    </div>
                {/each}
            </div>
        </div>
    {:else if tab === 'segments'}
        <div class="jinya-designer">
            <div class="cosmo-toolbar cosmo-toolbar--designer">
                <div class="cosmo-toolbar__group">
                    <button disabled={!selectedSegment} on:click={editSegment}
                            class="cosmo-button">{$_('pages_and_forms.segment.action.edit_segment')}</button>
                    <button disabled={!selectedSegment} on:click={deleteSegment}
                            class="cosmo-button">{$_('pages_and_forms.segment.action.delete_segment')}</button>
                </div>
            </div>
            <div class="jinya-designer__content jinya-designer__content--blog">
                <div bind:this={segmentListElement}
                     class="jinya-designer__result jinya-designer__result--horizontal">
                    {#each segments as segment (segment.id)}
                        {#if segment.file}
                            <div class:jinya-designer-item--selected={selectedSegment === segment}
                                 class:jinya-designer-item--file-selected={selectedSegment === segment}
                                 on:click={() => selectSegment(segment)}
                                 class="jinya-designer-item jinya-designer-item--file">
                                <img class="jinya-segment__image"
                                     src={`${getHost()}${files.find(f => f.id === segment.file)?.path}`}
                                     alt={files.find(f => f.id === segment.file)?.name}>
                                <div class="jinya-designer-item__details jinya-designer-item__details--file"
                                     class:jinya-designer-item__details--file-selected={selectedSegment === segment}>
                                    <span class="jinya-designer-item__title">{$_('pages_and_forms.segment.designer.file')}</span>
                                    <dl class="jinya-segment__action">
                                        <dt class="jinya-segment__label">{$_('blog.posts.edit.segment.file.name')}</dt>
                                        <dd class="jinya-segment__content">{files.find(f => f.id === segment.file)?.name}</dd>
                                        {#if segment.link}
                                            <dt class="jinya-segment__label">{$_('pages_and_forms.segment.designer.link')}</dt>
                                            <dd class="jinya-segment__content">{segment.link}</dd>
                                        {/if}
                                    </dl>
                                </div>
                            </div>
                        {:else if segment.gallery}
                            <div class:jinya-designer-item--selected={selectedSegment === segment}
                                 on:click={() => selectSegment(segment)}
                                 class="jinya-designer-item jinya-designer-item--gallery">
                                <span class="jinya-designer-item__title">{$_('pages_and_forms.segment.designer.gallery')}</span>
                                <span class="jinya-designer-item__details jinya-designer-item__details--gallery">{galleries.find(f => f.id === segment.gallery)?.name}</span>
                                <div class="jinya-media-tile__container--segment">
                                    {#await get(`/api/media/gallery/${segment.gallery}/file`)}
                                    {:then positions}
                                        {#each positions as file (file.id)}
                                            <div class="jinya-media-tile jinya-media-tile--small">
                                                <img class="jinya-media-tile__img jinya-media-tile__img--small"
                                                     src={`${getHost()}${file.file.path}`}>
                                            </div>
                                        {/each}
                                    {/await}
                                </div>
                            </div>
                        {:else if segment.html}
                            <div class:jinya-designer-item--selected={selectedSegment === segment}
                                 on:click={() => selectSegment(segment)}
                                 class="jinya-designer-item jinya-designer-item--html">
                                <span class="jinya-designer-item__title">{$_('pages_and_forms.segment.designer.html')}</span>
                                <div class="jinya-designer-item__details jinya-designer-item__details--html">{@html segment.html}</div>
                            </div>
                        {/if}
                    {/each}
                </div>
                <div bind:this={segmentToolboxElement} class="jinya-designer__toolbox">
                    <div data-type="gallery" class="jinya-designer-item__template">
                        <span class="jinya-designer__drag-handle"></span>
                        <span>{$_('pages_and_forms.segment.designer.gallery')}</span>
                    </div>
                    <div data-type="file" class="jinya-designer-item__template">
                        <span class="jinya-designer__drag-handle"></span>
                        <span>{$_('pages_and_forms.segment.designer.file')}</span>
                    </div>
                    <div data-type="html" class="jinya-designer-item__template">
                        <span class="jinya-designer__drag-handle"></span>
                        <span>{$_('pages_and_forms.segment.designer.html')}</span>
                    </div>
                </div>
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
{#if editSegmentOpen}
    <div class="cosmo-modal__backdrop"></div>
    <div class="cosmo-modal__container">
        <div class="cosmo-modal">
            <h1 class="cosmo-modal__title">{$_('pages_and_forms.segment.designer.edit.title')}</h1>
            <div class="cosmo-modal__content">
                {#if selectedSegmentType === 'file'}
                    <div class="cosmo-input__group">
                        <label for="editSegmentFile"
                               class="cosmo-label">{$_('pages_and_forms.segment.designer.edit.file')}</label>
                        <select required bind:value={editSegmentFile} id="editSegmentFile" class="cosmo-select">
                            {#each files as file}
                                <option value={file.id}>{file.name}</option>
                            {/each}
                        </select>
                        <div class="cosmo-checkbox__group">
                            <input class="cosmo-checkbox" type="checkbox" id="editSegmentLink"
                                   bind:checked={editSegmentIsLink}>
                            <label for="editSegmentLink">{$_('pages_and_forms.segment.designer.edit.has_link')}</label>
                        </div>
                        {#if editSegmentIsLink}
                            <label for="editSegmentTarget"
                                   class="cosmo-label">{$_('pages_and_forms.segment.designer.edit.target')}</label>
                            <input required bind:value={editSegmentTarget} type="text" id="editSegmentTarget"
                                   class="cosmo-input">
                        {/if}
                    </div>
                {:else if selectedSegmentType === 'gallery'}
                    <div class="cosmo-input__group">
                        <label for="editSegmentGallery"
                               class="cosmo-label">{$_('pages_and_forms.segment.designer.edit.gallery')}</label>
                        <select required bind:value={editSegmentGallery} id="editSegmentGallery" class="cosmo-select">
                            {#each galleries as gallery}
                                <option value={gallery.id}>{gallery.name}</option>
                            {/each}
                        </select>
                    </div>
                {:else if selectedSegmentType === 'html'}
                    <div class="cosmo-input__group">
                        <textarea bind:this={editSegmentHtmlElement}></textarea>
                    </div>
                {/if}
            </div>
            <div class="cosmo-modal__button-bar">
                <button class="cosmo-button"
                        on:click={cancelEditSegment}>{$_('pages_and_forms.segment.designer.edit.cancel')}</button>
                <button class="cosmo-button"
                        on:click={updateSegment}>{$_('pages_and_forms.segment.designer.edit.update')}</button>
            </div>
        </div>
    </div>
{/if}