<script>
  import page from 'page';
  import { post } from '../../http/request';
  import {
    deleteEmail,
    deletePassword,
    getEmail,
    getPassword,
    setDeviceCode,
    setJinyaApiKey,
    setRoles
  } from '../../storage/authentication/storage';
  import { createEventDispatcher } from 'svelte';
  import { _ } from 'svelte-i18n';
  import { jinyaAlert } from '../../ui/alert';

  const dispatch = createEventDispatcher();
  let twoFactorCode;
  let form;

  async function login() {
    try {
      if (form.checkValidity()) {
        const response = await post('/api/login', {
          username: getEmail(),
          password: getPassword(),
          twoFactorCode,
        });
        setDeviceCode(response.deviceCode);
        setJinyaApiKey(response.apiKey);
        setRoles(response.roles);
        deleteEmail();
        deletePassword();
        dispatch('authenticated');
      }
    } catch (e) {
      jinyaAlert($_('login.error.2fa_failed.title'), $_('login.error.2fa_failed.message'), $_('alert.dismiss'));
    }
  }
</script>

<form bind:this={form}>
    <div class="cosmo-input__group">
        <label for="twoFactorCode" class="cosmo-label">{$_('login.2fa_flow.label.two_factor_code')}</label>
        <input required maxlength="6" minlength="6" bind:value={twoFactorCode} type="text" pattern="\d.*"
               id="twoFactorCode"
               class="cosmo-input">
    </div>
    <div class="cosmo-button__container">
        <button on:click={login} class="cosmo-button" type="button">{$_('login.2fa_flow.action.login')}</button>
    </div>
</form>
