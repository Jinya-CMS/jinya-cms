<script>
  import { _ } from 'svelte-i18n';
  import { onMount } from 'svelte';
  import { get } from '../../http/request';

  let databaseInfo = null;
  let selectedTable = '';
  let activeTab = 'details';

  function selectTable(table) {
    selectedTable = table;
    activeTab = 'details';
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
            <div class="cosmo-tab-control">
                <div class="cosmo-tab-control__tabs">
                    <a on:click={() => activeTab = 'details'}
                       class:cosmo-tab-control__tab-link--active={activeTab === 'details'}
                       class="cosmo-tab-control__tab-link">{$_('database.details')}</a>
                    <a on:click={() => activeTab = 'structure'}
                       class:cosmo-tab-control__tab-link--active={activeTab === 'structure'}
                       class="cosmo-tab-control__tab-link">{$_('database.structure')}</a>
                    <a on:click={() => activeTab = 'constraints'}
                       class:cosmo-tab-control__tab-link--active={activeTab === 'constraints'}
                       class="cosmo-tab-control__tab-link">{$_('database.constraints.title')}</a>
                    <a on:click={() => activeTab = 'indexes'}
                       class:cosmo-tab-control__tab-link--active={activeTab === 'indexes'}
                       class="cosmo-tab-control__tab-link">{$_('database.indexes.title')}</a>
                </div>
                {#if selectedTable !== ''}
                    {#if activeTab === 'details'}
                        <div class="cosmo-tab-control__content">
                            <span class="cosmo-title">{$_('database.details')}</span>
                            <dl class="cosmo-key-value-list">
                                <dt class="cosmo-key-value-list__key">{$_('database.entryCount')}</dt>
                                <dd class="cosmo-key-value-list__value">{databaseInfo.tables[selectedTable].entryCount}</dd>
                                <dt class="cosmo-key-value-list__key">{$_('database.engine')}</dt>
                                <dd class="cosmo-key-value-list__value">{databaseInfo.tables[selectedTable].engine}</dd>
                                <dt class="cosmo-key-value-list__key">{$_('database.size')}</dt>
                                <dd class="cosmo-key-value-list__value">{databaseInfo.tables[selectedTable].size / 1024}
                                    KB
                                </dd>
                            </dl>
                        </div>
                    {:else if activeTab === 'structure'}
                        <div class="cosmo-tab-control__content">
                            <span class="cosmo-title">{$_('database.structure')}</span>
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
                        </div>
                    {:else if activeTab === 'constraints'}
                        <div class="cosmo-tab-control__content">
                            <span class="cosmo-title">{$_('database.constraints.title')}</span>
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
                        </div>
                    {:else if activeTab === 'indexes'}
                        <div class="cosmo-tab-control__content">
                            <span class="cosmo-title">{$_('database.indexes.title')}</span>
                            <table class="cosmo-table">
                                <thead>
                                <tr>
                                    <th>{$_('database.indexes.keyName')}</th>
                                    <th>{$_('database.indexes.columnName')}</th>
                                    <th>{$_('database.indexes.cardinality')}</th>
                                    <th>{$_('database.indexes.indexType')}</th>
                                    <th>{$_('database.indexes.collation')}</th>
                                    <th>{$_('database.indexes.unique')}</th>
                                </tr>
                                </thead>
                                <tbody>
                                {#each databaseInfo.tables[selectedTable].indexes as row (`${row.Key_name}+${row.Column_name}`)}
                                    <tr>
                                        <td>{row.Key_name}</td>
                                        <td>{row.Column_name}</td>
                                        <td>{row.Cardinality}</td>
                                        <td>{row.Index_type}</td>
                                        <td>{row.Collation}</td>
                                        <td>{row.Non_unique === 0}</td>
                                    </tr>
                                {/each}
                                </tbody>
                            </table>
                        </div>
                    {/if}
                {/if}
            </div>
        </div>
    {/if}
</div>
