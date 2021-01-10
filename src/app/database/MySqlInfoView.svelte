<script>
  import { _ } from 'svelte-i18n';
  import { onMount } from 'svelte';
  import { get } from '../../http/request';

  let databaseInfo = null;
  let selectedTab = 'server';
  let selectedVariables = [];

  function selectTab(tab) {
    selectedTab = tab;
    if (tab === 'local') {
      selectedVariables = databaseInfo.variables.local;
    } else if (tab === 'global') {
      selectedVariables = databaseInfo.variables.global;
    } else if (tab === 'session') {
      selectedVariables = databaseInfo.variables.session;
    }
  }

  onMount(async () => {
    databaseInfo = await get('/api/maintenance/database/analyze');
  });
</script>

<div class="cosmo-list">
    {#if databaseInfo}
        <nav class="cosmo-list__items">
            <a class:cosmo-list__item--active={'server' === selectedTab} class="cosmo-list__item"
               on:click={() => selectTab('server')}>{$_('database.mysql.system_and_server')}</a>
            <a class:cosmo-list__item--active={'global' === selectedTab} class="cosmo-list__item"
               on:click={() => selectTab('global')}>{$_('database.mysql.global_variables')}</a>
            <a class:cosmo-list__item--active={'local' === selectedTab} class="cosmo-list__item"
               on:click={() => selectTab('local')}>{$_('database.mysql.local_variables')}</a>
            <a class:cosmo-list__item--active={'session' === selectedTab} class="cosmo-list__item"
               on:click={() => selectTab('session')}>{$_('database.mysql.session_variables')}</a>
        </nav>
        <div class="cosmo-list__content">
            {#if 'server' === selectedTab}
                <dl class="cosmo-key-value-list">
                    <dt class="cosmo-key-value-list__key">{$_('database.mysql.server_type')}</dt>
                    <dd class="cosmo-key-value-list__value">{databaseInfo.server.comment}</dd>
                    <dt class="cosmo-key-value-list__key">{$_('database.mysql.server_version')}</dt>
                    <dd class="cosmo-key-value-list__value">{databaseInfo.server.version}</dd>
                    <dt class="cosmo-key-value-list__key">{$_('database.mysql.compile_machine')}</dt>
                    <dd class="cosmo-key-value-list__value">{databaseInfo.server.compileMachine}</dd>
                    <dt class="cosmo-key-value-list__key">{$_('database.mysql.compile_os')}</dt>
                    <dd class="cosmo-key-value-list__value">{databaseInfo.server.compileOs}</dd>
                </dl>
            {:else}
                <table class="cosmo-table">
                    <thead>
                    <tr>
                        <th>{$_('database.mysql.name')}</th>
                        <th>{$_('database.mysql.value')}</th>
                    </tr>
                    </thead>
                    <tbody>
                    {#each Object.keys(selectedVariables) as variable (variable)}
                        <tr>
                            <td>{variable}</td>
                            <td>{selectedVariables[variable]}</td>
                        </tr>
                    {/each}
                    </tbody>
                </table>
            {/if}
        </div>
    {/if}
</div>
