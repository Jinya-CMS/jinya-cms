<script>
  import page from 'page';
  import { _ } from 'svelte-i18n';
  import FileView from './media/FileView.svelte';
  import GalleryView from './media/GalleryView.svelte';
  import SimplePageView from './pages/SimplePageView.svelte';
  import SegmentPageView from './pages/SegmentPageView.svelte';
  import FormView from './forms/FormView.svelte';
  import MenuView from './design/MenuView.svelte';
  import ThemeView from './design/ThemeView.svelte';
  import MyProfileView from './my-jinya/MyProfileView.svelte';
  import ActiveSessionsView from './my-jinya/ActiveSessionsView.svelte';
  import ActiveDevicesView from './my-jinya/ActiveDevicesView.svelte';
  import VersionView from './maintenance/VersionView.svelte';
  import JinyaConfigurationView from './maintenance/JinyaConfigurationView.svelte';
  import PhpInfoView from './maintenance/PhpInfoView.svelte';
  import MySqlInfoView from './database/MySqlInfoView.svelte';
  import TablesView from './database/TablesView.svelte';
  import QueryToolView from './database/QueryToolView.svelte';
  import { deleteJinyaApiKey, deleteRoles, getRoles } from '../storage/authentication/storage';
  import { createEventDispatcher, onMount } from 'svelte';
  import { get, getHost, head, post, put, upload } from '../http/request';

  const dispatch = createEventDispatcher();
  let activeRoute;
  let activeCategory;
  let activeComponent;
  let profilepicture = '';
  const roles = getRoles();
  let allowBackstage = roles.includes('ROLE_ADMIN');
  let allowFrontstage = roles.includes('ROLE_WRITER');
  let isBackstage = !allowFrontstage;
  let me;
  let filesToUpload = 0;
  let filesUploaded = 0;
  let uploadDone = false;

  async function checkApiKey(ctx, next) {
    try {
      await head('/api/login');
      next();
    } catch (e) {
      deleteJinyaApiKey();
      deleteRoles();
      dispatch('logout');
    }
  }

  if (allowFrontstage) {
    page('/designer/media/files', checkApiKey, () => {
      activeRoute = 'files';
      activeCategory = 'media';
      isBackstage = false;
    });
    page('/designer/media/galleries', checkApiKey, () => {
      activeRoute = 'galleries';
      activeCategory = 'media';
      isBackstage = false;
      activeComponent = GalleryView;
    });

    page('/designer/pages-and-forms/simple-pages', checkApiKey, () => {
      activeRoute = 'simple-pages';
      activeCategory = 'pages-and-forms';
      isBackstage = false;
      activeComponent = SimplePageView;
    });
    page('/designer/pages-and-forms/segment-pages', checkApiKey, () => {
      activeRoute = 'segment-pages';
      activeCategory = 'pages-and-forms';
      isBackstage = false;
      activeComponent = SegmentPageView;
    });
    page('/designer/pages-and-forms/forms', checkApiKey, () => {
      activeRoute = 'forms';
      activeCategory = 'pages-and-forms';
      isBackstage = false;
      activeComponent = FormView;
    });

    page('/designer/design/menus', checkApiKey, () => {
      activeRoute = 'menus';
      activeCategory = 'design';
      isBackstage = false;
      activeComponent = MenuView;
    });
    page('/designer/design/themes', checkApiKey, () => {
      activeRoute = 'themes';
      activeCategory = 'design';
      isBackstage = false;
      activeComponent = ThemeView;
    });

    page('/designer/my-jinya/my-profile', checkApiKey, () => {
      activeRoute = 'my-profile';
      activeCategory = 'my-jinya';
      isBackstage = false;
    });
    page('/designer/my-jinya/active-sessions', checkApiKey, () => {
      activeRoute = 'active-sessions';
      activeCategory = 'my-jinya';
      isBackstage = false;
      activeComponent = ActiveSessionsView;
    });
    page('/designer/my-jinya/active-devices', checkApiKey, () => {
      activeRoute = 'active-devices';
      activeCategory = 'my-jinya';
      isBackstage = false;
      activeComponent = ActiveDevicesView;
    });
  }

  if (allowBackstage) {
    page('/designer/backstage/maintenance/update', checkApiKey, () => {
      activeRoute = 'update';
      activeCategory = 'maintenance';
      isBackstage = true;
      activeComponent = VersionView;
    });
    page('/designer/backstage/maintenance/app-config', checkApiKey, () => {
      activeRoute = 'app-config';
      activeCategory = 'maintenance';
      isBackstage = true;
      activeComponent = JinyaConfigurationView;
    });
    page('/designer/backstage/maintenance/php-info', checkApiKey, () => {
      activeRoute = 'php-info';
      activeCategory = 'maintenance';
      isBackstage = true;
      activeComponent = PhpInfoView;
    });

    page('/designer/backstage/database/mysql-info', checkApiKey, () => {
      activeRoute = 'mysql-info';
      activeCategory = 'database';
      isBackstage = true;
      activeComponent = MySqlInfoView;
    });
    page('/designer/backstage/database/tables', checkApiKey, () => {
      activeRoute = 'tables';
      activeCategory = 'database';
      isBackstage = true;
      activeComponent = TablesView;
    });
    page('/designer/backstage/database/query-tool', checkApiKey, () => {
      activeRoute = 'query-tool';
      activeCategory = 'database';
      isBackstage = true;
      activeComponent = QueryToolView;
    });

    page('/designer/backstage/artists', checkApiKey, () => {
      activeRoute = 'artists';
      activeCategory = 'artists';
      isBackstage = true;
    });

    page.redirect('/designer/backstage', '/designer/backstage/maintenance/update');
    page.redirect('/designer/backstage/*', '/designer/backstage/maintenance/update');
  }

  if (allowFrontstage) {
    page.redirect('/designer', '/designer/media/files');
    page.redirect('/designer/*', '/designer/media/files');
  } else if (allowBackstage) {
    page.redirect('/designer', '/designer/backstage/maintenance/update');
    page.redirect('/designer/*', '/designer/backstage/maintenance/update');
  }

  async function updateMe() {
    me = await get('/api/me');
    profilepicture = `${getHost()}/${me.profilePicture}`;
  }

  onMount(async () => {
    page.start();
    await updateMe();
  });

  function logout() {
    deleteJinyaApiKey();
    deleteRoles();
    dispatch('logout');
  }

  /**
   *
   * @param event CustomEvent
   */
  async function startUpload(event) {
    const files = event.detail;
    filesToUpload = files.length;
    filesUploaded = 0;
    uploadDone = false;
    for (const file of files) {
      try {
        const postResult = await post('/api/media/file', { name: file.name });
        await put(`/api/media/file/${postResult.id}/content`);
        await upload(`/api/media/file/${postResult.id}/content/0`, file);
        await put(`/api/media/file/${postResult.id}/content/finish`);
      } catch (e) {
        if (e.status === 409) {
          console.error($_('media.files.upload_single_file.error.conflict'));
        } else {
          console.error($_('media.files.upload_single_file.error.generic'));
        }
      }
      filesUploaded++;
    }
    uploadDone = true;
  }
</script>

<main class="cosmo-page-layout">
    <div class="cosmo-top-bar">
        <div class="cosmo-top-bar__menu">
            {#if isBackstage && allowFrontstage}
                <a href="/designer/media/files"
                   class="cosmo-top-bar__menu-item">{$_('media.menu.title')}</a>
                <a href="/designer/pages-and-forms/simple-pages"
                   class="cosmo-top-bar__menu-item">{$_('pages_and_forms.menu.title')}</a>
                <a href="/designer/design/menus"
                   class="cosmo-top-bar__menu-item">{$_('design.menu.title')}</a>
                <a href="/designer/my-jinya/my-profile"
                   class="cosmo-top-bar__menu-item">{$_('my_jinya.menu.title')}</a>
            {:else if allowBackstage && !isBackstage}
                <a href="/designer/backstage/maintenance/update"
                   class="cosmo-top-bar__menu-item">{$_('maintenance.menu.title')}</a>
                <a href="/designer/backstage/database/mysql-info"
                   class="cosmo-top-bar__menu-item">{$_('database.menu.title')}</a>
                <a href="/designer/backstage/artists" class="cosmo-top-bar__menu-item">{$_('artists.menu.title')}</a>
            {/if}
        </div>
        <img src={profilepicture} class="cosmo-profile-picture" alt={me?.artistName}>
        <a class="cosmo-top-bar__menu-item jinya-top-bar__menu-item--logout"
           on:click={logout}>{$_('top_menu.logout')}</a>
    </div>
    <div class="cosmo-menu-bar">
        <div class="cosmo-menu-bar__touch"></div>
        <button on:click={() => page.back()} type="button" class="cosmo-menu-bar__back-button"></button>
        <nav class="cosmo-menu-bar__menu-collection">
            <div class="cosmo-menu-bar__main-menu">
                {#if isBackstage}
                    <a href="/designer/backstage/maintenance/update"
                       class:cosmo-menu-bar__main-item--active={activeCategory === 'maintenance'}
                       class="cosmo-menu-bar__main-item">{$_('maintenance.menu.title')}</a>
                    <a href="/designer/backstage/database/mysql-info"
                       class:cosmo-menu-bar__main-item--active={activeCategory === 'database'}
                       class="cosmo-menu-bar__main-item">{$_('database.menu.title')}</a>
                    <a href="/designer/backstage/artists"
                       class:cosmo-menu-bar__main-item--active={activeCategory === 'artists'}
                       class="cosmo-menu-bar__main-item">{$_('artists.menu.title')}</a>
                {:else}
                    <a href="/designer/media/files" class:cosmo-menu-bar__main-item--active={activeCategory === 'media'}
                       class="cosmo-menu-bar__main-item">{$_('media.menu.title')}</a>
                    <a href="/designer/pages-and-forms/simple-pages"
                       class:cosmo-menu-bar__main-item--active={activeCategory === 'pages-and-forms'}
                       class="cosmo-menu-bar__main-item">{$_('pages_and_forms.menu.title')}</a>
                    <a href="/designer/design/menus"
                       class:cosmo-menu-bar__main-item--active={activeCategory === 'design'}
                       class="cosmo-menu-bar__main-item">{$_('design.menu.title')}</a>
                    <a href="/designer/my-jinya/my-profile"
                       class:cosmo-menu-bar__main-item--active={activeCategory === 'my-jinya'}
                       class="cosmo-menu-bar__main-item">{$_('my_jinya.menu.title')}</a>
                {/if}
            </div>
            <div class="cosmo-menu-bar__sub-menu">
                {#if activeCategory === 'media'}
                    <a href="/designer/media/files" class="cosmo-menu-bar__sub-item"
                       class:cosmo-menu-bar__sub-item--active={activeRoute === 'files'}>{$_('media.menu.files')}</a>
                    <a href="/designer/media/galleries" class="cosmo-menu-bar__sub-item"
                       class:cosmo-menu-bar__sub-item--active={activeRoute === 'galleries'}>{$_('media.menu.galleries')}</a>
                {:else if activeCategory === 'pages-and-forms'}
                    <a href="/designer/pages-and-forms/simple-pages" class="cosmo-menu-bar__sub-item"
                       class:cosmo-menu-bar__sub-item--active={activeRoute === 'simple-pages'}>{$_('pages_and_forms.menu.simple_pages')}</a>
                    <a href="/designer/pages-and-forms/segment-pages" class="cosmo-menu-bar__sub-item"
                       class:cosmo-menu-bar__sub-item--active={activeRoute === 'segment-pages'}>{$_('pages_and_forms.menu.segment_pages')}</a>
                    <a href="/designer/pages-and-forms/forms" class="cosmo-menu-bar__sub-item"
                       class:cosmo-menu-bar__sub-item--active={activeRoute === 'forms'}>{$_('pages_and_forms.menu.forms')}</a>
                {:else if activeCategory === 'design'}
                    <a href="/designer/design/menus" class="cosmo-menu-bar__sub-item"
                       class:cosmo-menu-bar__sub-item--active={activeRoute === 'menus'}>{$_('design.menu.menus')}</a>
                    <a href="/designer/design/themes" class="cosmo-menu-bar__sub-item"
                       class:cosmo-menu-bar__sub-item--active={activeRoute === 'themes'}>{$_('design.menu.themes')}</a>
                {:else if activeCategory === 'my-jinya'}
                    <a href="/designer/my-jinya/my-profile" class="cosmo-menu-bar__sub-item"
                       class:cosmo-menu-bar__sub-item--active={activeRoute === 'my-profile'}>{$_('my_jinya.menu.my_profile')}</a>
                    <a href="/designer/my-jinya/active-sessions" class="cosmo-menu-bar__sub-item"
                       class:cosmo-menu-bar__sub-item--active={activeRoute === 'active-sessions'}>{$_('my_jinya.menu.active_sessions')}</a>
                    <a href="/designer/my-jinya/active-devices" class="cosmo-menu-bar__sub-item"
                       class:cosmo-menu-bar__sub-item--active={activeRoute === 'active-devices'}>{$_('my_jinya.menu.active_devices')}</a>
                {:else if activeCategory === 'maintenance'}
                    <a href="/designer/backstage/maintenance/update" class="cosmo-menu-bar__sub-item"
                       class:cosmo-menu-bar__sub-item--active={activeRoute === 'update'}>{$_('maintenance.menu.update')}</a>
                    <a href="/designer/backstage/maintenance/app-config" class="cosmo-menu-bar__sub-item"
                       class:cosmo-menu-bar__sub-item--active={activeRoute === 'app-config'}>{$_('maintenance.menu.app_config')}</a>
                    <a href="/designer/backstage/maintenance/php-info" class="cosmo-menu-bar__sub-item"
                       class:cosmo-menu-bar__sub-item--active={activeRoute === 'php-info'}>{$_('maintenance.menu.php_info')}</a>
                {:else if activeCategory === 'database'}
                    <a href="/designer/backstage/database/mysql-info" class="cosmo-menu-bar__sub-item"
                       class:cosmo-menu-bar__sub-item--active={activeRoute === 'mysql-info'}>{$_('database.menu.mysql_info')}</a>
                    <a href="/designer/backstage/database/tables" class="cosmo-menu-bar__sub-item"
                       class:cosmo-menu-bar__sub-item--active={activeRoute === 'tables'}>{$_('database.menu.tables')}</a>
                    <a href="/designer/backstage/database/query-tool" class="cosmo-menu-bar__sub-item"
                       class:cosmo-menu-bar__sub-item--active={activeRoute === 'query-tool'}>{$_('database.menu.query_tool')}</a>
                {:else if activeCategory === 'artists'}
                    <a href="/designer/backstage/artists" class="cosmo-menu-bar__sub-item"
                       class:cosmo-menu-bar__sub-item--active={activeRoute === 'artists'}>{$_('artists.menu.artists')}</a>
                {/if}
            </div>
        </nav>
    </div>
    <div class="cosmo-page-body jinya-page-body--app">
        <div class="cosmo-page-body__content">
            {#if activeRoute === 'files'}
                <FileView uploadDone={uploadDone} on:multiple-files-upload-start={startUpload} />
            {:else if activeRoute === 'my-profile'}
                <MyProfileView on:update-me={updateMe} on:logout={logout} />
            {:else}
                <svelte:component this={activeComponent} />
            {/if}
        </div>
    </div>
    {#if 0 < filesToUpload}
        <div class="cosmo-bottom-bar">
            <span class="cosmo-progress-bar__top-label">
                {#if filesUploaded !== filesToUpload}
                    {$_('bottom_bar.upload_title.uploading')}
                {:else}
                    {$_('bottom_bar.upload_title.uploaded')}
                {/if}
            </span>
            <progress class="cosmo-progress-bar" value={filesUploaded} max={filesToUpload}></progress>
            <span class="cosmo-progress-bar__bottom-label">
                {$_('bottom_bar.upload_progress', { values: { filesToUpload, filesUploaded } })}
            </span>
        </div>
    {/if}
</main>
