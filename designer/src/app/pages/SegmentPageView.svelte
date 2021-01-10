<script>
  import { get, getHost, httpDelete, post, put } from '../../http/request';
  import { onMount, tick } from 'svelte';
  import { _ } from 'svelte-i18n';
  import { jinyaConfirm } from '../../ui/confirm';
  import { jinyaAlert } from '../../ui/alert';
  import Sortable from 'sortablejs';
  import { createTiny } from '../../ui/tiny';

  import 'tinymce/icons/default';
  import 'tinymce/themes/silver';
  import 'tinymce/plugins/advlist';
  import 'tinymce/plugins/anchor';
  import 'tinymce/plugins/autolink';
  import 'tinymce/plugins/charmap';
  import 'tinymce/plugins/code';
  import 'tinymce/plugins/fullscreen';
  import 'tinymce/plugins/help';
  import 'tinymce/plugins/hr';
  import 'tinymce/plugins/image';
  import 'tinymce/plugins/link';
  import 'tinymce/plugins/lists';
  import 'tinymce/plugins/media';
  import 'tinymce/plugins/paste';
  import 'tinymce/plugins/searchreplace';
  import 'tinymce/plugins/table';
  import 'tinymce/plugins/visualblocks';
  import 'tinymce/plugins/wordcount';

  let createPageName = '';
  let createPageOpen = false;

  let editPageName = '';
  let editPageOpen = false;

  let pages = [];
  let segments = [];
  let files = [];
  let galleries = [];

  let selectedPage = null;
  let selectedSegment = null;
  let segmentToolboxElement;
  let segmentListElement;
  let segmentEditorTiny;

  let editSegmentOpen;
  let editSegmentFile;
  let editSegmentIsLink;
  let editSegmentTarget;
  let editSegmentGallery;
  let editSegmentHtml;
  let editSegmentHtmlElement;
  let createNewSegment = false;
  let createPosition;
  let createNewSegmentType;

  $: if (segmentToolboxElement instanceof HTMLElement) {
    new Sortable(segmentToolboxElement, {
      group: { name: 'segment_page', put: false, pull: 'clone' },
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
      group: { name: 'segment_page', put: true, pull: false },
      sort: true,
      async onAdd(e) {
        createNewSegment = true;
        const dropIdx = e.newIndex;
        if (segments.length === 0) {
          createPosition = 0;
        } else if (segments.length === dropIdx) {
          createPosition = segments[segments.length - 1].position + 2;
        } else {
          createPosition = segments[dropIdx].position;
        }
        createNewSegmentType = e.item.getAttribute('data-type');
        switch (createNewSegmentType) {
          case 'gallery':
            selectedSegment = { gallery: galleries[0] };
            break;
          case 'file':
            selectedSegment = { file: files[0] };
            break;
          case 'html':
            selectedSegment = { html: '<p></p>' };
            break;
        }
        await editSegment();
      },
      async onUpdate(e) {
        const oldPosition = e.item.getAttribute('data-old-position');
        const dropIdx = e.newIndex;
        const position = segments[dropIdx].position;
        await put(`/api/segment-page/${selectedPage.id}/segment/${oldPosition}`, {
          newPosition: position,
        });
        await selectPage(selectedPage);
      },
    });
  }

  async function selectPage(page) {
    selectedPage = page;
    editPageName = selectedPage.name;
    segments = await get(`/api/segment-page/${selectedPage.id}/segment`);
    selectedSegment = null;
  }

  async function loadPages() {
    const response = await get('/api/segment-page');
    pages = response.items;
    if (pages.length > 0) {
      await selectPage(pages[0]);
    }
  }

  async function deletePage() {
    const result = await jinyaConfirm($_('pages_and_forms.segment.delete.title'), $_('pages_and_forms.segment.delete.message', { values: selectedPage }), $_('pages_and_forms.segment.delete.delete'), $_('pages_and_forms.segment.delete.keep'));
    if (result) {
      await httpDelete(`/api/segment-page/${selectedPage.id}`);
      await loadPages();
    }
  }

  async function createPage() {
    if (createPageName) {
      try {
        await post('/api/segment-page', {
          name: createPageName,
        });
        createPageOpen = false;
        await loadPages();
        const page = pages.find(item => item.name === createPageName);
        await selectPage(page);
        createPageName = '';
      } catch (e) {
        if (e.status === 409) {
          await jinyaAlert($_('pages_and_forms.segment.create.error.title'), $_('pages_and_forms.segment.create.error.conflict'), $_('alert.dismiss'));
        } else {
          await jinyaAlert($_('pages_and_forms.segment.create.error.title'), $_('pages_and_forms.segment.create.error.generic'), $_('alert.dismiss'));
        }
      }
    }
  }

  function openEdit() {
    editPageOpen = true;
  }

  async function updatePage() {
    if (editPageName) {
      try {
        const name = editPageName;
        await put(`/api/segment-page/${selectedPage.id}`, {
          name,
        });
        editPageOpen = false;
        await loadPages();
        const page = pages.find(item => item.name === name);
        await selectPage(page);
      } catch (e) {
        if (e.status === 409) {
          await jinyaAlert($_('pages_and_forms.segment.edit.error.title'), $_('pages_and_forms.segment.edit.error.conflict'), $_('alert.dismiss'));
        } else {
          await jinyaAlert($_('pages_and_forms.segment.edit.error.title'), $_('pages_and_forms.segment.edit.error.generic'), $_('alert.dismiss'));
        }
      }
    }
  }

  function onCreateCancel() {
    createPageName = '';
    createPageOpen = false;
  }

  function onEditCancel() {
    editPageOpen = false;
    editPageName = selectedPage.name;
  }

  async function editSegment() {
    editSegmentOpen = true;
    editSegmentFile = selectedSegment.file?.id;
    editSegmentIsLink = selectedSegment.action === 'link';
    editSegmentTarget = selectedSegment.target;
    editSegmentGallery = selectedSegment.gallery?.id;
    if (selectedSegment.html) {
      await tick();
      segmentEditorTiny = await createTiny(editSegmentHtmlElement);
      segmentEditorTiny.setContent(selectedSegment.html);
    }
  }

  async function deleteSegment() {
    const result = await jinyaConfirm($_('pages_and_forms.segment.delete_segment.title'), $_('pages_and_forms.segment.delete_segment.message', { values: selectedPage }), $_('pages_and_forms.segment.delete_segment.delete'), $_('pages_and_forms.segment.delete_segment.keep'));
    if (result) {
      await httpDelete(`/api/segment-page/${selectedPage.id}/segment/${selectedSegment.position}`);
      await selectPage(selectedPage);
    }
  }

  function selectSegment(segment) {
    selectedSegment = segment;
  }

  function cancelEditSegment() {
    editSegmentOpen = false;
    editSegmentFile = null;
    editSegmentIsLink = false;
    editSegmentTarget = null;
    editSegmentGallery = null;
    editSegmentHtml = null;
    segmentEditorTiny = null;
  }

  async function updateSegment() {
    if (createNewSegment) {
      switch (createNewSegmentType) {
        case 'file':
          await post(`/api/segment-page/${selectedPage.id}/segment/file`, {
            file: editSegmentFile,
            target: editSegmentTarget,
            action: editSegmentIsLink ? 'link' : 'none',
            position: createPosition,
          });
          break;
        case 'gallery':
          await post(`/api/segment-page/${selectedPage.id}/segment/gallery`, {
            gallery: editSegmentGallery,
            position: createPosition,
          });
          break;
        case 'html':
          await post(`/api/segment-page/${selectedPage.id}/segment/html`, {
            html: segmentEditorTiny.getContent(),
            position: createPosition,
          });
          break;
      }
      createNewSegment = false;
    } else {
      const data = {};
      if (selectedSegment.file) {
        data.action = editSegmentIsLink ? 'link' : 'none';
        data.file = editSegmentFile;
        data.target = editSegmentTarget;
      } else if (selectedSegment.gallery) {
        data.gallery = editSegmentGallery;
      } else if (selectedSegment.html) {
        data.html = segmentEditorTiny.getContent();
      } else {
        throw new Error('Unsupported');
      }

      await put(`/api/segment-page/${selectedPage.id}/segment/${selectedSegment.position}`, data);
    }
    cancelEditSegment();
    await selectPage(selectedPage);
  }

  onMount(async () => {
    await loadPages();
    const fileResult = await get('/api/media/file');
    files = fileResult.items ?? [];
    const galleryResult = await get('/api/media/gallery');
    galleries = galleryResult.items ?? [];
  });
</script>

<div class="cosmo-list">
    <nav class="cosmo-list__items">
        {#each pages as page (page.id)}
            <a class:cosmo-list__item--active={page.id === selectedPage.id} class="cosmo-list__item"
               on:click={() => selectPage(page)}>{page.name}</a>
        {/each}
        <button on:click={() => createPageOpen = true}
                class="cosmo-button cosmo-button--full-width">{$_('pages_and_forms.segment.action.new')}</button>
    </nav>
    <div class="cosmo-list__content jinya-designer">
        {#if selectedPage}
            <div class="jinya-designer__title">
                <span class="cosmo-title">{selectedPage.name}</span>
            </div>
            <div class="cosmo-toolbar cosmo-toolbar--designer">
                <div class="cosmo-toolbar__group">
                    <button disabled={!selectedPage} on:click={openEdit}
                            class="cosmo-button">{$_('pages_and_forms.segment.action.edit')}</button>
                    <button disabled={!selectedPage} on:click={deletePage}
                            class="cosmo-button">{$_('pages_and_forms.segment.action.delete')}</button>
                </div>
                <div class="cosmo-toolbar__group">
                    <button disabled={!selectedSegment} on:click={editSegment}
                            class="cosmo-button">{$_('pages_and_forms.segment.action.edit_segment')}</button>
                    <button disabled={!selectedSegment} on:click={deleteSegment}
                            class="cosmo-button">{$_('pages_and_forms.segment.action.delete_segment')}</button>
                </div>
            </div>
        {/if}
        <div class="jinya-designer__content">
            <div bind:this={segmentListElement} class="jinya-designer__result jinya-designer__result--horizontal">
                {#each segments as segment (segment.id)}
                    {#if segment.file}
                        <div data-old-position={segment.position}
                             class:jinya-designer-item--selected={selectedSegment === segment}
                             class:jinya-designer-item--file-selected={selectedSegment === segment}
                             on:click={() => selectSegment(segment)}
                             class="jinya-designer-item jinya-designer-item--file">
                            <img class="jinya-segment__image" src={`${getHost()}${segment.file.path}`}
                                 alt={segment.file.name}>
                            <div class="jinya-designer-item__details jinya-designer-item__details--file"
                                 class:jinya-designer-item__details--file-selected={selectedSegment === segment}>
                                <span class="jinya-designer-item__title">{$_('pages_and_forms.segment.designer.file')}</span>
                                <dl class="jinya-segment__action">
                                    <dt class="jinya-segment__label">{$_('pages_and_forms.segment.designer.action')}</dt>
                                    <dd class="jinya-segment__content">{$_(`pages_and_forms.segment.designer.action_${segment.action}`)}</dd>
                                    {#if segment.action === 'link'}
                                        <dt class="jinya-segment__label">{$_('pages_and_forms.segment.designer.link')}</dt>
                                        <dd class="jinya-segment__content">{segment.target}</dd>
                                    {/if}
                                </dl>
                            </div>
                        </div>
                    {:else if segment.gallery}
                        <div data-old-position={segment.position}
                             class:jinya-designer-item--selected={selectedSegment === segment}
                             on:click={() => selectSegment(segment)}
                             class="jinya-designer-item jinya-designer-item--gallery">
                            <span class="jinya-designer-item__title">{$_('pages_and_forms.segment.designer.gallery')}</span>
                            <span class="jinya-designer-item__details jinya-designer-item__details--gallery">{segment.gallery.name}</span>
                        </div>
                    {:else if segment.html}
                        <div data-old-position={segment.position}
                             class:jinya-designer-item--selected={selectedSegment === segment}
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
</div>
{#if createPageOpen}
    <div class="cosmo-modal__backdrop"></div>
    <div class="cosmo-modal__container">
        <div class="cosmo-modal">
            <h1 class="cosmo-modal__title">{$_('pages_and_forms.segment.create.title')}</h1>
            <div class="cosmo-modal__content">
                <div class="cosmo-input__group">
                    <label for="createPageName" class="cosmo-label">{$_('pages_and_forms.segment.create.name')}</label>
                    <input required bind:value={createPageName} type="text" id="createPageName" class="cosmo-input">
                </div>
            </div>
            <div class="cosmo-modal__button-bar">
                <button class="cosmo-button"
                        on:click={onCreateCancel}>{$_('pages_and_forms.segment.create.cancel')}</button>
                <button class="cosmo-button"
                        on:click={createPage}>{$_('pages_and_forms.segment.create.create')}</button>
            </div>
        </div>
    </div>
{/if}
{#if editPageOpen}
    <div class="cosmo-modal__backdrop"></div>
    <div class="cosmo-modal__container">
        <div class="cosmo-modal">
            <h1 class="cosmo-modal__title">{$_('pages_and_forms.segment.edit.title')}</h1>
            <div class="cosmo-modal__content">
                <div class="cosmo-input__group">
                    <label for="editPageName" class="cosmo-label">{$_('pages_and_forms.segment.edit.name')}</label>
                    <input required bind:value={editPageName} type="text" id="editPageName" class="cosmo-input">
                </div>
            </div>
            <div class="cosmo-modal__button-bar">
                <button class="cosmo-button"
                        on:click={onEditCancel}>{$_('pages_and_forms.segment.edit.cancel')}</button>
                <button class="cosmo-button"
                        on:click={updatePage}>{$_('pages_and_forms.segment.edit.update')}</button>
            </div>
        </div>
    </div>
{/if}
{#if editSegmentOpen}
    <div class="cosmo-modal__backdrop"></div>
    <div class="cosmo-modal__container">
        <div class="cosmo-modal">
            <h1 class="cosmo-modal__title">{$_('pages_and_forms.segment.designer.edit.title')}</h1>
            <div class="cosmo-modal__content">
                {#if selectedSegment.file}
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
                {:else if selectedSegment.gallery}
                    <div class="cosmo-input__group">
                        <label for="editSegmentGallery"
                               class="cosmo-label">{$_('pages_and_forms.segment.designer.edit.gallery')}</label>
                        <select required bind:value={editSegmentGallery} id="editSegmentGallery" class="cosmo-select">
                            {#each galleries as gallery}
                                <option value={gallery.id}>{gallery.name}</option>
                            {/each}
                        </select>
                    </div>
                {:else if selectedSegment.html}
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
