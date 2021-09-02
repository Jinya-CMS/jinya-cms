<script>
  import { onMount } from 'svelte';
  import { _ } from 'svelte-i18n';
  import { get, getHost, httpDelete, post, put } from '../../http/request';
  import { jinyaAlert } from '../../ui/alert';
  import { jinyaConfirm } from '../../ui/confirm';

  let createArtistName = '';
  let createArtistEmail = '';
  let createArtistPassword = '';
  let createArtistIsReader = true;
  let createArtistIsWriter = true;
  let createArtistIsAdmin = false;
  let createArtistOpen = false;

  let editArtistName = '';
  let editArtistEmail = '';
  let editArtistPassword = '';
  let editArtistIsReader = true;
  let editArtistIsWriter = true;
  let editArtistIsAdmin = false;
  let editArtistOpen = false;

  let artists = [];
  let selectedArtist = null;
  let me = {};

  function selectArtist(artist) {
    selectedArtist = artist;
    editArtistName = selectedArtist.artistName;
    editArtistEmail = selectedArtist.email;
    editArtistPassword = selectedArtist.password;
    editArtistIsReader = selectedArtist.roles.includes('ROLE_READER');
    editArtistIsWriter = selectedArtist.roles.includes('ROLE_WRITER');
    editArtistIsAdmin = selectedArtist.roles.includes('ROLE_ADMIN');
    editArtistOpen = false;
  }

  async function loadArtists() {
    const response = await get('/api/user');
    artists = response.items;
    if (artists.length > 0) {
      selectArtist(artists[0]);
    }
  }

  async function deleteArtist() {
    const result = await jinyaConfirm($_('artists.delete.title'), $_('artists.delete.message', { values: selectedArtist }), $_('artists.delete.delete'), $_('artists.delete.keep'));
    if (result) {
      await httpDelete(`/api/user/${selectedArtist.id}`);
      await loadArtists();
    }
  }

  async function enableArtist() {
    const result = await jinyaConfirm($_('artists.enable.title'), $_('artists.enable.message', { values: selectedArtist }), $_('artists.enable.delete'), $_('artists.enable.keep'));
    if (result) {
      const id = selectedArtist.id;
      await put(`/api/user/${selectedArtist.id}/activation`);
      await loadArtists();
      const artist = artists.find(item => item.id === id);
      selectArtist(artist);
    }
  }

  async function disableArtist() {
    const result = await jinyaConfirm($_('artists.disable.title'), $_('artists.disable.message', { values: selectedArtist }), $_('artists.disable.delete'), $_('artists.disable.keep'));
    if (result) {
      const id = selectedArtist.id;
      await httpDelete(`/api/user/${selectedArtist.id}/activation`);
      await loadArtists();
      const artist = artists.find(item => item.id === id);
      selectArtist(artist);
    }
  }

  async function createArtist() {
    if (createArtistEmail && createArtistName && createArtistPassword) {
      try {
        const roles = [];
        if (createArtistIsReader) {
          roles.push('ROLE_READER');
        }
        if (createArtistIsWriter) {
          roles.push('ROLE_WRITER');
        }
        if (createArtistIsAdmin) {
          roles.push('ROLE_ADMIN');
        }
        await post('/api/user', {
          artistName: createArtistName,
          email: createArtistEmail,
          password: createArtistPassword,
          roles,
        });
        await loadArtists();
        const artist = artists.find(item => item.email === createArtistEmail);
        selectArtist(artist);
        onCreateCancel();
      } catch (e) {
        if (e.status === 409) {
          await jinyaAlert($_('artists.create.error.title'), $_('artists.create.error.conflict'), $_('alert.dismiss'));
        } else {
          await jinyaAlert($_('artists.create.error.title'), $_('artists.create.error.generic'), $_('alert.dismiss'));
        }
      }
    }
  }

  function openEdit() {
    editArtistOpen = true;
  }

  async function updateArtist() {
    if (editArtistEmail && editArtistName) {
      try {
        const roles = [];
        if (editArtistIsReader) {
          roles.push('ROLE_READER');
        }
        if (editArtistIsWriter) {
          roles.push('ROLE_WRITER');
        }
        if (editArtistIsAdmin) {
          roles.push('ROLE_ADMIN');
        }
        const data = {
          artistName: editArtistName,
          email: editArtistEmail,
          roles,
        };
        if (editArtistPassword) {
          data.password = editArtistPassword;
        }

        await put(`/api/user/${selectedArtist.id}`, data);
        await loadArtists();
        const artist = artists.find(item => item.email === editArtistEmail);
        await selectArtist(artist);
        editArtistOpen = false;
      } catch (e) {
        if (e.status === 409) {
          await jinyaAlert($_('artists.edit.error.title'), $_('artists.edit.error.conflict'), $_('alert.dismiss'));
        } else {
          await jinyaAlert($_('artists.create.edit.title'), $_('artists.edit.error.generic'), $_('alert.dismiss'));
        }
      }
    }
  }

  function onCreateCancel() {
    createArtistName = '';
    createArtistEmail = '';
    createArtistPassword = '';
    createArtistIsReader = true;
    createArtistIsWriter = true;
    createArtistIsAdmin = false;
    createArtistOpen = false;
  }

  function onEditCancel() {
    selectArtist(selectedArtist);
    editArtistOpen = false;
  }

  onMount(async () => {
    await loadArtists();
    me = await get('/api/me');
  });
</script>

<div class="cosmo-list">
    <nav class="cosmo-list__items">
        {#each artists as artist (artist.id)}
            <a class:cosmo-list__item--active={artist.id === selectedArtist.id} class="cosmo-list__item"
               on:click={() => selectArtist(artist)}>{artist.artistName}</a>
        {/each}
        <button on:click={() => createArtistOpen = true}
                class="cosmo-button cosmo-button--full-width">{$_('artists.action.new')}</button>
    </nav>
    <div class="cosmo-list__content jinya-designer">
        {#if selectedArtist}
            <div class="jinya-designer__title">
                <span class="cosmo-title">{selectedArtist.artistName}</span>
            </div>
            <div class="cosmo-toolbar cosmo-toolbar--designer">
                <div class="cosmo-toolbar__group">
                    <button disabled={!selectedArtist || me.id === selectedArtist.id} on:click={openEdit}
                            class="cosmo-button">{$_('artists.action.edit')}</button>
                    <button disabled={!selectedArtist || selectedArtist.enabled || me.id === selectedArtist.id}
                            on:click={enableArtist} class="cosmo-button">{$_('artists.action.enable')}</button>
                    <button disabled={!selectedArtist || !selectedArtist.enabled || me.id === selectedArtist.id}
                            on:click={disableArtist} class="cosmo-button">{$_('artists.action.disable')}</button>
                    <button disabled={!selectedArtist || me.id === selectedArtist.id} on:click={deleteArtist}
                            class="cosmo-button">{$_('artists.action.delete')}</button>
                </div>
            </div>
        {/if}
        <div class="jinya-profile__container">
            {#if selectedArtist}
                <div class="jinya-profile__sidebar">
                    <img src={`${getHost()}${selectedArtist.profilePicture}`} class="jinya-profile__picture"
                         alt={selectedArtist.artistName}>
                    <span class="jinya-profile__label">{$_('my_jinya.my_profile.email')}</span>
                    <span class="jinya-profile__detail">{selectedArtist.email}</span>
                </div>
                <div class="jinya-profile__about-me">
                    <div class="cosmo-title">{selectedArtist.artistName}</div>
                    <div>
                        {@html selectedArtist.aboutMe}
                    </div>
                </div>
            {/if}
        </div>
    </div>
</div>
{#if createArtistOpen}
    <div class="cosmo-modal__backdrop"></div>
    <div class="cosmo-modal__container">
        <div class="cosmo-modal">
            <h1 class="cosmo-modal__title">{$_('artists.create.title')}</h1>
            <div class="cosmo-modal__content">
                <div class="cosmo-input__group">
                    <label for="createArtistName" class="cosmo-label">{$_('artists.create.name')}</label>
                    <input required bind:value={createArtistName} type="text" id="createArtistName" class="cosmo-input">
                    <label for="createArtistEmail" class="cosmo-label">{$_('artists.create.email')}</label>
                    <input required bind:value={createArtistEmail} type="email" id="createArtistEmail"
                           class="cosmo-input">
                    <label for="createArtistPassword" class="cosmo-label">{$_('artists.create.password')}</label>
                    <input required bind:value={createArtistPassword} type="password" id="createArtistPassword"
                           class="cosmo-input">
                    <span class="cosmo-input__header cosmo-input__header--small">{$_('artists.create.roles')}</span>
                    <div class="cosmo-checkbox__group">
                        <input bind:checked={createArtistIsReader} type="checkbox" id="createArtistIsReader"
                               class="cosmo-checkbox">
                        <label for="createArtistIsReader">{$_('artists.create.is_reader')}</label>
                    </div>
                    <div class="cosmo-checkbox__group">
                        <input bind:checked={createArtistIsWriter} type="checkbox" id="createArtistIsWriter"
                               class="cosmo-checkbox">
                        <label for="createArtistIsWriter">{$_('artists.create.is_writer')}</label>
                    </div>
                    <div class="cosmo-checkbox__group">
                        <input bind:checked={createArtistIsAdmin} type="checkbox" id="createArtistIsAdmin"
                               class="cosmo-checkbox">
                        <label for="createArtistIsAdmin">{$_('artists.create.is_admin')}</label>
                    </div>
                </div>
            </div>
            <div class="cosmo-modal__button-bar">
                <button class="cosmo-button"
                        on:click={onCreateCancel}>{$_('artists.create.cancel')}</button>
                <button class="cosmo-button"
                        on:click={createArtist}>{$_('artists.create.create')}</button>
            </div>
        </div>
    </div>
{/if}
{#if editArtistOpen}
    <div class="cosmo-modal__backdrop"></div>
    <div class="cosmo-modal__container">
        <div class="cosmo-modal">
            <h1 class="cosmo-modal__title">{$_('artists.edit.title')}</h1>
            <div class="cosmo-modal__content">
                <div class="cosmo-input__group">
                    <label for="editArtistName" class="cosmo-label">{$_('artists.edit.name')}</label>
                    <input required bind:value={editArtistName} type="text" id="editArtistName" class="cosmo-input">
                    <label for="editArtistEmail" class="cosmo-label">{$_('artists.edit.email')}</label>
                    <input required bind:value={editArtistEmail} type="email" id="editArtistEmail"
                           class="cosmo-input">
                    <label for="editArtistPassword" class="cosmo-label">{$_('artists.edit.password')}</label>
                    <input bind:value={editArtistPassword} type="password" id="editArtistPassword"
                           class="cosmo-input">
                    <span class="cosmo-input__header cosmo-input__header--small">{$_('artists.edit.roles')}</span>
                    <div class="cosmo-checkbox__group">
                        <input bind:checked={editArtistIsReader} type="checkbox" id="editArtistIsReader"
                               class="cosmo-checkbox">
                        <label for="editArtistIsReader">{$_('artists.edit.is_reader')}</label>
                    </div>
                    <div class="cosmo-checkbox__group">
                        <input bind:checked={editArtistIsWriter} type="checkbox" id="editArtistIsWriter"
                               class="cosmo-checkbox">
                        <label for="editArtistIsWriter">{$_('artists.edit.is_writer')}</label>
                    </div>
                    <div class="cosmo-checkbox__group">
                        <input bind:checked={editArtistIsAdmin} type="checkbox" id="editArtistIsAdmin"
                               class="cosmo-checkbox">
                        <label for="editArtistIsAdmin">{$_('artists.edit.is_admin')}</label>
                    </div>
                </div>
            </div>
            <div class="cosmo-modal__button-bar">
                <button class="cosmo-button"
                        on:click={onEditCancel}>{$_('artists.edit.cancel')}</button>
                <button class="cosmo-button"
                        on:click={updateArtist}>{$_('artists.edit.update')}</button>
            </div>
        </div>
    </div>
{/if}
