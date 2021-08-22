<script>
  import { get, getHost, httpDelete, post, put } from '../../http/request';
  import { onMount } from 'svelte';
  import { writable, get as getStore } from 'svelte/store';
  import { _ } from 'svelte-i18n';
  import Sortable from 'sortablejs';
  import { jinyaConfirm } from '../../ui/confirm';
  import { jinyaAlert } from '../../ui/alert';

  let filesStore = writable([]);
  let positionsStore = writable([]);
  let createGalleryName = '';
  let createGalleryOrientation = 'horizontal';
  let createGalleryType = 'sequence';
  let createGalleryDescription = '';
  let createGalleryOpen = false;

  let editGalleryName = '';
  let editGalleryOrientation = '';
  let editGalleryType = '';
  let editGalleryDescription = '';
  let editGalleryOpen = false;

  let galleries = [];
  let selectedGallery = {};

  let positions = [];
  let files = [];
  let allFiles = {};
  let loading = false;

  async function selectGallery(gallery) {
    selectedGallery = gallery;
    loading = true;
    positions = await get(`/api/media/gallery/${gallery.id}/file`);
    allFiles = await get('/api/media/file');
    files = allFiles.items.filter(file => {
      const idx = positions.map(position => position.file).findIndex(position => position.id === file.id);
      return idx === -1;
    });
    positions.sort((a, b) => {
      if (a.position < b.position) {
        return -1;
      }

      if (a.position > b.position) {
        return 1;
      }

      return 0;
    });

    if (filesStore.length > 0) {
      filesStore.update(() => {
        return files;
      });
    } else {
      filesStore = writable(files);
    }
    if (positionsStore.length > 0) {
      positionsStore.update(() => {
        return positions;
      });
    } else {
      positionsStore = writable(positions);
    }
    loading = false;
  }

  const filesSortable = function (node, {items, options}) {
    let sortable;
    let toRemoveOnDestroy = [];
    options = Object.assign(options, {
      async onAdd(e) {
        e.item.classList.add('jinya-media-tile--medium');
        const idx = e.oldIndex;
        const position = positions[idx];
        await httpDelete(`/api/media/gallery/${selectedGallery.id}/file/${position.position}`);
        positions = await get(`/api/media/gallery/${selectedGallery.id}/file`);

        files = allFiles.items.filter(file => {
          const idx = positions.map(position => position.file).findIndex(position => position.id === file.id);
          return idx === -1;
        });

        e.from.appendChild(e.item);

        positionsStore = writable(positions);
        filesStore = writable(files);
      },
    });

    sortable = new Sortable(node, options);

    return {
      update(items) {
        sortable.destroy();
        sortable = new Sortable(node, options);
      },
      destroy() {
        sortable.destroy();
      }
    };
  };

  const positionsSortable = function (node, {items, options}) {
    let sortable;
    options = Object.assign(options, {
      async onAdd(e) {
        e.item.classList.remove('jinya-media-tile--medium');
        const fileIdx = e.oldIndex;
        const file = files[fileIdx];
        const dropIdx = e.newIndex;
        let position;
        if (positions.length === 0) {
          position = 0;
        } else if (positions.length === dropIdx) {
          position = positions[positions.length - 1].position + 2;
        } else {
          position = positions[dropIdx].position;
        }
        const galleryFilePosition = await post(`/api/media/gallery/${selectedGallery.id}/file`, {
          file: file.id,
          position,
        });

        positions = await get(`/api/media/gallery/${selectedGallery.id}/file`);

        files = allFiles.items.filter(file => {
          const idx = positions.map(position => position.file).findIndex(position => position.id === file.id);
          return idx === -1;
        });

        e.from.appendChild(e.item);

        positionsStore = writable(positions);
        filesStore = writable(files);
      },
      async onUpdate(e) {
        const oldPosition = positions[e.oldIndex].position;
        const dropIdx = e.newIndex;
        const position = positions[dropIdx].position;
        await put(`/api/media/gallery/${selectedGallery.id}/file/${oldPosition}`, {
          newPosition: position > oldPosition ? position + 1 : position,
        });

        positions = await get(`/api/media/gallery/${selectedGallery.id}/file`);
        positionsStore.update(() => {
          return positions;
        });
      },
    });

    sortable = new Sortable(node, options);

    return {
      update(items) {
        sortable.destroy();
        sortable = new Sortable(node, options);
      },
      destroy() {
        sortable.destroy();
      }
    };
  };

  async function loadGalleries() {
    const response = await get('/api/media/gallery');
    galleries = response.items;
    if (galleries.length > 0) {
      await selectGallery(galleries[0]);
    }
  }

  async function deleteGallery() {
    const result = await jinyaConfirm($_('media.galleries.delete.title'), $_('media.galleries.delete.message', {values: selectedGallery}), $_('media.galleries.delete.delete'), $_('media.galleries.delete.keep'));
    if (result) {
      await httpDelete(`/api/media/gallery/${selectedGallery.id}`);
      await loadGalleries();
    }
  }

  async function createGallery() {
    if (createGalleryName) {
      try {
        await post('/api/media/gallery', {
          name: createGalleryName,
          description: createGalleryDescription,
          orientation: createGalleryOrientation,
          type: createGalleryType,
        });
        createGalleryOpen = false;
        await loadGalleries();
        const gallery = galleries.find(item => item.name === createGalleryName);
        await selectGallery(gallery);
        createGalleryName = '';
        createGalleryDescription = '';
        createGalleryOrientation = 'horizontal';
        createGalleryType = 'sequence';
      } catch (e) {
        if (e.status === 409) {
          await jinyaAlert($_('media.galleries.create.error.title'), $_('media.galleries.create.error.conflict'), $_('alert.dismiss'));
        } else {
          await jinyaAlert($_('media.galleries.create.error.title'), $_('media.galleries.create.error.generic'), $_('alert.dismiss'));
        }
      }
    }
  }

  function openEdit() {
    editGalleryOpen = true;
    editGalleryName = selectedGallery.name;
    editGalleryDescription = selectedGallery.description;
    editGalleryOrientation = selectedGallery.orientation;
    editGalleryType = selectedGallery.type;
  }

  async function updateGallery() {
    if (editGalleryName) {
      try {
        await put(`/api/media/gallery/${selectedGallery.id}`, {
          name: editGalleryName,
          description: editGalleryDescription,
          orientation: editGalleryOrientation,
          type: editGalleryType,
        });
        editGalleryOpen = false;
        await loadGalleries();
        const gallery = galleries.find(item => item.name === editGalleryName);
        await selectGallery(gallery);
        editGalleryName = '';
        editGalleryDescription = '';
        editGalleryOrientation = 'horizontal';
        editGalleryType = 'sequence';
      } catch (e) {
        if (e.status === 409) {
          await jinyaAlert($_('media.galleries.edit.error.title'), $_('media.galleries.edit.error.conflict'), $_('alert.dismiss'));
        } else {
          await jinyaAlert($_('media.galleries.edit.error.title'), $_('media.galleries.edit.error.generic'), $_('alert.dismiss'));
        }
      }
    }
  }

  onMount(async () => {
    await loadGalleries();
  });
</script>

<div class="cosmo-list">
    <nav class="cosmo-list__items">
        {#each galleries as gallery (gallery.id)}
            <a class:cosmo-list__item--active={gallery.id === selectedGallery.id} class="cosmo-list__item"
               on:click={() => selectGallery(gallery)}>{gallery.name}</a>
        {/each}
        <button on:click={() => createGalleryOpen = true}
                class="cosmo-button cosmo-button--full-width">{$_('media.galleries.action.new')}</button>
    </nav>
    {#if loading}
        <div class="cosmo-list__content jinya-loader__container">
            <div class="jinya-loader"></div>
        </div>
    {:else if selectedGallery.id !== undefined}
        <div class="cosmo-list__content jinya-designer">
            <div class="jinya-designer__title">
                <span class="cosmo-title">#{selectedGallery.id} {selectedGallery.name}</span>
                <span class="cosmo-title">
                    {$_(`media.galleries.designer.title.${selectedGallery.orientation.toLowerCase()}_${selectedGallery.type.toLowerCase()}`)}
                </span>
            </div>
            <div class="cosmo-toolbar cosmo-toolbar--designer">
                <div class="cosmo-toolbar__group">
                    <button disabled={!selectedGallery} on:click={openEdit}
                            class="cosmo-button">{$_('media.galleries.action.edit')}</button>
                    <button disabled={!selectedGallery} on:click={deleteGallery}
                            class="cosmo-button">{$_('media.galleries.action.delete')}</button>
                </div>
            </div>
            <div class="jinya-designer__content">
                <div class="jinya-designer__result"
                     use:positionsSortable="{{ items: positionsStore, options: { group: 'gallery', sort: true, } }}">
                    {#each getStore(positionsStore) as position, index (position.id)}
                        <img class="jinya-media-tile jinya-media-tile--draggable" alt={position.name}
                             src={`${getHost()}${position.file?.path}`}>
                    {/each}
                </div>
                <div class="jinya-designer__toolbox"
                     use:filesSortable="{{ items: filesStore, options: { group: 'gallery', sort: false, } }}">
                    {#each getStore(filesStore) as file, index (file.id)}
                        <img class="jinya-media-tile jinya-media-tile--medium jinya-media-tile--draggable"
                             alt={file.name} src={`${getHost()}${file.path}`}>
                    {/each}
                </div>
            </div>
        </div>
    {/if}
</div>
{#if createGalleryOpen}
    <div class="cosmo-modal__backdrop"></div>
    <div class="cosmo-modal__container">
        <div class="cosmo-modal">
            <h1 class="cosmo-modal__title">{$_('media.galleries.create.title')}</h1>
            <div class="cosmo-modal__content">
                <div class="cosmo-input__group">
                    <label for="createGalleryName" class="cosmo-label">{$_('media.galleries.create.name')}</label>
                    <input required bind:value={createGalleryName} type="text" id="createGalleryName"
                           class="cosmo-input">
                    <span class="cosmo-label cosmo-label--radio">{$_('media.galleries.create.orientation')}</span>
                    <div class="cosmo-radio__group">
                        <input class="cosmo-radio" type="radio" id="createGalleryOrientationHorizontal"
                               bind:group={createGalleryOrientation} value="horizontal">
                        <label for="createGalleryOrientationHorizontal">{$_('media.galleries.create.horizontal')}</label>
                        <input class="cosmo-radio" type="radio" id="createGalleryOrientationVertical"
                               bind:group={createGalleryOrientation} value="vertical">
                        <label for="createGalleryOrientationVertical">{$_('media.galleries.create.vertical')}</label>
                    </div>
                    <span class="cosmo-label cosmo-label--radio">{$_('media.galleries.create.type')}</span>
                    <div class="cosmo-radio__group">
                        <input class="cosmo-radio" type="radio" id="createGalleryTypeMasonry"
                               bind:group={createGalleryType} value="masonry">
                        <label for="createGalleryTypeMasonry">{$_('media.galleries.create.masonry')}</label>
                        <input class="cosmo-radio" type="radio" id="createGalleryTypeSequence"
                               bind:group={createGalleryType} value="sequence">
                        <label for="createGalleryTypeSequence">{$_('media.galleries.create.sequence')}</label>
                    </div>
                    <label for="createGalleryDescription"
                           class="cosmo-label cosmo-label--textarea">{$_('media.galleries.create.description')}</label>
                    <textarea bind:value={createGalleryDescription} rows="5" id="createGalleryDescription"
                              class="cosmo-textarea"></textarea>
                </div>
            </div>
            <div class="cosmo-modal__button-bar">
                <button class="cosmo-button"
                        on:click={() => createGalleryOpen = false}>{$_('media.galleries.create.cancel')}</button>
                <button class="cosmo-button" on:click={createGallery}>{$_('media.galleries.create.create')}</button>
            </div>
        </div>
    </div>
{/if}
{#if editGalleryOpen}
    <div class="cosmo-modal__backdrop"></div>
    <div class="cosmo-modal__container">
        <div class="cosmo-modal">
            <h1 class="cosmo-modal__title">{$_('media.galleries.edit.title')}</h1>
            <div class="cosmo-modal__content">
                <div class="cosmo-input__group">
                    <label for="editGalleryName" class="cosmo-label">{$_('media.galleries.edit.name')}</label>
                    <input required bind:value={editGalleryName} type="text" id="editGalleryName"
                           class="cosmo-input">
                    <span class="cosmo-label cosmo-label--radio">{$_('media.galleries.edit.orientation')}</span>
                    <div class="cosmo-radio__group">
                        <input class="cosmo-radio" type="radio" id="editGalleryOrientationHorizontal"
                               bind:group={editGalleryOrientation} value="horizontal">
                        <label for="editGalleryOrientationHorizontal">{$_('media.galleries.edit.horizontal')}</label>
                        <input class="cosmo-radio" type="radio" id="editGalleryOrientationVertical"
                               bind:group={editGalleryOrientation} value="vertical">
                        <label for="editGalleryOrientationVertical">{$_('media.galleries.edit.vertical')}</label>
                    </div>
                    <span class="cosmo-label cosmo-label--radio">{$_('media.galleries.edit.type')}</span>
                    <div class="cosmo-radio__group">
                        <input class="cosmo-radio" type="radio" id="editGalleryTypeMasonry"
                               bind:group={editGalleryType} value="masonry">
                        <label for="editGalleryTypeMasonry">{$_('media.galleries.edit.masonry')}</label>
                        <input class="cosmo-radio" type="radio" id="editGalleryTypeSequence"
                               bind:group={editGalleryType} value="sequence">
                        <label for="editGalleryTypeSequence">{$_('media.galleries.edit.sequence')}</label>
                    </div>
                    <label for="editGalleryDescription"
                           class="cosmo-label cosmo-label--textarea">{$_('media.galleries.edit.description')}</label>
                    <textarea bind:value={editGalleryDescription} rows="5" id="editGalleryDescription"
                              class="cosmo-textarea"></textarea>
                </div>
            </div>
            <div class="cosmo-modal__button-bar">
                <button class="cosmo-button"
                        on:click={() => editGalleryOpen = false}>{$_('media.galleries.edit.cancel')}</button>
                <button class="cosmo-button" on:click={updateGallery}>{$_('media.galleries.edit.update')}</button>
            </div>
        </div>
    </div>
{/if}
