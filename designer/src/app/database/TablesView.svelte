<script>
  import { _ } from 'svelte-i18n';
  import { onMount } from 'svelte';
  import { get } from '../../http/request';

  let databaseInfo = null;
  let selectedTable = '';

  function selectTable(table) {
    selectedTable = table;
  }

  onMount(async () => {
    databaseInfo = await get('/api/maintenance/database/analyze');
    selectedTable = Object.keys(databaseInfo.tables)[0];
  });
</script>

<div class="cosmo-list">
    {#if databaseInfo}
        <nav class="cosmo-list__items">
            {#each Object.keys(databaseInfo.tables) as table}
                <a class:cosmo-list__item--active={table === selectedTable} class="cosmo-list__item"
                   on:click={() => selectTable(table)}>{table}</a>
            {/each}
        </nav>
        <div class="cosmo-list__content">
            {#if selectedTable !== ''}
                <table class="cosmo-table">
                    <thead>
                    <tr>
                        <th>{$_('database.tables.field')}</th>
                        <th>{$_('database.tables.type')}</th>
                        <th>{$_('database.tables.null')}</th>
                        <th>{$_('database.tables.key')}</th>
                        <th>{$_('database.tables.default')}</th>
                        <th>{$_('database.tables.extra')}</th>
                    </tr>
                    </thead>
                    <tbody>
                    {#each databaseInfo.tables[selectedTable] as row (row.Field)}
                        <tr>
                            <td>{row.Field}</td>
                            <td>{row.Type}</td>
                            <td>{row.Null}</td>
                            <td>{row.Key}</td>
                            <td>{row.Default}</td>
                            <td>{row.Extra}</td>
                        </tr>
                    {/each}
                    </tbody>
                </table>
            {/if}
        </div>
    {/if}
</div>
