<script>
  import page from 'page';
  import { post } from '../../http/request';
  import { _ } from 'svelte-i18n';
  import { setEmail, setPassword } from '../../storage/authentication/storage';
  import { jinyaAlert } from '../../ui/alert';

  let username;
  let password;
  let form;

  async function getSecondFactor() {
    try {
      if (form.checkValidity()) {
        await post('/api/2fa', {
          username,
          password,
        });
        setEmail(username);
        setPassword(password);
        page('/designer/login');
      }
    } catch (e) {
      jinyaAlert($_('login.error.login_failed.title'), $_('login.error.login_failed.message'), $_('alert.dismiss'));
    }
  }
</script>

<form bind:this={form}>
    <div class="jinya-input__group">
        <label for="email" class="jinya-label">{$_('login.2fa_flow.label.email')}</label>
        <input required bind:value={username} type="email" id="email" class="jinya-input">
        <label for="password" class="jinya-label">{$_('login.2fa_flow.label.password')}</label>
        <input required bind:value={password} type="password" id="password" class="jinya-input">
    </div>
    <div class="jinya-button__container">
        <button on:click={getSecondFactor} class="jinya-button"
                type="button">{$_('login.2fa_flow.action.request')}</button>
    </div>
</form>
