<script>
  import { get, httpDelete, post, put } from '../../http/request';
  import { onMount, tick } from 'svelte';
  import { _ } from 'svelte-i18n';
  import Sortable from 'sortablejs';
  import { createTiny } from '../../ui/tiny';
  import { jinyaConfirm } from '../../ui/confirm';
  import { jinyaAlert } from '../../ui/alert';

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

  let createFormTitle = '';
  let createFormToAddress = '';
  let createFormOpen = false;

  let editFormTitle = '';
  let editFormToAddress = '';
  let editFormOpen = false;

  let forms = [];
  let formItems = [];

  let selectedForm = null;
  let selectedFormItem = null;
  let formItemToolboxElement;
  let formItemListElement;

  let editFormItemOpen;
  let editItemLabel;
  let editItemPlaceholder;
  let editItemHelpText;
  let editItemSpamFilter;
  let editItemOptions;
  let editItemIsSubject;
  let editItemIsFromAddress;
  let editItemIsRequired;

  let formDescriptionElement;
  let createNewFormItem = false;
  let createPosition;
  let formDescriptionTiny;

  $: if (formItemToolboxElement instanceof HTMLElement) {
    new Sortable(formItemToolboxElement, {
      group: { name: 'form_items', put: false, pull: 'clone' },
      sort: false,
      handle: '.jinya-designer__drag-handle',
      onEnd(e) {
        if (!e.to.classList.contains('jinya-designer__toolbox')) {
          e.item.remove();
        }
      }
    });
  }

  $: if (formItemListElement instanceof HTMLElement) {
    new Sortable(formItemListElement, {
      group: { name: 'form_items', put: true, pull: false },
      sort: true,
      async onAdd(e) {
        createNewFormItem = true;
        const dropIdx = e.newIndex;
        if (formItems.length === 0) {
          createPosition = 0;
        } else if (formItems.length === dropIdx) {
          createPosition = formItems[formItems.length - 1].position + 2;
        } else {
          createPosition = formItems[dropIdx].position;
        }
        selectedFormItem = { type: e.item.getAttribute('data-type') };
        await editFormItem();
      },
      async onUpdate(e) {
        const oldPosition = e.item.getAttribute('data-old-position');
        const dropIdx = e.newIndex;
        const position = formItems[dropIdx].position;
        await put(`/api/form/${selectedForm.id}/item/${oldPosition}`, {
          newPosition: position,
        });
        await selectForm(selectedForm);
      },
    });
  }

  async function selectForm(form) {
    selectedForm = form;
    editFormTitle = selectedForm.name;
    formItems = await get(`/api/form/${selectedForm.id}/item`);
    selectedFormItem = null;
  }

  async function loadForms() {
    const response = await get('/api/form');
    forms = response.items;
    if (forms.length > 0) {
      await selectForm(forms[0]);
    }
  }

  async function deleteForm() {
    const result = await jinyaConfirm($_('pages_and_forms.form.delete.title'), $_('pages_and_forms.form.delete.message', { values: selectedForm }), $_('pages_and_forms.form.delete.delete'), $_('pages_and_forms.form.delete.keep'));
    if (result) {
      await httpDelete(`/api/form/${selectedForm.id}`);
      await loadForms();
    }
  }

  async function createForm() {
    if (createFormTitle) {
      try {
        await post('/api/form', {
          title: createFormTitle,
          toAddress: createFormToAddress,
          description: formDescriptionTiny.getContent(),
        });
        createFormOpen = false;
        await loadForms();
        const form = forms.find(item => item.title === createFormTitle);
        await selectForm(form);
        createFormTitle = '';
      } catch (e) {
        if (e.status === 409) {
          await jinyaAlert($_('pages_and_forms.form.create.error.title'), $_('pages_and_forms.form.create.error.conflict'), $_('alert.dismiss'));
        } else {
          await jinyaAlert($_('pages_and_forms.form.create.error.title'), $_('pages_and_forms.form.create.error.generic'), $_('alert.dismiss'));
        }
      }
    }
  }

  async function openEdit() {
    editFormOpen = true;
    editFormTitle = selectedForm.title;
    editFormToAddress = selectedForm.toAddress;
    await tick();
    formDescriptionTiny = await createTiny(formDescriptionElement);
    formDescriptionTiny.setContent(selectedForm?.description);
  }

  async function openCreate() {
    createFormOpen = true;
    await tick();
    formDescriptionTiny = await createTiny(formDescriptionElement);
  }

  async function updateForm() {
    if (editFormTitle) {
      try {
        const title = editFormTitle;
        await put(`/api/form/${selectedForm.id}`, {
          title,
          toAddress: editFormToAddress,
          description: formDescriptionTiny.getContent(),
        });
        editFormOpen = false;
        await loadForms();
        const form = forms.find(item => item.title === title);
        await selectForm(form);
      } catch (e) {
        if (e.status === 409) {
          await jinyaAlert($_('pages_and_forms.form.edit.error.title'), $_('pages_and_forms.form.edit.error.conflict'), $_('alert.dismiss'));
        } else {
          await jinyaAlert($_('pages_and_forms.form.edit.error.title'), $_('pages_and_forms.form.edit.error.generic'), $_('alert.dismiss'));
        }
      }
    }
  }

  function onCreateCancel() {
    createFormTitle = '';
    createFormOpen = false;
  }

  function onEditCancel() {
    editFormOpen = false;
    editFormTitle = selectedForm.name;
  }

  async function editFormItem() {
    editFormItemOpen = true;
    if (!createNewFormItem) {
      editItemLabel = selectedFormItem.label;
      editItemPlaceholder = selectedFormItem.placeholder;
      editItemHelpText = selectedFormItem.helpText;
      editItemSpamFilter = selectedFormItem.spamFilter?.join('\n');
      editItemIsSubject = selectedFormItem.isSubject;
      editItemIsFromAddress = selectedFormItem.isFromAddress;
      editItemIsRequired = selectedFormItem.isRequired;
      editItemOptions = selectedFormItem.options?.join('\n');
    }
  }

  async function deleteFormItem() {
    const result = await jinyaConfirm($_('pages_and_forms.form.delete_item.title'), $_('pages_and_forms.form.delete_item.message', { values: selectedForm }), $_('pages_and_forms.form.delete_item.delete'), $_('pages_and_forms.form.delete_item.keep'));
    if (result) {
      await httpDelete(`/api/form/${selectedForm.id}/item/${selectedFormItem.position}`);
      await selectForm(selectedForm);
    }
  }

  function selectFormItem(item) {
    selectedFormItem = item;
  }

  function cancelEditFormItem() {
    editFormItemOpen = false;
    editItemLabel = null;
    editItemPlaceholder = null;
    editItemHelpText = null;
    editItemSpamFilter = null;
    editItemIsSubject = null;
    editItemIsFromAddress = null;
    editItemIsRequired = null;
    editItemOptions = null;
  }

  async function updateFormItem() {
    const data = {
      label: editItemLabel,
      isRequired: editItemIsRequired,
      isFromAddress: editItemIsFromAddress,
      isSubject: editItemIsSubject,
      placeholder: editItemPlaceholder,
      spamFilter: editItemSpamFilter?.split(/\n|\r|\r\n/gm),
      helpText: editItemHelpText,
      options: editItemOptions?.split(/\n|\r|\r\n/gm),
    };
    if (createNewFormItem) {
      createNewFormItem = false;
      data.position = createPosition;
      data.type = selectedFormItem.type;
      await post(`/api/form/${selectedForm.id}/item`, data);
    } else {
      await put(`/api/form/${selectedForm.id}/item/${selectedFormItem.position}`, data);
    }
    cancelEditFormItem();
    await selectForm(selectedForm);
  }

  onMount(async () => {
    await loadForms();
  });
</script>

<div class="cosmo-list">
    <nav class="cosmo-list__items">
        {#each forms as form (form.id)}
            <a class:cosmo-list__item--active={form.id === selectedForm.id} class="cosmo-list__item"
               on:click={() => selectForm(form)}>{form.title}</a>
        {/each}
        <button on:click={openCreate}
                class="cosmo-button cosmo-button--full-width">{$_('pages_and_forms.form.action.new')}</button>
    </nav>
    <div class="cosmo-list__content jinya-designer">
        {#if selectedForm}
            <div class="jinya-designer__title">
                <span class="cosmo-title">{selectedForm.title}</span>
            </div>
            <div class="cosmo-toolbar cosmo-toolbar--designer">
                <div class="cosmo-toolbar__group">
                    <button disabled={!selectedForm} on:click={openEdit}
                            class="cosmo-button">{$_('pages_and_forms.form.action.edit')}</button>
                    <button disabled={!selectedForm} on:click={deleteForm}
                            class="cosmo-button">{$_('pages_and_forms.form.action.delete')}</button>
                </div>
                <div class="cosmo-toolbar__group">
                    <button disabled={!selectedFormItem} on:click={editFormItem}
                            class="cosmo-button">{$_('pages_and_forms.form.action.edit_item')}</button>
                    <button disabled={!selectedFormItem} on:click={deleteFormItem}
                            class="cosmo-button">{$_('pages_and_forms.form.action.delete_item')}</button>
                </div>
            </div>
        {/if}
        <div class="jinya-designer__content">
            <div bind:this={formItemListElement} class="jinya-designer__result jinya-designer__result--horizontal">
                {#each formItems as item (item.id)}
                    <div data-old-position={item.position} class="jinya-designer-item"
                         class:jinya-designer-item--selected={selectedFormItem === item}
                         on:click={() => selectFormItem(item)}>
                        <span class="jinya-designer-item__title">{$_(`pages_and_forms.form.designer.type_${item.type}`)}</span>
                        <span class="jinya-form-item__label">{item.label}</span>
                    </div>
                {/each}
            </div>
            <div bind:this={formItemToolboxElement} class="jinya-designer__toolbox">
                <div data-type="text" class="jinya-designer-item__template">
                    <span class="jinya-designer__drag-handle"></span>
                    <span>{$_('pages_and_forms.form.designer.type_text')}</span>
                </div>
                <div data-type="email" class="jinya-designer-item__template">
                    <span class="jinya-designer__drag-handle"></span>
                    <span>{$_('pages_and_forms.form.designer.type_email')}</span>
                </div>
                <div data-type="textarea" class="jinya-designer-item__template">
                    <span class="jinya-designer__drag-handle"></span>
                    <span>{$_('pages_and_forms.form.designer.type_textarea')}</span>
                </div>
                <div data-type="select" class="jinya-designer-item__template">
                    <span class="jinya-designer__drag-handle"></span>
                    <span>{$_('pages_and_forms.form.designer.type_select')}</span>
                </div>
                <div data-type="checkbox" class="jinya-designer-item__template">
                    <span class="jinya-designer__drag-handle"></span>
                    <span>{$_('pages_and_forms.form.designer.type_checkbox')}</span>
                </div>
            </div>
        </div>
    </div>
</div>
{#if createFormOpen}
    <div class="cosmo-modal__backdrop"></div>
    <div class="cosmo-modal__container">
        <div class="cosmo-modal">
            <h1 class="cosmo-modal__title">{$_('pages_and_forms.form.create.title')}</h1>
            <div class="cosmo-modal__content">
                <div class="cosmo-input__group">
                    <label for="createFormTitle"
                           class="cosmo-label">{$_('pages_and_forms.form.create.form_title')}</label>
                    <input required bind:value={createFormTitle} type="text" id="createFormTitle" class="cosmo-input">
                    <label for="createFormToAddress"
                           class="cosmo-label">{$_('pages_and_forms.form.create.to_address')}</label>
                    <input required bind:value={createFormToAddress} type="email" id="createFormToAddress"
                           class="cosmo-input">
                    <label class="cosmo-label cosmo-label--textarea">{$_('pages_and_forms.form.create.description')}</label>
                    <textarea bind:this={formDescriptionElement}></textarea>
                </div>
            </div>
            <div class="cosmo-modal__button-bar">
                <button class="cosmo-button"
                        on:click={onCreateCancel}>{$_('pages_and_forms.form.create.cancel')}</button>
                <button class="cosmo-button"
                        on:click={createForm}>{$_('pages_and_forms.form.create.create')}</button>
            </div>
        </div>
    </div>
{/if}
{#if editFormOpen}
    <div class="cosmo-modal__backdrop"></div>
    <div class="cosmo-modal__container">
        <div class="cosmo-modal">
            <h1 class="cosmo-modal__title">{$_('pages_and_forms.form.edit.title')}</h1>
            <div class="cosmo-modal__content">
                <div class="cosmo-input__group">
                    <label for="editFormTitle" class="cosmo-label">{$_('pages_and_forms.form.edit.form_title')}</label>
                    <input required bind:value={editFormTitle} type="text" id="editFormTitle" class="cosmo-input">
                    <label for="editFormToAddress"
                           class="cosmo-label">{$_('pages_and_forms.form.edit.to_address')}</label>
                    <input required bind:value={editFormToAddress} type="email" id="editFormToAddress"
                           class="cosmo-input">
                    <label class="cosmo-label cosmo-label--textarea">{$_('pages_and_forms.form.create.description')}</label>
                    <textarea bind:this={formDescriptionElement}></textarea>
                </div>
            </div>
            <div class="cosmo-modal__button-bar">
                <button class="cosmo-button"
                        on:click={onEditCancel}>{$_('pages_and_forms.form.edit.cancel')}</button>
                <button class="cosmo-button"
                        on:click={updateForm}>{$_('pages_and_forms.form.edit.update')}</button>
            </div>
        </div>
    </div>
{/if}
{#if editFormItemOpen}
    <div class="cosmo-modal__backdrop"></div>
    <div class="cosmo-modal__container">
        <div class="cosmo-modal">
            <h1 class="cosmo-modal__title">{$_('pages_and_forms.form.designer.edit.title')}</h1>
            <div class="cosmo-modal__content">
                <div class="cosmo-input__group">
                    <label for="editItemLabel"
                           class="cosmo-label">{$_('pages_and_forms.form.designer.edit.label')}</label>
                    <input required bind:value={editItemLabel} type="text" id="editItemLabel" class="cosmo-input">
                    <label for="editItemPlaceholder"
                           class="cosmo-label">{$_('pages_and_forms.form.designer.edit.placeholder')}</label>
                    <input bind:value={editItemPlaceholder} type="text" id="editItemPlaceholder" class="cosmo-input">
                    <label for="editItemHelpText"
                           class="cosmo-label">{$_('pages_and_forms.form.designer.edit.help_text')}</label>
                    <input required bind:value={editItemHelpText} type="text" id="editItemHelpText" class="cosmo-input">
                    {#if ['text', 'textarea'].includes(selectedFormItem.type)}
                        <label for="editItemSpamFilter"
                               class="cosmo-label cosmo-label--textarea">{$_('pages_and_forms.form.designer.edit.spam_filter')}</label>
                        <textarea bind:value={editItemSpamFilter} id="editItemSpamFilter" rows="5"
                                  class="cosmo-textarea"></textarea>
                    {/if}
                    {#if selectedFormItem.type === 'select'}
                        <label for="editItemItems"
                               class="cosmo-label cosmo-label--textarea">{$_('pages_and_forms.form.designer.edit.items')}</label>
                        <textarea bind:value={editItemOptions} id="editItemItems" rows="5"
                                  class="cosmo-textarea"></textarea>
                    {/if}
                    {#if selectedFormItem.type === 'text'}
                        <div class="cosmo-checkbox__group">
                            <input bind:checked={editItemIsSubject} type="checkbox" id="editItemIsSubject"
                                   class="cosmo-checkbox">
                            <label for="editItemIsSubject">{$_('pages_and_forms.form.designer.edit.is_subject')}</label>
                        </div>
                    {/if}
                    {#if selectedFormItem.type === 'email'}
                        <div class="cosmo-checkbox__group">
                            <input bind:checked={editItemIsFromAddress} type="checkbox" id="editItemIsFromAddress"
                                   class="cosmo-checkbox">
                            <label for="editItemIsFromAddress">{$_('pages_and_forms.form.designer.edit.is_from_address')}</label>
                        </div>
                    {/if}
                    <div class="cosmo-checkbox__group">
                        <input bind:checked={editItemIsRequired} type="checkbox" id="editItemIsRequired"
                               class="cosmo-checkbox">
                        <label for="editItemIsRequired">{$_('pages_and_forms.form.designer.edit.is_required')}</label>
                    </div>
                </div>
            </div>
            <div class="cosmo-modal__button-bar">
                <button class="cosmo-button"
                        on:click={cancelEditFormItem}>{$_('pages_and_forms.form.designer.edit.cancel')}</button>
                <button class="cosmo-button"
                        on:click={updateFormItem}>{$_('pages_and_forms.form.designer.edit.update')}</button>
            </div>
        </div>
    </div>
{/if}
