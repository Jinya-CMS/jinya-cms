<script>
  import LoginLayout from './login/Layout.svelte';
  import AppLayout from './login/Layout.svelte';
  import { hasJinyaApiKey } from './storage/authentication/storage';

  const authenticated = hasJinyaApiKey();
</script>

{#if authenticated}
    <AppLayout />
{:else}
    <LoginLayout />
{/if}
