<script>
  import { _ } from 'svelte-i18n';
  import { get, getHost, post, put, upload } from '../../http/request';
  import { createEventDispatcher, onMount, tick } from 'svelte';
  import { createTiny } from '../../ui/tiny';
  import { jinyaAlert } from '../../ui/alert';

  const dispatch = createEventDispatcher();
  let changePasswordOpen = false;
  let artist = null;
  let editMode = false;
  let email = '';
  let artistName = '';
  let profilePictureName = '';
  let profilePictureFile;
  let aboutMeElement;
  let aboutMeTiny;
  let oldPassword;
  let newPassword;
  let newPasswordRepeat;

  $: if (profilePictureFile) {
    const file = profilePictureFile[0];
    profilePictureName = file.name;
  }

  async function editProfile() {
    editMode = true;
    email = artist.email;
    artistName = artist.artistName;
    await tick();
    aboutMeTiny = await createTiny(aboutMeElement);
    aboutMeTiny.setContent(artist.aboutMe);
  }

  function discardProfile() {
    aboutMeTiny.destroy();
    editMode = false;
    profilePictureFile = null;
    profilePictureName = '';
  }

  async function saveProfile() {
    if (profilePictureFile) {
      await upload('/api/me/profilepicture', profilePictureFile[0]);
    }

    await put('/api/me', { email, artistName, aboutMe: aboutMeTiny.getContent() });
    artist = await get('/api/me');
    discardProfile();
    dispatch('update-me');
  }

  function cancelPasswordChange() {
    oldPassword = '';
    newPassword = '';
    newPasswordRepeat = '';
    changePasswordOpen = false;
  }

  async function changePassword() {
    if (oldPassword && newPassword && newPassword === newPasswordRepeat) {
      try {
        await post('/api/account/password', { oldPassword, password: newPassword });
        cancelPasswordChange();
        dispatch('logout');
      } catch (e) {
        if (e.status === 403) {
          await jinyaAlert($_('my_jinya.my_profile.change_password.error.title'), $_('my_jinya.my_profile.change_password.error.forbidden'), $_('alert.dismiss'));
        } else {
          await jinyaAlert($_('my_jinya.my_profile.change_password.error.title'), $_('my_jinya.my_profile.change_password.error.generic'), $_('alert.dismiss'));
        }
      }
    } else if (newPassword !== newPasswordRepeat) {
      await jinyaAlert($_('my_jinya.my_profile.change_password.not_match.title'), $_('my_jinya.my_profile.change_password.not_match.message'), $_('alert.dismiss'));
    }
  }

  onMount(async () => {
    artist = await get('/api/me');
  });
</script>

<div class="jinya-profile-view">
    <div class="cosmo-toolbar jinya-toolbar--media">
        <div class="cosmo-toolbar__group">
            <button on:click={editProfile} class="cosmo-button" disabled={editMode}
                    type="button">{$_('my_jinya.my_profile.action.edit_profile')}</button>
        </div>
        <div class="cosmo-toolbar__group">
            <button class="cosmo-button" on:click={() => changePasswordOpen = true}
                    type="button">{$_('my_jinya.my_profile.action.change_password')}</button>
        </div>
    </div>
    {#if artist}
        <div class="jinya-profile__container">
            {#if editMode}
                <div class="jinya-profile__sidebar">
                    <div class="cosmo-input__group">
                        <label for="artistName" class="cosmo-label">{$_('my_jinya.my_profile.artist_name')}</label>
                        <input required bind:value={artistName} type="text" id="artistName" class="cosmo-input">
                        <label for="email" class="cosmo-label">{$_('my_jinya.my_profile.email')}</label>
                        <input required bind:value={email} type="email" id="email" class="cosmo-input">
                        <label for="profilePictureFile"
                               class="cosmo-label">{$_('my_jinya.my_profile.profile_picture')}</label>
                        <div class="cosmo-input cosmo-input--picker">
                            <label class="cosmo-picker__name" for="profilePictureFile">{profilePictureName}</label>
                            <label class="cosmo-picker__button" for="profilePictureFile"><span
                                    class="mdi mdi-upload mdi-24px"></span></label>
                            <input style="display: none" bind:files={profilePictureFile} type="file"
                                   id="profilePictureFile">
                        </div>
                    </div>
                </div>
                <div class="jinya-profile__about-me" bind:this={aboutMeElement}></div>
                <div class="cosmo-button__container cosmo-button__container--profile">
                    <button on:click={discardProfile}
                            class="cosmo-button">{$_('my_jinya.my_profile.action.discard_profile')}</button>
                    <button on:click={saveProfile}
                            class="cosmo-button">{$_('my_jinya.my_profile.action.save_profile')}</button>
                </div>
            {:else}
                <div class="jinya-profile__sidebar">
                    <img src={`${getHost()}${artist.profilePicture}`} class="jinya-profile__picture"
                         alt={artist.artistName}>
                    <span class="jinya-profile__label">{$_('my_jinya.my_profile.email')}</span>
                    <span class="jinya-profile__detail">{artist.email}</span>
                </div>
                <div class="jinya-profile__about-me">
                    <div class="cosmo-title">{artist.artistName}</div>
                    <div>
                        {@html artist.aboutMe}
                    </div>
                </div>
            {/if}
        </div>
    {/if}
</div>
{#if changePasswordOpen}
    <div class="cosmo-modal__backdrop"></div>
    <div class="cosmo-modal__container">
        <div class="cosmo-modal">
            <h1 class="cosmo-modal__title">{$_('my_jinya.my_profile.change_password.title')}</h1>
            <div class="cosmo-modal__content">
                <div class="cosmo-input__group">
                    <label for="oldPassword"
                           class="cosmo-label">{$_('my_jinya.my_profile.change_password.old_password')}</label>
                    <input required bind:value={oldPassword} type="password" id="oldPassword" class="cosmo-input">
                    <label for="newPassword"
                           class="cosmo-label">{$_('my_jinya.my_profile.change_password.new_password')}</label>
                    <input required bind:value={newPassword} type="password" id="newPassword" class="cosmo-input">
                    <label for="newPasswordRepeat"
                           class="cosmo-label">{$_('my_jinya.my_profile.change_password.new_password_repeat')}</label>
                    <input required bind:value={newPasswordRepeat} type="password" id="newPasswordRepeat"
                           class="cosmo-input">
                </div>
            </div>
            <div class="cosmo-modal__button-bar">
                <button class="cosmo-button"
                        on:click={cancelPasswordChange}>{$_('my_jinya.my_profile.change_password.keep')}</button>
                <button class="cosmo-button"
                        on:click={changePassword}>{$_('my_jinya.my_profile.change_password.change')}</button>
            </div>
        </div>
    </div>
{/if}
