<script>
  import page from 'page';
  import { post } from '../../http/request';
  import { setEmail, setPassword } from '../../storage/authentication/storage';

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
        <button on:click={getSecondFactor} class="jinya-button" type="button">Request second factor</button>
    </div>
</form>
