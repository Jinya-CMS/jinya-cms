<script>
  import { createEventDispatcher, onMount } from 'svelte';
  import { _ } from 'svelte-i18n';
  import { get, getHost, httpDelete, post, put, upload } from '../../http/request';
  import { jinyaConfirm } from '../../ui/confirm';
  import { jinyaAlert } from '../../ui/alert';
  import { readDataUrl } from '../../files/reader';

  export let uploadDone = false;

  const dispatch = createEventDispatcher();
  let files = [];
  let selectedFile;

  let uploadSingleFileOpen = false;
  let uploadSingleFileName;
  let uploadSingleFileFile;
  let uploadSingleFileFileName = '';

  let uploadMultipleFilesOpen = false;
  let uploadMultipleFilesFiles = [];
  let uploadMultipleFilesSelectedFiles = [];

  let editFileOpen = false;
  let editFileName;
  let editFileFile;
  let editFileFileName = '';

  $: if (uploadSingleFileFile) {
    const file = uploadSingleFileFile[0];
    uploadSingleFileFileName = file.name;
    if (!uploadSingleFileName) {
      uploadSingleFileName = uploadSingleFileFileName;
    }
  }
  $: if (editFileFile) {
    const file = editFileFile[0];
    editFileFileName = file.name;
  }
  $: if (uploadMultipleFilesFiles) {
    const promises = [];
    for (const file of uploadMultipleFilesFiles) {
      promises.push(readDataUrl(file));
    }

    Promise.all(promises).then((files) => {
      uploadMultipleFilesSelectedFiles = files;
    });
  }
  $: if (uploadDone) {
    loadFiles();
  }

  async function deleteFile() {
    const reallyDelete = await jinyaConfirm($_('media.files.delete.title'), $_('media.files.delete.message', { values: selectedFile }), $_('media.files.delete.approve'), $_('media.files.delete.decline'));
    if (reallyDelete) {
      await httpDelete(`/api/media/file/${selectedFile.id}`);
      const result = await get('/api/media/file');
      files = result.items ?? [];
      selectedFile = null;
    }
  }

  function onSingleUploadCloseClick() {
    uploadSingleFileOpen = false;
    uploadSingleFileFile = null;
    uploadSingleFileFileName = '';
    uploadSingleFileName = '';
  }

  function onMultipleCloseClick() {
    uploadMultipleFilesOpen = false;
    uploadMultipleFilesFiles = [];
  }

  function onEditFileClick() {
    editFileName = selectedFile.name;
    editFileFileName = selectedFile.name;
    editFileOpen = true;
  }

  function onEditCloseClick() {
    editFileOpen = false;
    editFileFile = null;
    editFileFileName = '';
    editFileName = '';
  }

  function onMultipleFilesUpload() {
    dispatch('multiple-files-upload-start', uploadMultipleFilesFiles);
    onMultipleCloseClick();
  }

  async function onSingleFileUpload() {
    if (!(uploadSingleFileName && uploadSingleFileFile)) {
      return;
    }
    try {
      const postResult = await post('/api/media/file', { name: uploadSingleFileName });
      await put(`/api/media/file/${postResult.id}/content`);
      await upload(`/api/media/file/${postResult.id}/content/0`, uploadSingleFileFile[0]);
      await put(`/api/media/file/${postResult.id}/content/finish`);

      await loadFiles();
      onSingleUploadCloseClick();
    } catch (e) {
      if (e.status === 409) {
        await jinyaAlert($_('media.files.upload_single_file.error.title'), $_('media.files.upload_single_file.error.conflict'), $_('alert.dismiss'));
      } else {
        await jinyaAlert($_('media.files.upload_single_file.error.title'), $_('media.files.upload_single_file.error.generic'), $_('alert.dismiss'));
      }
    }
  }

  async function loadFiles() {
    const result = await get('/api/media/file');
    files = result.items ?? [];
  }

  async function onEditFileSave() {
    if (!editFileName) {
      return;
    }

    try {
      await put(`/api/media/file/${selectedFile.id}`, { name: editFileName });
      if (editFileFile && editFileFile.length > 0) {
        await put(`/api/media/file/${selectedFile.id}/content`);
        await upload(`/api/media/file/${selectedFile.id}/content/0`, editFileFile[0]);
        await put(`/api/media/file/${selectedFile.id}/content/finish`);
      }
      await loadFiles();
      onEditCloseClick();
    } catch (e) {
      if (e.status === 409) {
        await jinyaAlert($_('media.files.edit.error.title'), $_('media.files.edit.error.conflict'), $_('alert.dismiss'));
      } else {
        await jinyaAlert($_('media.files.edit.error.title'), $_('media.files.edit.error.generic'), $_('alert.dismiss'));
      }
    }
  }

  onMount(async () => {
    await loadFiles();
  });
</script>

<div class="jinya-media-view">
    <div class="cosmo-toolbar jinya-toolbar--media">
        <div class="cosmo-toolbar__group">
            <button on:click={() => uploadSingleFileOpen = true} class="cosmo-button"
                    type="button">{$_('media.files.action.upload_single_file')}</button>
            <button on:click={() => uploadMultipleFilesOpen = true} class="cosmo-button"
                    type="button">{$_('media.files.action.upload_multiple_file')}</button>
        </div>
        <div class="cosmo-toolbar__group">
            <button disabled={!selectedFile} class="cosmo-button" on:click={onEditFileClick}
                    type="button">{$_('media.files.action.edit_file')}</button>
            <button disabled={!selectedFile} class="cosmo-button" on:click={() => deleteFile()}
                    type="button">{$_('media.files.action.delete_file')}</button>
        </div>
    </div>
    <div class="jinya-media-tile__container">
        {#each files as file (file.id)}
            <div on:click={() => selectedFile = file} class:jinya-media-tile--selected={selectedFile === file}
                 class="jinya-media-tile" data-title={file.name}>
                <img class="jinya-media-tile__img" src={`${getHost()}${file.path}`} alt={file.name}>
            </div>
        {/each}
    </div>
</div>
{#if uploadSingleFileOpen}
    <div class="cosmo-modal__backdrop"></div>
    <div class="cosmo-modal__container">
        <div class="cosmo-modal">
            <h1 class="cosmo-modal__title">{$_('media.files.upload_single_file.title')}</h1>
            <div class="cosmo-modal__content">
                <div class="cosmo-input__group">
                    <label for="uploadFileName" class="cosmo-label">{$_('media.files.upload_single_file.name')}</label>
                    <input required bind:value={uploadSingleFileName} type="text" id="uploadFileName"
                           class="cosmo-input">
                    <label for="uploadSingleFileFile"
                           class="cosmo-label">{$_('media.files.upload_single_file.file')}</label>
                    <div class="cosmo-input cosmo-input--picker">
                        <label class="cosmo-picker__name" for="uploadSingleFileFile">{uploadSingleFileFileName}</label>
                        <label class="cosmo-picker__button" for="uploadSingleFileFile"><span
                                class="mdi mdi-upload mdi-24px"></span></label>
                        <input style="display: none" required bind:files={uploadSingleFileFile} type="file"
                               id="uploadSingleFileFile">
                    </div>
                </div>
            </div>
            <div class="cosmo-modal__button-bar">
                <button class="cosmo-button"
                        on:click={onSingleUploadCloseClick}>{$_('media.files.upload_single_file.cancel')}</button>
                <button class="cosmo-button"
                        on:click={onSingleFileUpload}>{$_('media.files.upload_single_file.upload')}</button>
            </div>
        </div>
    </div>
{/if}
{#if uploadMultipleFilesOpen}
    <div class="cosmo-modal__backdrop"></div>
    <div class="cosmo-modal__container">
        <div class="cosmo-modal jinya-modal--multiple-files">
            <h1 class="cosmo-modal__title">{$_('media.files.upload_multiple_files.title')}</h1>
            <div class="cosmo-modal__content">
                <div class="cosmo-input__group">
                    <label for="uploadMultipleFilesPicker"
                           class="cosmo-label">{$_('media.files.upload_multiple_files.files')}</label>
                    <div class="cosmo-input jinya-input--multiple-picker cosmo-input--picker">
                        <label class="cosmo-picker__name"
                               for="uploadSingleFileFile">{$_('media.files.upload_multiple_files.n_files_selected', { values: uploadMultipleFilesFiles })}</label>
                        <label class="cosmo-picker__button" for="uploadMultipleFilesPicker"><span
                                class="mdi mdi-upload mdi-24px"></span></label>
                        <input style="display: none" multiple required bind:files={uploadMultipleFilesFiles} type="file"
                               id="uploadMultipleFilesPicker">
                    </div>
                </div>
                <div class="jinya-media-tile__container--modal">
                    {#each uploadMultipleFilesFiles as file}
                        {#await readDataUrl(file)}
                        {:then src}
                            <div class="jinya-media-tile jinya-media-tile--small">
                                <img class="jinya-media-tile__img jinya-media-tile__img--small" {src}>
                            </div>
                        {/await}
                    {/each}
                </div>
            </div>
            <div class="cosmo-modal__button-bar">
                <button class="cosmo-button"
                        on:click={onMultipleCloseClick}>{$_('media.files.upload_multiple_files.cancel')}</button>
                <button class="cosmo-button"
                        on:click={onMultipleFilesUpload}>{$_('media.files.upload_multiple_files.upload')}</button>
            </div>
        </div>
    </div>
{/if}
{#if editFileOpen}
    <div class="cosmo-modal__backdrop"></div>
    <div class="cosmo-modal__container">
        <div class="cosmo-modal">
            <h1 class="cosmo-modal__title">{$_('media.files.edit.title')}</h1>
            <div class="cosmo-modal__content">
                <div class="cosmo-input__group">
                    <label for="uploadFileName" class="cosmo-label">{$_('media.files.edit.name')}</label>
                    <input required bind:value={editFileName} type="text" id="editFileName" class="cosmo-input">
                    <label for="editFileName"
                           class="cosmo-label">{$_('media.files.edit.file')}</label>
                    <div class="cosmo-input cosmo-input--picker">
                        <label class="cosmo-picker__name" for="editFileFile">{editFileFileName}</label>
                        <label class="cosmo-picker__button" for="editFileFile"><span
                                class="mdi mdi-upload mdi-24px"></span></label>
                        <input style="display: none" required bind:files={editFileFile} type="file"
                               id="editFileFile">
                    </div>
                </div>
            </div>
            <div class="cosmo-modal__button-bar">
                <button class="cosmo-button" on:click={onEditCloseClick}>{$_('media.files.edit.cancel')}</button>
                <button class="cosmo-button" on:click={onEditFileSave}>{$_('media.files.edit.save')}</button>
            </div>
        </div>
    </div>
{/if}
