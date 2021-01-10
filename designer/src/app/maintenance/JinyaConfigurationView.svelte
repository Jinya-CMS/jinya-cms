<script>
  import { _ } from 'svelte-i18n';
  import { onMount } from 'svelte';
  import { get } from '../../http/request';

  let config = [];

  onMount(async () => {
    config = await get('/api/environment');
  });
</script>

<table class="cosmo-table">
    <thead>
    <tr>
        <th>{$_('maintenance.configuration.key')}</th>
        <th>{$_('maintenance.configuration.value')}</th>
    </tr>
    </thead>
    <tbody>
    {#each config as configValue (configValue.key)}
        <tr>
            <td>{configValue.key}</td>
            <td>{configValue.value}</td>
        </tr>
    {/each}
    </tbody>
</table>
