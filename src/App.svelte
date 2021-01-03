<script>
  import LoginLayout from './login/Layout.svelte';
  import AppLayout from './app/Layout.svelte';
  import { hasJinyaApiKey } from './storage/authentication/storage';

  let authenticated = hasJinyaApiKey();
</script>

{#if authenticated}
    <AppLayout on:logout={() => location.reload()} />
{:else}
    <LoginLayout on:authenticated={() => location.reload()} />
{/if}
