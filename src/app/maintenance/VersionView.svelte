<script>
  import { _ } from 'svelte-i18n';
  import { onMount } from 'svelte';
  import { get, put } from '../../http/request';

  let version = null;

  async function update() {
    await put('/api/update');
    location.href = '/update';
  }

  onMount(async () => {
    version = await get('/api/version');
  });
</script>

{#if version}
    {#if version.currentVersion !== version.mostRecentVersion}
        <div class="jinya-update__text">
            {@html $_('maintenance.update.version_text', { values: { ...version, openB: '<b>', closeB: '</b>' } })}
        </div>
        <button on:click={update} class="cosmo-button">{$_('maintenance.update.update_now')}</button>
    {:else}
        <div class="jinya-update__text">
            {@html $_('maintenance.update.version_text_no_update', {
              values: {
                ...version,
                openB: '<b>',
                closeB: '</b>',
              },
            })}
        </div>
    {/if}
{/if}
