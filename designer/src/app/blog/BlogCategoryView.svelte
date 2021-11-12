<script>
  import { onMount } from "svelte";
  import { _ } from "svelte-i18n";
  import { get, httpDelete, post, put } from '../../http/request';
  import { jinyaAlert } from "../../ui/alert";
  import { jinyaConfirm } from "../../ui/confirm";

  let categories = [];
  let selectedCategory;

  let createCategoryName = '';
  let createCategoryDescription = '';
  let createCategoryParent = null;
  let createCategoryOpen = false;
  let createCategoryWebhookEnabled = true;
  let createCategoryWebhookUrl = '';

  let editCategoryName = '';
  let editCategoryDescription = '';
  let editCategoryParent = null;
  let editCategoryOpen = false;
  let editCategoryWebhookEnabled = true;
  let editCategoryWebhookUrl = '';

  function openEdit() {
    editCategoryName = selectedCategory.name;
    editCategoryDescription = selectedCategory.description;
    editCategoryParent = selectedCategory.parent?.id;
    editCategoryWebhookUrl = selectedCategory.webhookUrl;
    editCategoryWebhookEnabled = selectedCategory.webhookEnabled;
    editCategoryOpen = true;
  }

  async function selectCategory(category) {
    selectedCategory = category;
  }

  async function loadCategories() {
    const response = await get('/api/blog/category');
    categories = response.items;
    if (categories.length > 0) {
      await selectCategory(categories[0]);
    }
  }

  async function deleteCategory() {
    const result = await jinyaConfirm($_('blog.categories.delete.title'), $_('blog.categories.delete.message', {values: selectedCategory}), $_('blog.categories.delete.delete'), $_('blog.categories.delete.keep'));
    if (result) {
      await httpDelete(`/api/blog/category/${selectedCategory.id}`);
      await loadCategories();
    }
  }

  function onCreateCancel() {
    createCategoryOpen = false;
    createCategoryName = '';
    createCategoryDescription = '';
    createCategoryParent = null;
    createCategoryWebhookEnabled = true;
    createCategoryWebhookUrl = '';
  }

  async function createCategory() {
    if (createCategoryName) {
      try {
        const data = {
          name: createCategoryName,
          description: createCategoryDescription,
          webhookEnabled: createCategoryWebhookEnabled,
          webhookUrl: createCategoryWebhookUrl,
        };
        if (createCategoryParent) {
          data.parentId = createCategoryParent;
        }
        await post('/api/blog/category', data);
        createCategoryOpen = false;
        await loadCategories();
        const category = categories.find(item => item.name === createCategoryName);
        await selectCategory(category);
        createCategoryName = '';
        createCategoryDescription = '';
        createCategoryParent = null;
        createCategoryWebhookEnabled = true;
        createCategoryWebhookUrl = '';
      } catch (e) {
        if (e.status === 409) {
          await jinyaAlert($_('blog.categories.create.error.title'), $_('blog.categories.create.error.conflict'), $_('alert.dismiss'));
        } else {
          await jinyaAlert($_('blog.categories.create.error.title'), $_('blog.categories.create.error.generic'), $_('alert.dismiss'));
        }
      }
    }
  }

  function onEditCancel() {
    editCategoryOpen = false;
    editCategoryName = '';
    editCategoryDescription = '';
    editCategoryParent = null;
  }

  async function updateCategory() {
    if (editCategoryName) {
      try {
        const data = {
          name: editCategoryName,
          description: editCategoryDescription,
          webhookEnabled: editCategoryWebhookEnabled,
          webhookUrl: editCategoryWebhookUrl,
        };
        if (editCategoryParent) {
          data.parentId = editCategoryParent;
        }
        await put(`/api/blog/category/${selectedCategory.id}`, data);
        editCategoryOpen = false;
        await loadCategories();
        const category = categories.find(item => item.name === editCategoryName);
        await selectCategory(category);
        editCategoryName = '';
        editCategoryDescription = '';
        editCategoryParent = null;
      } catch (e) {
        if (e.status === 409) {
          await jinyaAlert($_('blog.categories.edit.error.title'), $_('blog.categories.edit.error.conflict'), $_('alert.dismiss'));
        } else {
          await jinyaAlert($_('blog.categories.edit.error.title'), $_('blog.categories.edit.error.generic'), $_('alert.dismiss'));
        }
      }
    }
  }

  onMount(async () => {
    await loadCategories();
  });
</script>

<div class="cosmo-list">
    <nav class="cosmo-list__items">
        {#each categories as category (category.id)}
            <a class:cosmo-list__item--active={category.id === selectedCategory.id} class="cosmo-list__item"
               on:click={() => selectCategory(category)}>#{category.id} {category.name}</a>
        {/each}
        <button class="cosmo-button cosmo-button--full-width"
                on:click={() => createCategoryOpen = true}>{$_('blog.categories.action.new')}</button>
    </nav>
    <div class="cosmo-list__content jinya-designer">
        {#if selectedCategory}
            <div class="jinya-designer__title">
                <span class="cosmo-title">#{selectedCategory.id} {selectedCategory.name}</span>
            </div>
            <div class="cosmo-toolbar cosmo-toolbar--designer">
                <div class="cosmo-toolbar__group">
                    <button disabled={!selectedCategory} on:click={openEdit}
                            class="cosmo-button">{$_('blog.categories.action.edit')}</button>
                    <button disabled={!selectedCategory} on:click={deleteCategory}
                            class="cosmo-button">{$_('blog.categories.action.delete')}</button>
                </div>
            </div>
            <div class="jinya-designer__content jinya-designer__content--key-value-list">
                <dl class="cosmo-key-value-list">
                    <dt class="cosmo-key-value-list__key">{$_('blog.categories.details.name')}</dt>
                    <dd class="cosmo-key-value-list__value">{selectedCategory.name}</dd>
                    <dt class="cosmo-key-value-list__key">{$_('blog.categories.details.description')}</dt>
                    <dd class="cosmo-key-value-list__value">{selectedCategory.description ?? $_('blog.categories.details.description_none')}</dd>
                    <dt class="cosmo-key-value-list__key">{$_('blog.categories.details.parent')}</dt>
                    <dd class="cosmo-key-value-list__value">{selectedCategory.parent?.name ?? $_('blog.categories.details.parent_none')}</dd>
                    {#if selectedCategory.webhookEnabled}
                        <dt class="cosmo-key-value-list__key">{$_('blog.categories.details.webhook')}</dt>
                        <dd class="cosmo-key-value-list__value">{selectedCategory.webhookUrl}</dd>
                    {/if}
                </dl>
            </div>
        {/if}
    </div>
</div>
{#if createCategoryOpen}
    <div class="cosmo-modal__backdrop"></div>
    <div class="cosmo-modal__container">
        <div class="cosmo-modal">
            <h1 class="cosmo-modal__title">{$_('blog.categories.create.title')}</h1>
            <div class="cosmo-modal__content">
                <div class="cosmo-input__group">
                    <label for="createCategoryName" class="cosmo-label">{$_('blog.categories.create.name')}</label>
                    <input required bind:value={createCategoryName} type="text" id="createCategoryName"
                           class="cosmo-input">
                    <label for="createCategoryParent" class="cosmo-label">{$_('blog.categories.create.parent')}</label>
                    <select required bind:value={createCategoryParent} id="createCategoryParent" class="cosmo-select">
                        <option value={null}>{$_('blog.categories.create.parent_none')}</option>
                        {#each categories as category}
                            <option value={category.id}>#{category.id} {category.name}</option>
                        {/each}
                    </select>
                    <label for="createCategoryDescription"
                           class="cosmo-label cosmo-label--textarea">{$_('blog.categories.create.description')}</label>
                    <textarea bind:value={createCategoryDescription} rows="5" id="createCategoryDescription"
                              class="cosmo-textarea"></textarea>
                    <label for="createCategoryWebhookUrl"
                           class="cosmo-label">{$_('blog.categories.create.webhook_url')}</label>
                    <input bind:value={createCategoryWebhookUrl} type="text" id="createCategoryWebhookUrl"
                           class="cosmo-input">
                    <div class="cosmo-checkbox__group">
                        <input class="cosmo-checkbox" type="checkbox" id="createCategoryWebhookEnabled"
                               bind:checked={createCategoryWebhookEnabled}>
                        <label for="createCategoryWebhookEnabled">{$_('blog.categories.create.webhook_enabled')}</label>
                    </div>
                </div>
            </div>
            <div class="cosmo-modal__button-bar">
                <button class="cosmo-button"
                        on:click={onCreateCancel}>{$_('blog.categories.create.cancel')}</button>
                <button class="cosmo-button"
                        on:click={createCategory}>{$_('blog.categories.create.create')}</button>
            </div>
        </div>
    </div>
{/if}
{#if editCategoryOpen}
    <div class="cosmo-modal__backdrop"></div>
    <div class="cosmo-modal__container">
        <div class="cosmo-modal">
            <h1 class="cosmo-modal__title">{$_('blog.categories.edit.title')}</h1>
            <div class="cosmo-modal__content">
                <div class="cosmo-input__group">
                    <label for="editCategoryName"
                           class="cosmo-label">{$_('blog.categories.edit.name')}</label>
                    <input required bind:value={editCategoryName} type="text" id="editCategoryName"
                           class="cosmo-input">
                    <label for="editCategoryParent" class="cosmo-label">{$_('blog.categories.edit.parent')}</label>
                    <select required bind:value={editCategoryParent} id="editCategoryParent" class="cosmo-select">
                        <option value={null}>{$_('blog.categories.edit.parent_none')}</option>
                        {#each categories as category}
                            <option value={category.id}>#{category.id} {category.name}</option>
                        {/each}
                    </select>
                    <label for="editCategoryDescription"
                           class="cosmo-label cosmo-label--textarea">{$_('blog.categories.edit.description')}</label>
                    <textarea bind:value={editCategoryDescription} rows="5" id="editCategoryDescription"
                              class="cosmo-textarea"></textarea>
                    <label for="editCategoryWebhookUrl"
                           class="cosmo-label">{$_('blog.categories.edit.webhook_url')}</label>
                    <input bind:value={editCategoryWebhookUrl} type="text" id="editCategoryWebhookUrl"
                           class="cosmo-input">
                    <div class="cosmo-checkbox__group">
                        <input class="cosmo-checkbox" type="checkbox" id="editCategoryWebhookEnabled"
                               bind:checked={editCategoryWebhookEnabled}>
                        <label for="editCategoryWebhookEnabled">{$_('blog.categories.edit.webhook_enabled')}</label>
                    </div>
                </div>
            </div>
            <div class="cosmo-modal__button-bar">
                <button class="cosmo-button"
                        on:click={onEditCancel}>{$_('blog.categories.edit.cancel')}</button>
                <button class="cosmo-button"
                        on:click={updateCategory}>{$_('blog.categories.edit.edit')}</button>
            </div>
        </div>
    </div>
{/if}
