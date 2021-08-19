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
                <span class="cosmo-title">{selectedTable}</span>
                <h2>{$_('database.details')}</h2>
                <dl class="cosmo-key-value-list">
                    <dt class="cosmo-key-value-list__key">{$_('database.entryCount')}</dt>
                    <dd class="cosmo-key-value-list__value">{databaseInfo.tables[selectedTable].entryCount}</dd>
                    <dt class="cosmo-key-value-list__key">{$_('database.engine')}</dt>
                    <dd class="cosmo-key-value-list__value">{databaseInfo.tables[selectedTable].engine}</dd>
                    <dt class="cosmo-key-value-list__key">{$_('database.size')}</dt>
                    <dd class="cosmo-key-value-list__value">{databaseInfo.tables[selectedTable].size / 1024} KB</dd>
                </dl>
                <h2>{$_('database.structure')}</h2>
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
                    {#each databaseInfo.tables[selectedTable].structure as row (row.Field)}
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
                <h2>{$_('database.constraints.title')}</h2>
                <table class="cosmo-table">
                    <thead>
                    <tr>
                        <th>{$_('database.constraints.constraintName')}</th>
                        <th>{$_('database.constraints.constraintType')}</th>
                        <th>{$_('database.constraints.columnName')}</th>
                        <th>{$_('database.constraints.referencedTableName')}</th>
                        <th>{$_('database.constraints.referencedColumnName')}</th>
                        <th>{$_('database.constraints.positionInUniqueConstraint')}</th>
                        <th>{$_('database.constraints.deleteRule')}</th>
                        <th>{$_('database.constraints.updateRule')}</th>
                    </tr>
                    </thead>
                    <tbody>
                    {#each databaseInfo.tables[selectedTable].constraints as row (`${row.CONSTRAINT_NAME}+${row.COLUMN_NAME}`)}
                        <tr>
                            <td>{row.CONSTRAINT_NAME}</td>
                            <td>{row.CONSTRAINT_TYPE}</td>
                            <td>{row.COLUMN_NAME}</td>
                            <td>{row.REFERENCED_TABLE_NAME ?? $_('database.constraints.none')}</td>
                            <td>{row.REFERENCED_COLUMN_NAME ?? $_('database.constraints.none')}</td>
                            <td>{row.POSITION_IN_UNIQUE_CONSTRAINT ?? $_('database.constraints.none')}</td>
                            <td>{row.DELETE_RULE ?? $_('database.constraints.none')}</td>
                            <td>{row.UPDATE_RULE ?? $_('database.constraints.none')}</td>
                        </tr>
                    {/each}
                    </tbody>
                </table>
            {/if}
        </div>
    {/if}
</div>
