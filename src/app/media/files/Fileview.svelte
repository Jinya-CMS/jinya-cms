<script>
  import { onMount } from 'svelte';
  import { _ } from 'svelte-i18n';
  import { get, getHost, httpDelete, post, put, upload } from '../../../http/request';
  import { jinyaConfirm } from '../../../ui/confirm';
  import { jinyaAlert } from '../../../ui/alert';

  let files = [];
  let selectedFile;

  let uploadSingleFileOpen = false;
  let uploadSingleFileName;
  let uploadSingleFileFile;
  let uploadSingleFileFileName = '';

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

  async function deleteFile() {
    const reallyDelete = await jinyaConfirm($_('media.files.delete.title'), $_('media.files.delete.message', { values: selectedFile }), $_('media.files.delete.approve'), $_('media.files.delete.decline'));
    if (reallyDelete) {
      await httpDelete(`/api/media/file/${selectedFile.id}`);
      const result = await get('/api/media/file');
      files = result.items ?? [];
      selectedFile = null;
    }
  }

  function onUploadCloseClick() {
    uploadSingleFileOpen = false;
    uploadSingleFileFile = null;
    uploadSingleFileFileName = '';
    uploadSingleFileName = '';
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
      onUploadCloseClick();
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
    <div class="jinya-toolbar jinya-toolbar--media">
        <div class="jinya-toolbar__group">
            <button on:click={() => uploadSingleFileOpen = true} class="jinya-button"
                    type="button">{$_('media.files.action.upload_single_file')}</button>
            <button class="jinya-button" type="button">{$_('media.files.action.upload_multiple_file')}</button>
        </div>
        <div class="jinya-toolbar__group">
            <button disabled={!selectedFile} class="jinya-button" on:click={onEditFileClick}
                    type="button">{$_('media.files.action.edit_file')}</button>
            <button disabled={!selectedFile} class="jinya-button" on:click={() => deleteFile()}
                    type="button">{$_('media.files.action.delete_file')}</button>
        </div>
    </div>
    <div class="jinya-media-tile__container">
        {#each files as file}
            <div on:click={() => selectedFile = file} class:jinya-media-tile--selected={selectedFile === file}
                 class="jinya-media-tile" data-title={file.name}>
                <img class="jinya-media-tile__img" src={`${getHost()}${file.path}`} alt={file.name}>
            </div>
        {/each}
    </div>
</div>
{#if uploadSingleFileOpen}
    <div class="jinya-modal__backdrop"></div>
    <div class="jinya-modal__container">
        <div class="jinya-modal">
            <h1 class="jinya-modal__title">{$_('media.files.upload_single_file.title')}</h1>
            <div class="jinya-modal__content">
                <div class="jinya-input__group">
                    <label for="uploadFileName" class="jinya-label">{$_('media.files.upload_single_file.name')}</label>
                    <input required bind:value={uploadSingleFileName} type="text" id="uploadFileName"
                           class="jinya-input">
                    <label for="uploadSingleFileFile"
                           class="jinya-label">{$_('media.files.upload_single_file.file')}</label>
                    <div class="jinya-input jinya-input--picker">
                        <label class="jinya-picker__name" for="uploadSingleFileFile">{uploadSingleFileFileName}</label>
                        <label class="jinya-picker__button" for="uploadSingleFileFile"><span
                                class="mdi mdi-upload mdi-24px"></span></label>
                        <input style="display: none" required bind:files={uploadSingleFileFile} type="file"
                               id="uploadSingleFileFile">
                    </div>
                </div>
            </div>
            <div class="jinya-modal__button-bar">
                <button class="jinya-button"
                        on:click={onUploadCloseClick}>{$_('media.files.upload_single_file.cancel')}</button>
                <button class="jinya-button"
                        on:click={onSingleFileUpload}>{$_('media.files.upload_single_file.upload')}</button>
            </div>
        </div>
    </div>
{/if}
{#if editFileOpen}
    <div class="jinya-modal__backdrop"></div>
    <div class="jinya-modal__container">
        <div class="jinya-modal">
            <h1 class="jinya-modal__title">{$_('media.files.edit.title')}</h1>
            <div class="jinya-modal__content">
                <div class="jinya-input__group">
                    <label for="uploadFileName" class="jinya-label">{$_('media.files.edit.name')}</label>
                    <input required bind:value={editFileName} type="text" id="editFileName" class="jinya-input">
                    <label for="editFileName"
                           class="jinya-label">{$_('media.files.edit.file')}</label>
                    <div class="jinya-input jinya-input--picker">
                        <label class="jinya-picker__name" for="editFileFile">{editFileFileName}</label>
                        <label class="jinya-picker__button" for="editFileFile"><span
                                class="mdi mdi-upload mdi-24px"></span></label>
                        <input style="display: none" required bind:files={editFileFile} type="file"
                               id="editFileFile">
                    </div>
                </div>
            </div>
            <div class="jinya-modal__button-bar">
                <button class="jinya-button" on:click={onEditCloseClick}>{$_('media.files.edit.cancel')}</button>
                <button class="jinya-button" on:click={onEditFileSave}>{$_('media.files.edit.save')}</button>
            </div>
        </div>
    </div>
{/if}
