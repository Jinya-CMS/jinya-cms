<script>
  import { get, getHost, httpDelete, post, put, upload } from '../../http/request';
  import { onMount } from 'svelte';
  import { _ } from 'svelte-i18n';
  import { jinyaConfirm } from '../../ui/confirm';
  import { jinyaAlert } from '../../ui/alert';
  import tinymce from 'tinymce';
  import ConflictError from '../../http/Error/ConflictError';

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

  let createPageTitle = '';
  let createPageOpen = false;

  let editPageTitle = '';
  let editPageOpen = false;

  let pages = [];
  let selectedPage = null;

  let editor;
  let tiny;

  async function selectPage(page) {
    selectedPage = page;
    tiny.setContent(page.content);
    editPageTitle = selectedPage.title;
  }

  async function loadPages() {
    const response = await get('/api/page');
    pages = response.items;
    if (pages.length > 0) {
      await selectPage(pages[0]);
    }
  }

  async function deletePage() {
    const result = await jinyaConfirm($_('pages_and_forms.simple.delete.title'), $_('pages_and_forms.simple.delete.message', { values: selectedPage }), $_('pages_and_forms.simple.delete.delete'), $_('pages_and_forms.simple.delete.keep'));
    if (result) {
      await httpDelete(`/api/page/${selectedPage.id}`);
      await loadPages();
    }
  }

  async function createPage() {
    if (createPageTitle) {
      try {
        await post('/api/page', {
          title: createPageTitle,
          content: '',
        });
        createPageOpen = false;
        await loadPages();
        const page = pages.find(item => item.title === createPageTitle);
        await selectPage(page);
        createPageTitle = '';
      } catch (e) {
        if (e.status === 409) {
          await jinyaAlert($_('pages_and_forms.simple.create.error.title'), $_('pages_and_forms.simple.create.error.conflict'), $_('alert.dismiss'));
        } else {
          await jinyaAlert($_('pages_and_forms.simple.create.error.title'), $_('pages_and_forms.simple.create.error.generic'), $_('alert.dismiss'));
        }
      }
    }
  }

  function openEdit() {
    editPageOpen = true;
  }

  async function updatePage() {
    if (editPageTitle) {
      let content = selectedPage.content;
      if (!editPageOpen) {
        content = tiny.getContent();
      }
      try {
        const title = editPageTitle;
        await put(`/api/page/${selectedPage.id}`, {
          title,
          content,
        });
        editPageOpen = false;
        await loadPages();
        const page = pages.find(item => item.title === title);
        await selectPage(page);
      } catch (e) {
        if (e.status === 409) {
          await jinyaAlert($_('pages_and_forms.simple.edit.error.title'), $_('pages_and_forms.simple.edit.error.conflict'), $_('alert.dismiss'));
        } else {
          await jinyaAlert($_('pages_and_forms.simple.edit.error.title'), $_('pages_and_forms.simple.edit.error.generic'), $_('alert.dismiss'));
        }
      }
    }
  }

  function onCreateCancel() {
    createPageTitle = '';
    createPageOpen = false;
  }

  function onEditCancel() {
    editPageOpen = false;
    editPageTitle = selectedPage.title;
  }

  function discardContent() {
    tiny.setContent(selectedPage.content);
  }

  onMount(async () => {
    tiny = (await tinymce.init({
      target: editor,
      object_resizing: true,
      relative_urls: false,
      image_advtab: true,
      remove_script_host: false,
      convert_urls: true,
      height: 'calc(100% - 37px)',
      width: '100%',
      async image_list(success) {
        const files = await get('/api/media/file');
        success(files.items.map((item) => ({ title: item.name, value: `${getHost()}${item.path}` })));
      },
      plugins: [
        'advlist',
        'anchor',
        'autolink',
        'charmap',
        'code',
        'fullscreen',
        'help',
        'hr',
        'image',
        'link',
        'lists',
        'media',
        'paste',
        'searchreplace',
        'table',
        'visualblocks',
        'wordcount',
      ],
      toolbar: 'undo redo | '
        + 'styleselect | '
        + 'bold italic | '
        + 'alignleft aligncenter alignright alignjustify | '
        + 'bullist numlist outdent indent | '
        + 'forecolor backcolor | '
        + 'link image | ',
      file_picker_type: 'image',
      file_picker_callback(cb) {
        const input = document.createElement('input');
        input.setAttribute('type', 'file');
        input.setAttribute('accept', 'image/*');

        input.onchange = async (event) => {
          const file = event.target.files[0];
          try {
            const { id } = await post('/api/media/file', { name: file.name });
            await put(`/api/media/file/${id}/content`);
            await upload(`/api/media/file/${id}/content/0`, file);
            await put(`/api/media/file/${id}/content/finish`);
            const uploadedFile = await get(`/api/media/file/${id}`);

            cb(`${getHost()}${uploadedFile.path}`, { title: file.name });
          } catch (e) {
            if (e instanceof ConflictError) {
              const files = await get(`/api/media/file?keyword=${encodeURIComponent(file.name)}`);
              const selectedFile = files.items[0];

              cb(`${getHost()}${selectedFile.path}`, { title: selectedFile.name, });
            }
          }
        };

        input.click();
      },
    }))[0];
    await loadPages();
  });
</script>

<div class="cosmo-list">
    <nav class="cosmo-list__items">
        {#each pages as page}
            <a class:cosmo-list__item--active={page.id === selectedPage.id} class="cosmo-list__item"
               on:click={() => selectPage(page)}>{page.title}</a>
        {/each}
        <button on:click={() => createPageOpen = true}
                class="cosmo-button cosmo-button--full-width">{$_('pages_and_forms.simple.action.new')}</button>
    </nav>
    <div class="cosmo-list__content jinya-designer">
        {#if selectedPage}
            <div class="jinya-designer__title">
                <span class="cosmo-title">{selectedPage.title}</span>
            </div>
            <div class="cosmo-toolbar cosmo-toolbar--designer">
                <div class="cosmo-toolbar__group">
                    <button disabled={!selectedPage} on:click={openEdit}
                            class="cosmo-button">{$_('pages_and_forms.simple.action.edit')}</button>
                    <button disabled={!selectedPage} on:click={deletePage}
                            class="cosmo-button">{$_('pages_and_forms.simple.action.delete')}</button>
                </div>
            </div>
        {/if}
        <div class="jinya-designer__content jinya-designer__content--simple-pages">
            <div bind:this={editor}></div>
            <div class="cosmo-button__container">
                <button on:click={discardContent}
                        class="cosmo-button">{$_('pages_and_forms.simple.action.discard_content')}</button>
                <button on:click={updatePage}
                        class="cosmo-button">{$_('pages_and_forms.simple.action.save_content')}</button>
            </div>
        </div>
    </div>
</div>
{#if createPageOpen}
    <div class="cosmo-modal__backdrop"></div>
    <div class="cosmo-modal__container">
        <div class="cosmo-modal">
            <h1 class="cosmo-modal__title">{$_('pages_and_forms.simple.create.title')}</h1>
            <div class="cosmo-modal__content">
                <div class="cosmo-input__group">
                    <label for="createPageTitle"
                           class="cosmo-label">{$_('pages_and_forms.simple.create.page_title')}</label>
                    <input required bind:value={createPageTitle} type="text" id="createPageTitle" class="cosmo-input">
                </div>
            </div>
            <div class="cosmo-modal__button-bar">
                <button class="cosmo-button"
                        on:click={onCreateCancel}>{$_('pages_and_forms.simple.create.cancel')}</button>
                <button class="cosmo-button"
                        on:click={createPage}>{$_('pages_and_forms.simple.create.create')}</button>
            </div>
        </div>
    </div>
{/if}
{#if editPageOpen}
    <div class="cosmo-modal__backdrop"></div>
    <div class="cosmo-modal__container">
        <div class="cosmo-modal">
            <h1 class="cosmo-modal__title">{$_('pages_and_forms.simple.edit.title')}</h1>
            <div class="cosmo-modal__content">
                <div class="cosmo-input__group">
                    <label for="editPageTitle"
                           class="cosmo-label">{$_('pages_and_forms.simple.edit.page_title')}</label>
                    <input required bind:value={editPageTitle} type="text" id="editPageTitle" class="cosmo-input">
                </div>
            </div>
            <div class="cosmo-modal__button-bar">
                <button class="cosmo-button"
                        on:click={onEditCancel}>{$_('pages_and_forms.simple.edit.cancel')}</button>
                <button class="cosmo-button"
                        on:click={updatePage}>{$_('pages_and_forms.simple.edit.update')}</button>
            </div>
        </div>
    </div>
{/if}
