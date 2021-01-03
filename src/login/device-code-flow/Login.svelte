<script>
  import { post } from '../../http/request';
  import { setDeviceCode, setJinyaApiKey } from '../../storage/authentication/storage';
  import { createEventDispatcher } from 'svelte';

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
        dispatch('authenticated');
      }
    } catch (e) {
      alert(e.message);
    }
  }
</script>

<form bind:this={form}>
    <div class="jinya-input__group">
        <label for="email" class="jinya-label">Email</label>
        <input required bind:value={username} type="email" id="email" class="jinya-input">
        <label for="password" class="jinya-label">Password</label>
        <input required bind:value={password} type="password" id="password" class="jinya-input">
    </div>
    <div class="jinya-button__container">
        <button on:click={login} class="jinya-button" type="button">Login</button>
    </div>
</form>
