<script>
  import { onMount } from 'svelte';
  import { get, getHost, put } from '../../http/request';
  import { _ } from 'svelte-i18n';
  import { jinyaConfirm } from '../../ui/confirm';
  import { jinyaAlert } from '../../ui/alert';

  let themes = [];
  let selectedTheme;
  let selectedTab = 'details';
  let configurationStructure = {};
  let defaultConfiguration = {};
  let variables = {};
  let defaultVariables = {};
  let configuration = {};
  let previewImage = '';

  let files = [];
  let galleries = [];
  let segmentPages = [];
  let pages = [];
  let menus = [];
  let forms = [];

  let themeFiles = {};
  let themeGalleries = {};
  let themeSegmentPages = {};
  let themePages = {};
  let themeMenus = {};
  let themeForms = {};

  async function activateTheme() {
    if (await jinyaConfirm($_('design.themes.activate.title'), $_('design.themes.activate.message', { values: selectedTheme }), $_('design.themes.activate.approve'), $_('design.themes.activate.decline'))) {
      try {
        await put(`/api/theme/${selectedTheme.id}/active`);
        await jinyaAlert($_('design.themes.activate.success.title'), $_('design.themes.activate.success.message', { values: selectedTheme }), $_('alert.dismiss'));
      } catch (e) {
        await jinyaAlert($_('design.themes.activate.error.title'), $_('design.themes.activate.error.message', { values: selectedTheme }), $_('alert.dismiss'));
      }
    }
  }

  async function compileAssets() {
    if (await jinyaConfirm($_('design.themes.assets.title'), $_('design.themes.assets.message', { values: selectedTheme }), $_('design.themes.assets.approve'), $_('design.themes.assets.decline'))) {
      try {
        await put(`/api/theme/${selectedTheme.id}/assets`);
        await jinyaAlert($_('design.themes.assets.success.title'), $_('design.themes.assets.success.message', { values: selectedTheme }), $_('alert.dismiss'));
      } catch (e) {
        await jinyaAlert($_('design.themes.assets.error.title'), $_('design.themes.assets.error.message', { values: selectedTheme }), $_('alert.dismiss'));
      }
    }
  }

  async function selectTheme(theme) {
    selectedTheme = theme;
    previewImage = `${getHost()}/api/theme/${selectedTheme.id}/preview`;
    configurationStructure = {};

    get(`/api/theme/${selectedTheme.id}/segment-page`).then(result => {
      const pages = {};
      for (const key of Object.keys(result)) {
        pages[key] = result[key].id;
      }

      themeSegmentPages = pages;
    });
    get(`/api/theme/${selectedTheme.id}/page`).then(result => {
      const pages = {};
      for (const key of Object.keys(result)) {
        pages[key] = result[key].id;
      }

      themePages = pages;
    });
    get(`/api/theme/${selectedTheme.id}/form`).then(result => {
      const forms = {};
      for (const key of Object.keys(result)) {
        forms[key] = result[key].id;
      }

      themeForms = forms;
    });
    get(`/api/theme/${selectedTheme.id}/menu`).then(result => {
      const menus = {};
      for (const key of Object.keys(result)) {
        menus[key] = result[key].id;
      }

      themeMenus = menus;
    });
    get(`/api/theme/${selectedTheme.id}/gallery`).then(result => {
      const galleries = {};
      for (const key of Object.keys(result)) {
        galleries[key] = result[key].id;
      }

      themeGalleries = galleries;
    });
    get(`/api/theme/${selectedTheme.id}/file`).then(result => {
      const files = {};
      for (const key of Object.keys(result)) {
        files[key] = result[key].id;
      }

      themeFiles = files;
    });

    defaultVariables = await get(`/api/theme/${selectedTheme.id}/styling`);
    variables = selectedTheme.scssVariables;
    defaultConfiguration = await get(`/api/theme/${selectedTheme.id}/configuration/default`);
    configuration = selectedTheme.configuration;
    Object.keys(defaultConfiguration).forEach((item) => {
      configuration[item] = configuration[item] ?? {};
    });
    configurationStructure = await get(`/api/theme/${selectedTheme.id}/configuration/structure`);
  }

  async function discardVariables() {
    variables = selectedTheme.scssVariables;
    defaultVariables = await get(`/api/theme/${selectedTheme.id}/styling`);
  }

  async function saveVariables() {
    try {
      await put(`/api/theme/${selectedTheme.id}/styling`, { variables });
      selectedTheme.scssVariables = variables;
      defaultVariables = await get(`/api/theme/${selectedTheme.id}/styling`);
      await jinyaAlert($_('design.themes.variables.success.title'), $_('design.themes.variables.success.message'), $_('alert.dismiss'));
    } catch (e) {
      await jinyaAlert($_('design.themes.variables.error.title'), $_('design.themes.variables.error.message'), $_('alert.dismiss'));
    }
  }

  async function discardConfiguration() {
    await selectTheme(selectedTheme);
  }

  async function saveConfiguration() {
    try {
      await put(`/api/theme/${selectedTheme.id}/configuration`, { configuration });
      selectTheme(selectedTheme);
      await jinyaAlert($_('design.themes.configuration.success.title'), $_('design.themes.configuration.success.message'), $_('alert.dismiss'));
    } catch (e) {
      await jinyaAlert($_('design.themes.configuration.error.title'), $_('design.themes.configuration.error.message'), $_('alert.dismiss'));
    }
  }

  async function discardLinks() {
    await selectTheme(selectedTheme);
  }

  async function saveLinks() {
    try {
      const promises = [];
      promises.push(...Object.keys(themeFiles).map(key => put(`/api/theme/${selectedTheme.id}/file/${key}`, { file: themeFiles[key] })));
      promises.push(...Object.keys(themePages).map(key => put(`/api/theme/${selectedTheme.id}/page/${key}`, { page: themePages[key] })));
      promises.push(...Object.keys(themeSegmentPages).map(key => put(`/api/theme/${selectedTheme.id}/segment-page/${key}`, { segmentPage: themeSegmentPages[key] })));
      promises.push(...Object.keys(themeMenus).map(key => put(`/api/theme/${selectedTheme.id}/menu/${key}`, { menu: themeMenus[key] })));
      promises.push(...Object.keys(themeForms).map(key => put(`/api/theme/${selectedTheme.id}/form/${key}`, { form: themeForms[key] })));
      promises.push(...Object.keys(themeGalleries).map(key => put(`/api/theme/${selectedTheme.id}/gallery/${key}`, { gallery: themeGalleries[key] })));
      await Promise.all(promises);
      selectTheme(selectedTheme);
      await jinyaAlert($_('design.themes.links.success.title'), $_('design.themes.links.success.message'), $_('alert.dismiss'));
    } catch (e) {
      await jinyaAlert($_('design.themes.links.error.title'), $_('design.themes.links.error.message'), $_('alert.dismiss'));
    }
  }

  onMount(async () => {
    get('/api/segment-page').then(result => segmentPages = result.items ?? []);
    get('/api/page').then(result => pages = result.items ?? []);
    get('/api/form').then(result => forms = result.items ?? []);
    get('/api/menu').then(result => menus = result.items ?? []);
    get('/api/media/gallery').then(result => galleries = result.items ?? []);
    get('/api/media/file').then(result => files = result.items ?? []);

    const result = await get('/api/theme');
    themes = result.items;
    await selectTheme(themes[0]);
  });
</script>

<div class="cosmo-list">
    <nav class="cosmo-list__items">
        {#each themes as theme (theme.id)}
            <a class:cosmo-list__item--active={theme.id === selectedTheme.id} class="cosmo-list__item"
               on:click={() => selectTheme(theme)}>{theme.displayName}</a>
        {/each}
    </nav>
    <div class="cosmo-list__content jinya-designer">
        {#if selectedTheme}
            <div class="jinya-designer__title">
                <span class="cosmo-title">{selectedTheme.displayName}</span>
            </div>
            <div class="cosmo-toolbar cosmo-toolbar--designer">
                <div class="cosmo-toolbar__group">
                    <button disabled={!selectedTheme} on:click={activateTheme}
                            class="cosmo-button">{$_('design.themes.action.activate')}</button>
                    <button disabled={!selectedTheme} on:click={compileAssets}
                            class="cosmo-button">{$_('design.themes.action.compile_assets')}</button>
                </div>
            </div>
        {/if}
        <div class="jinya-designer__content jinya-designer__content--theme">
            <div class="cosmo-tab-control cosmo-tab-control--theme">
                <div class="cosmo-tab-control__tabs">
                    <a on:click={() => selectedTab = 'details'}
                       class:cosmo-tab-control__tab-link--active={selectedTab === 'details'}
                       class="cosmo-tab-control__tab-link">{$_('design.themes.tabs.details')}</a>
                    <a on:click={() => selectedTab = 'configuration'}
                       class:cosmo-tab-control__tab-link--active={selectedTab === 'configuration'}
                       class="cosmo-tab-control__tab-link">{$_('design.themes.tabs.configuration')}</a>
                    <a on:click={() => selectedTab = 'links'}
                       class:cosmo-tab-control__tab-link--active={selectedTab === 'links'}
                       class="cosmo-tab-control__tab-link">{$_('design.themes.tabs.links')}</a>
                    <a on:click={() => selectedTab = 'variables'}
                       class:cosmo-tab-control__tab-link--active={selectedTab === 'variables'}
                       class="cosmo-tab-control__tab-link">{$_('design.themes.tabs.variables')}</a>
                </div>
                {#if selectedTheme}
                    {#if selectedTab === 'details'}
                        <div class="cosmo-tab-control__content cosmo-tab-control__content--details cosmo-tab-control__content--theme">
                            <img class="jinya-theme-details__preview"
                                 src={previewImage}
                                 alt={$_('design.themes.details.preview')}>
                            <div class="jinya-theme-details__description">{@html selectedTheme?.description}</div>
                        </div>
                    {/if}
                    {#if selectedTab === 'configuration' && configurationStructure && configurationStructure.groups}
                        <div class="cosmo-tab-control__content cosmo-tab-control__content--theme">
                            <div class="cosmo-input__group">
                                {#each configurationStructure.groups as group (group.name)}
                                    <span class="cosmo-input__header">{group.title}</span>
                                    {#each group.fields as field (field.name)}
                                        {#if field.type === 'string'}
                                            <label class="cosmo-label"
                                                   for={`${group.name}_${field.name}`}>{field.label}</label>
                                            <input id={`${group.name}_${field.name}`} class="cosmo-input" type="text"
                                                   placeholder={defaultConfiguration[group.name][field.name]}
                                                   bind:value={configuration[group.name][field.name]}>
                                        {:else if field.type === 'boolean'}
                                            <div class="cosmo-checkbox__group">
                                                <input bind:checked={configuration[group.name][field.name]}
                                                       type="checkbox" id={`${group.name}_${field.name}`}
                                                       class="cosmo-checkbox">
                                                <label for={`${group.name}_${field.name}`}>{$_(field.label)}</label>
                                            </div>
                                        {/if}
                                    {/each}
                                {/each}
                            </div>
                            <div class="cosmo-button__container">
                                <button on:click={discardConfiguration}
                                        class="cosmo-button">{$_('design.themes.configuration.discard')}</button>
                                <button on:click={saveConfiguration}
                                        class="cosmo-button">{$_('design.themes.configuration.save')}</button>
                            </div>
                        </div>
                    {/if}
                    {#if selectedTab === 'links' && configurationStructure && configurationStructure.links}
                        <div class="cosmo-tab-control__content cosmo-tab-control__content--theme">
                            <div class="cosmo-input__group">
                                {#if configurationStructure.links.files}
                                    <span class="cosmo-input__header">{$_('design.themes.links.files')}</span>
                                    {#each Object.keys(configurationStructure.links.files) as link (`file_${link}`)}
                                        <label for={`files_${link}`}
                                               class="cosmo-label">{configurationStructure.links.files[link]}</label>
                                        <select required bind:value={themeFiles[link]} id={`files_${link}`}
                                                class="cosmo-select">
                                            {#each files as file}
                                                <option value={file.id}>{file.name}</option>
                                            {/each}
                                        </select>
                                    {/each}
                                {/if}
                                {#if configurationStructure.links.galleries}
                                    <span class="cosmo-input__header">{$_('design.themes.links.galleries')}</span>
                                    {#each Object.keys(configurationStructure.links.galleries) as link (`gallery_${link}`)}
                                        <label for={`galleries_${link}`}
                                               class="cosmo-label">{configurationStructure.links.galleries[link]}</label>
                                        <select required bind:value={themeGalleries[link]} id={`galleries_${link}`}
                                                class="cosmo-select">
                                            {#each galleries as gallery}
                                                <option value={gallery.id}>{gallery.name}</option>
                                            {/each}
                                        </select>
                                    {/each}
                                {/if}
                                {#if configurationStructure.links.pages}
                                    <span class="cosmo-input__header">{$_('design.themes.links.pages')}</span>
                                    {#each Object.keys(configurationStructure.links.pages) as link (`page_${link}`)}
                                        <label for={`pages_${link}`}
                                               class="cosmo-label">{configurationStructure.links.pages[link]}</label>
                                        <select required bind:value={themePages[link]} id={`pages_${link}`}
                                                class="cosmo-select">
                                            {#each pages as page}
                                                <option value={page.id}>{page.title}</option>
                                            {/each}
                                        </select>
                                    {/each}
                                {/if}
                                {#if configurationStructure.links.segment_pages}
                                    <span class="cosmo-input__header">{$_('design.themes.links.segment_pages')}</span>
                                    {#each Object.keys(configurationStructure.links.segment_pages) as link (`page_${link}`)}
                                        <label for={`segment_pages_${link}`}
                                               class="cosmo-label">{configurationStructure.links.segment_pages[link]}</label>
                                        <select required bind:value={themeSegmentPages[link]}
                                                id={`segment_pages_${link}`}
                                                class="cosmo-select">
                                            {#each segmentPages as page}
                                                <option value={page.id}>{page.name}</option>
                                            {/each}
                                        </select>
                                    {/each}
                                {/if}
                                {#if configurationStructure.links.forms}
                                    <span class="cosmo-input__header">{$_('design.themes.links.forms')}</span>
                                    {#each Object.keys(configurationStructure.links.forms) as link (`form_${link}`)}
                                        <label for={`forms_${link}`}
                                               class="cosmo-label">{configurationStructure.links.forms[link]}</label>
                                        <select required bind:value={themeForms[link]} id={`forms_${link}`}
                                                class="cosmo-select">
                                            {#each forms as form}
                                                <option value={form.id}>{form.title}</option>
                                            {/each}
                                        </select>
                                    {/each}
                                {/if}
                                {#if configurationStructure.links.menus}
                                    <span class="cosmo-input__header">{$_('design.themes.links.menus')}</span>
                                    {#each Object.keys(configurationStructure.links.menus) as link (`menu_${link}`)}
                                        <label for={`menus_${link}`}
                                               class="cosmo-label">{configurationStructure.links.menus[link]}</label>
                                        <select required bind:value={themeMenus[link]} id={`menus_${link}`}
                                                class="cosmo-select">
                                            {#each menus as menu}
                                                <option value={menu.id}>{menu.name}</option>
                                            {/each}
                                        </select>
                                    {/each}
                                {/if}
                            </div>
                            <div class="cosmo-button__container">
                                <button on:click={discardLinks}
                                        class="cosmo-button">{$_('design.themes.links.discard')}</button>
                                <button on:click={saveLinks}
                                        class="cosmo-button">{$_('design.themes.links.save')}</button>
                            </div>
                        </div>
                    {/if}
                    {#if selectedTab === 'variables'}
                        <div class="cosmo-tab-control__content cosmo-tab-control__content--theme">
                            <div class="cosmo-input__group">
                                {#each Object.keys(defaultVariables) as variable (variable)}
                                    <label for={variable}>{variable}</label>
                                    <input id={variable} placeholder={defaultVariables[variable]} type="text"
                                           class="cosmo-input" bind:value={variables[variable]}>
                                {/each}
                            </div>
                            <div class="cosmo-button__container">
                                <button on:click={discardVariables}
                                        class="cosmo-button">{$_('design.themes.variables.discard')}</button>
                                <button on:click={saveVariables}
                                        class="cosmo-button">{$_('design.themes.variables.save')}</button>
                            </div>
                        </div>
                    {/if}
                {/if}
            </div>
        </div>
    </div>
</div>
