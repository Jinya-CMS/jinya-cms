<script>
  import { get, httpDelete, post, put } from '../../http/request';
  import { onMount, tick } from 'svelte';
  import { _ } from 'svelte-i18n';
  import { jinyaConfirm } from '../../ui/confirm';
  import { jinyaAlert } from '../../ui/alert';
  import Sortable from 'sortablejs';

  let createMenuName = '';
  let createMenuLogo = null;
  let createMenuOpen = false;

  let editMenuName = '';
  let editMenuLogo = null;
  let editMenuOpen = false;

  let menus = [];
  let menuItems = [];
  let segmentPages = [];
  let galleries = [];
  let pages = [];
  let forms = [];
  let files = [];
  let artists = [];

  let allowDecreaseNesting = false;
  let allowIncreaseNesting = false;
  let selectedMenu = null;
  let selectedMenuItem = null;
  let menuItemToolboxElement;
  let menuItemListElement;

  let editMenuItemOpen;
  let editMenuItemRoute;
  let editMenuItemIsHighlighted;
  let editMenuItemTitle;
  let editMenuItemElement;
  let itemsToChooseFrom = [];

  let createNewMenuItem = false;
  let createPosition;
  let createMenuItemParent = null;

  $: if (menuItemToolboxElement instanceof HTMLElement) {
    new Sortable(menuItemToolboxElement, {
      group: { name: 'menu_items', put: false, pull: 'clone' },
      sort: false,
      handle: '.jinya-designer__drag-handle',
      onEnd(e) {
        if (!e.to.classList.contains('jinya-designer__toolbox')) {
          e.item.remove();
        }
      }
    });
  }

  $: if (menuItemListElement instanceof HTMLElement) {
    new Sortable(menuItemListElement, {
      group: { name: 'menu_items', put: true, pull: false },
      sort: true,
      async onAdd(e) {
        createNewMenuItem = true;
        const dropIdx = e.newIndex;
        if (menuItems.length === 0) {
          createPosition = 0;
          createMenuItemParent = null;
        } else if (menuItems.length === dropIdx) {
          createPosition = menuItems[menuItems.length - 1].position + 2;
          if (menuItems[menuItems.length - 1].parent) {
            createMenuItemParent = menuItems[menuItems.length - 1].parent.id;
          } else {
            createMenuItemParent = null;
          }
        } else {
          createPosition = menuItems[dropIdx].position;
          if (menuItems[dropIdx].parent) {
            createMenuItemParent = menuItems[dropIdx].parent.id;
          } else {
            createMenuItemParent = null;
          }
        }

        selectedMenuItem = { type: e.item.getAttribute('data-type') };
        switch (e.item.getAttribute('data-type')) {
          case 'gallery':
            selectedMenuItem.gallery = galleries[0];
            break;
          case 'page':
            selectedMenuItem.page = pages[0];
            break;
          case 'segment_page':
            selectedMenuItem.segmentPage = segmentPages[0];
            break;
          case 'form':
            selectedMenuItem.form = forms[0];
            break;
        }
        await editMenuItem();
      },
      async onUpdate(e) {
        const dropIdx = e.newIndex;
        const menuItemId = parseInt(e.item.getAttribute('data-id'));
        const position = menuItems[dropIdx].position;
        const newParent = menuItems[dropIdx].parent.id;
        const newParentItem = menuItems[dropIdx].parent;
        const dataParentId = parseInt(e.item.previousSibling.getAttribute('data-parent-id'));
        let currentParent = menuItems.find(item => item.id === dataParentId);
        let allowMove = true;
        while (currentParent) {
          if (currentParent?.id === menuItemId) {
            allowMove = false;
            break;
          }

          currentParent = currentParent.parent;
        }

        if (allowMove) {
          await put(`/api/menu-item/${menuItemId}/move/parent/to/item/${newParent}`);
          await put(`/api/menu-item/${menuItemId}`, {
            position,
          });
        }
        menuItems = [];
        await tick();
        await selectMenu(selectedMenu);
      },
    });
  }

  async function selectMenu(menu) {
    selectedMenu = menu;
    editMenuName = selectedMenu.name;
    menuItems = await get(`/api/menu/${selectedMenu.id}/item`);
    menuItems = flattenMenuItems(null, 0, menuItems);
    selectMenuItem(null);
  }

  function flattenMenuItems(parent, nestingIndex, items) {
    let result = [];
    for (const item of items) {
      item.parent = parent;
      item.nestingIndex = nestingIndex;
      item.type = getType(item);
      result.push(item);
      if (items.length > 0) {
        result.push(...flattenMenuItems(item, nestingIndex + 1, item.items));
      }
    }

    return result;
  }

  async function loadMenus() {
    const response = await get('/api/menu');
    menus = response.items;
    if (menus.length > 0) {
      await selectMenu(menus[0]);
    }
  }

  async function deleteMenu() {
    const result = await jinyaConfirm($_('design.menus.delete.title'), $_('design.menus.delete.message', { values: selectedMenu }), $_('design.menus.delete.delete'), $_('design.menus.delete.keep'));
    if (result) {
      await httpDelete(`/api/menu/${selectedMenu.id}`);
      await loadMenus();
    }
  }

  async function createMenu() {
    if (createMenuName) {
      try {
        await post('/api/menu', {
          name: createMenuName,
          logo: createMenuLogo,
        });
        createMenuOpen = false;
        await loadMenus();
        const menu = menus.find(item => item.name === createMenuName);
        await selectMenu(menu);
        createMenuName = '';
        createMenuLogo = null;
      } catch (e) {
        if (e.status === 409) {
          await jinyaAlert($_('design.menus.create.error.title'), $_('design.menus.create.error.conflict'), $_('alert.dismiss'));
        } else {
          await jinyaAlert($_('design.menus.create.error.title'), $_('design.menus.create.error.generic'), $_('alert.dismiss'));
        }
      }
    }
  }

  async function openEdit() {
    editMenuOpen = true;
    editMenuName = selectedMenu.name;
    editMenuLogo = selectedMenu.logo.id;
  }

  async function openCreate() {
    createMenuOpen = true;
    await tick();
  }

  async function updateMenu() {
    if (editMenuName) {
      try {
        const name = editMenuName;
        await put(`/api/menu/${selectedMenu.id}`, {
          name,
          logo: editMenuLogo,
        });
        editMenuOpen = false;
        await loadMenus();
        const menu = menus.find(item => item.name === name);
        await selectMenu(menu);
      } catch (e) {
        if (e.status === 409) {
          await jinyaAlert($_('design.menus.edit.error.title'), $_('design.menus.edit.error.conflict'), $_('alert.dismiss'));
        } else {
          await jinyaAlert($_('design.menus.edit.error.title'), $_('design.menus.edit.error.generic'), $_('alert.dismiss'));
        }
      }
    }
  }

  function onCreateCancel() {
    createMenuName = '';
    createMenuOpen = false;
  }

  function onEditCancel() {
    editMenuOpen = false;
    editMenuName = selectedMenu.name;
    editMenuLogo = selectedMenu.logo.id;
  }

  async function editMenuItem() {
    editMenuItemOpen = true;
    editMenuItemRoute = selectedMenuItem.route;
    editMenuItemIsHighlighted = selectedMenuItem.highlighted;
    editMenuItemTitle = selectedMenuItem.title;
    switch (selectedMenuItem.type) {
      case 'gallery':
        editMenuItemElement = selectedMenuItem.gallery.id;
        itemsToChooseFrom = galleries;
        break;
      case 'group':
        editMenuItemElement = null;
        break;
      case 'page':
        editMenuItemElement = selectedMenuItem.page.id;
        itemsToChooseFrom = pages;
        break;
      case 'segment_page':
        editMenuItemElement = selectedMenuItem.segmentPage.id;
        itemsToChooseFrom = segmentPages;
        break;
      case 'form':
        editMenuItemElement = selectedMenuItem.form.id;
        itemsToChooseFrom = forms;
        break;
      case 'artist':
        editMenuItemElement = selectedMenuItem.artist.id;
        itemsToChooseFrom = artists;
        break;
      case 'external_link':
        editMenuItemElement = null;
        break;
    }
  }

  async function deleteMenuItem() {
    const result = await jinyaConfirm($_('design.menus.delete_item.title'), $_('design.menus.delete_item.message', { values: selectedMenu }), $_('design.menus.delete_item.delete'), $_('design.menus.delete_item.keep'));
    if (result) {
      await httpDelete(`/api/menu/${selectedMenu.id}/item/${selectedMenuItem.position}`);
      await selectMenu(selectedMenu);
    }
  }

  function getType(item) {
    let type;
    if (item.segmentPage) {
      type = 'segment_page';
    } else if (item.page) {
      type = 'page';
    } else if (item.form) {
      type = 'form';
    } else if (item.route === null) {
      type = 'group';
    } else if (item.gallery) {
      type = 'gallery';
    } else if (item.artist) {
      type = 'artist';
    } else {
      type = 'external_link';
    }

    return type;
  }

  function selectMenuItem(item) {
    selectedMenuItem = item;
    allowDecreaseNesting = checkIfNestingDecreaseIsAllowed();
    allowIncreaseNesting = checkIfNestingIncreaseIsAllowed();
  }

  function cancelEditMenuItem() {
    editMenuItemOpen = false;
  }

  async function updateMenuItem() {
    const data = {
      title: editMenuItemTitle,
      route: editMenuItemRoute,
      highlighted: editMenuItemIsHighlighted,
    };

    switch (selectedMenuItem.type) {
      case 'gallery':
        data.gallery = editMenuItemElement;
        break;
      case 'page':
        data.page = editMenuItemElement;
        break;
      case 'segment_page':
        data.segmentPage = editMenuItemElement;
        break;
      case 'form':
        data.form = editMenuItemElement;
        break;
      case 'artist':
        data.artist = editMenuItemElement;
        break;
    }

    if (createNewMenuItem) {
      createNewMenuItem = false;
      data.position = createPosition;
      if (createMenuItemParent !== null) {
        await post(`/api/menu-item/${createMenuItemParent}/item`, data);
      } else {
        await post(`/api/menu/${selectedMenu.id}/item`, data);
      }
    } else {
      await put(`/api/menu-item/${selectedMenuItem.id}`, data);
    }

    cancelEditMenuItem();
    await selectMenu(selectedMenu);
  }

  async function decreaseNesting() {
    await put(`/api/menu/${selectedMenu.id}/item/${selectedMenuItem.id}/move/parent/one/level/up`);
    await selectMenu(selectedMenu);
  }

  async function increaseNesting() {
    const previous = menuItems[menuItems.indexOf(selectedMenuItem) - 1];
    await put(`/api/menu-item/${selectedMenuItem.id}/move/parent/to/item/${previous.id}`);
    await selectMenu(selectedMenu);
  }

  function checkIfNestingDecreaseIsAllowed() {
    if (!selectedMenuItem || selectedMenuItem.nestingIndex === 0) {
      return false;
    }

    return true;
  }

  function checkIfNestingIncreaseIsAllowed() {
    if (!selectedMenuItem) {
      return false;
    }

    const current = menuItems.indexOf(selectedMenuItem);
    if (current === 0) {
      return false;
    }

    const previous = menuItems[current - 1];
    if (previous.nestingIndex > selectedMenuItem?.nestingIndex) {
      return false;
    }

    if (selectedMenuItem?.items[0]?.position === selectedMenuItem?.position) {
      return false;
    }

    if (selectedMenuItem?.parent && selectedMenuItem?.parent === previous) {
      return false;
    }

    return true;
  }

  onMount(async () => {
    await loadMenus();
    get('/api/segment-page').then(result => segmentPages = result.items ?? []);
    get('/api/page').then(result => pages = result.items ?? []);
    get('/api/form').then(result => forms = result.items ?? []);
    get('/api/media/gallery').then(result => galleries = result.items ?? []);
    get('/api/media/file').then(result => files = result.items ?? []);
    get('/api/user').then(result => artists = result.items ?? []);
  });
</script>

<div class="cosmo-list">
    <nav class="cosmo-list__items">
        {#each menus as menu (menu.id)}
            <a class:cosmo-list__item--active={menu.id === selectedMenu.id} class="cosmo-list__item"
               on:click={() => selectMenu(menu)}>{menu.name}</a>
        {/each}
        <button on:click={openCreate}
                class="cosmo-button cosmo-button--full-width">{$_('design.menus.action.new')}</button>
    </nav>
    <div class="cosmo-list__content jinya-designer">
        {#if selectedMenu}
            <div class="jinya-designer__title">
                <span class="cosmo-title">{selectedMenu.name}</span>
            </div>
            <div class="cosmo-toolbar cosmo-toolbar--designer">
                <div class="cosmo-toolbar__group">
                    <button disabled={!selectedMenu} on:click={openEdit}
                            class="cosmo-button">{$_('design.menus.action.edit')}</button>
                    <button disabled={!selectedMenu} on:click={deleteMenu}
                            class="cosmo-button">{$_('design.menus.action.delete')}</button>
                </div>
                <div class="cosmo-toolbar__group">
                    <button disabled={!allowDecreaseNesting} on:click={decreaseNesting}
                            class="cosmo-button">{$_('design.menus.action.decrease_nesting')}</button>
                    <button disabled={!allowIncreaseNesting} on:click={increaseNesting}
                            class="cosmo-button">{$_('design.menus.action.increase_nesting')}</button>
                    <button disabled={!selectedMenuItem} on:click={editMenuItem}
                            class="cosmo-button">{$_('design.menus.action.edit_item')}</button>
                    <button disabled={!selectedMenuItem} on:click={deleteMenuItem}
                            class="cosmo-button">{$_('design.menus.action.delete_item')}</button>
                </div>
            </div>
        {/if}
        <div class="jinya-designer__content">
            <div bind:this={menuItemListElement} class="jinya-designer__result jinya-designer__result--horizontal">
                {#each menuItems as item (item.id)}
                    <div data-id={item.id} class="jinya-designer-item jinya-designer-item--menu"
                         on:click={() => selectMenuItem(item)} data-parent-id={item.parent?.id}
                         class:jinya-designer-item--selected={selectedMenuItem === item}
                         style="margin-left: {item.nestingIndex * 16}px; width: calc(100% - {item.nestingIndex * 16}px);">
                        <span class="jinya-designer-item__title">{$_(`design.menus.designer.type_${item.type}`)}</span>
                        <span>
                            <span>{item.title}</span>
                            {#if item.route}
                                <span class="jinya-menu-item__route">{item.route}</span>
                            {/if}
                        </span>
                    </div>
                {/each}
            </div>
            <div bind:this={menuItemToolboxElement} class="jinya-designer__toolbox">
                <div data-type="gallery" class="jinya-designer-item__template">
                    <span class="jinya-designer__drag-handle"></span>
                    <span>{$_('design.menus.designer.type_gallery')}</span>
                </div>
                <div data-type="page" class="jinya-designer-item__template">
                    <span class="jinya-designer__drag-handle"></span>
                    <span>{$_('design.menus.designer.type_page')}</span>
                </div>
                <div data-type="segment_page" class="jinya-designer-item__template">
                    <span class="jinya-designer__drag-handle"></span>
                    <span>{$_('design.menus.designer.type_segment_page')}</span>
                </div>
                <div data-type="form" class="jinya-designer-item__template">
                    <span class="jinya-designer__drag-handle"></span>
                    <span>{$_('design.menus.designer.type_form')}</span>
                </div>
                <div data-type="group" class="jinya-designer-item__template">
                    <span class="jinya-designer__drag-handle"></span>
                    <span>{$_('design.menus.designer.type_group')}</span>
                </div>
                <div data-type="external_link" class="jinya-designer-item__template">
                    <span class="jinya-designer__drag-handle"></span>
                    <span>{$_('design.menus.designer.type_external_link')}</span>
                </div>
            </div>
        </div>
    </div>
</div>
{#if createMenuOpen}
    <div class="cosmo-modal__backdrop"></div>
    <div class="cosmo-modal__container">
        <div class="cosmo-modal">
            <h1 class="cosmo-modal__title">{$_('design.menus.create.title')}</h1>
            <div class="cosmo-modal__content">
                <div class="cosmo-input__group">
                    <label for="createMenuName" class="cosmo-label">{$_('design.menus.create.name')}</label>
                    <input required bind:value={createMenuName} type="text" id="createMenuName" class="cosmo-input">
                    <label for="createMenuLogo" class="cosmo-label">{$_('design.menus.create.logo')}</label>
                    <select required bind:value={createMenuLogo} id="createMenuLogo" class="cosmo-select">
                        <option value={null}>{$_('design.menus.create.logo_none')}</option>
                        {#each files as file}
                            <option value={file.id}>{file.name}</option>
                        {/each}
                    </select>
                </div>
            </div>
            <div class="cosmo-modal__button-bar">
                <button class="cosmo-button" on:click={onCreateCancel}>{$_('design.menus.create.cancel')}</button>
                <button class="cosmo-button" on:click={createMenu}>{$_('design.menus.create.create')}</button>
            </div>
        </div>
    </div>
{/if}
{#if editMenuOpen}
    <div class="cosmo-modal__backdrop"></div>
    <div class="cosmo-modal__container">
        <div class="cosmo-modal">
            <h1 class="cosmo-modal__title">{$_('design.menus.edit.title')}</h1>
            <div class="cosmo-modal__content">
                <div class="cosmo-input__group">
                    <label for="editMenuName" class="cosmo-label">{$_('design.menus.edit.name')}</label>
                    <input required bind:value={editMenuName} type="text" id="editMenuName" class="cosmo-input">
                    <label for="editMenuLogo" class="cosmo-label">{$_('design.menus.edit.logo')}</label>
                    <select required bind:value={editMenuLogo} id="editMenuLogo" class="cosmo-select">
                        {#each files as file}
                            <option value={file.id}>{file.name}</option>
                        {/each}
                    </select>
                </div>
            </div>
            <div class="cosmo-modal__button-bar">
                <button class="cosmo-button" on:click={onEditCancel}>{$_('design.menus.edit.cancel')}</button>
                <button class="cosmo-button" on:click={updateMenu}>{$_('design.menus.edit.update')}</button>
            </div>
        </div>
    </div>
{/if}
{#if editMenuItemOpen}
    <div class="cosmo-modal__backdrop"></div>
    <div class="cosmo-modal__container">
        <div class="cosmo-modal">
            <h1 class="cosmo-modal__title">{$_('design.menus.designer.edit.title')}</h1>
            <div class="cosmo-modal__content">
                <div class="cosmo-input__group">
                    <label for="editMenuItemTitle"
                           class="cosmo-label">{$_('design.menus.designer.edit.item_title')}</label>
                    <input required bind:value={editMenuItemTitle} type="text" id="editMenuItemTitle"
                           class="cosmo-input">
                    {#if selectedMenuItem.type !== 'group'}
                        <label for="editMenuItemRoute"
                               class="cosmo-label">{$_('design.menus.designer.edit.route')}</label>
                        <input bind:value={editMenuItemRoute} type="text" id="editMenuItemRoute" class="cosmo-input">
                    {/if}
                    {#if editMenuItemElement !== null}
                        <label for="editMenuElement"
                               class="cosmo-label">{$_(`design.menus.designer.type_${selectedMenuItem.type}`)}</label>
                        <select required bind:value={editMenuItemElement} id="editMenuElement" class="cosmo-select">
                            {#each itemsToChooseFrom as item}
                                <option value={item.id}>{item.name ?? item.title ?? item.artistName}</option>
                            {/each}
                        </select>
                    {/if}
                    <div class="cosmo-checkbox__group">
                        <input bind:checked={editMenuItemIsHighlighted} type="checkbox" id="editMenuItemIsHighlighted"
                               class="cosmo-checkbox">
                        <label for="editMenuItemIsHighlighted">{$_('design.menus.designer.edit.is_highlighted')}</label>
                    </div>
                </div>
            </div>
            <div class="cosmo-modal__button-bar">
                <button class="cosmo-button"
                        on:click={cancelEditMenuItem}>{$_('design.menus.designer.edit.cancel')}</button>
                <button class="cosmo-button"
                        on:click={updateMenuItem}>{$_('design.menus.designer.edit.update')}</button>
            </div>
        </div>
    </div>
{/if}
