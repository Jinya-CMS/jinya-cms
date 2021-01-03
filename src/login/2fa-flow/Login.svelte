<script>
  import page from 'page';
  import { post } from '../../http/request';
  import {
    deleteEmail,
    deletePassword,
    getEmail,
    getPassword,
    setDeviceCode,
    setJinyaApiKey
  } from '../../storage/authentication/storage';
  import { createEventDispatcher } from 'svelte';

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
        deleteEmail();
        deletePassword();
        dispatch('authenticated');
        page.stop();
      }
    } catch (e) {
      alert(e.message);
    }
  }
</script>

<form bind:this={form}>
    <div class="jinya-input__group">
        <label for="twoFactorCode" class="jinya-label">Two factor code</label>
        <input required maxlength="6" minlength="6" bind:value={twoFactorCode} type="number" id="twoFactorCode"
               class="jinya-input">
    </div>
    <div class="jinya-button__container">
        <button on:click={login} class="jinya-button" type="button">Request second factor</button>
    </div>
</form>
