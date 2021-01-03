<script>
  import { post } from '../../http/request';
  import { deleteEmail, setDeviceCode, setJinyaApiKey, setRoles } from '../../storage/authentication/storage';
  import { createEventDispatcher } from 'svelte';
  import { jinyaAlert } from '../../ui/alert';
  import { _ } from 'svelte-i18n';

  const dispatch = createEventDispatcher();
  let username;
  let password;
  let form;

  async function login() {
    try {
      if (form.checkValidity()) {
        const response = await post('/api/login', {
          username,
          password,
        });
        setDeviceCode(response.deviceCode);
        setJinyaApiKey(response.apiKey);
        setRoles(response.roles);
        dispatch('authenticated');
      }
    } catch (e) {
      jinyaAlert($_('login.error.login_failed.title'), $_('login.error.login_failed.message'), $_('alert.dismiss'));
    }
  }
</script>

<form bind:this={form}>
    <div class="jinya-input__group">
        <label for="email" class="jinya-label">{$_('login.device_code_flow.label.email')}</label>
        <input required bind:value={username} type="email" id="email" class="jinya-input">
        <label for="password" class="jinya-label">{$_('login.device_code_flow.label.password')}</label>
        <input required bind:value={password} type="password" id="password" class="jinya-input">
    </div>
    <div class="jinya-button__container">
        <button on:click={login} class="jinya-button" type="button">{$_('login.device_code_flow.action.login')}</button>
    </div>
</form>
