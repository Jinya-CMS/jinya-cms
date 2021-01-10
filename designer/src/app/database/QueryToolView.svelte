<script>
  import { _ } from 'svelte-i18n';
  import { editor } from 'monaco-editor';
  import { post } from '../../http/request';
  import { jinyaAlert } from '../../ui/alert';

  let codeEditor;
  let codeEditorElement;
  let queryResult = [];
  let activeQuery;
  let activeResult;

  $: if (codeEditorElement instanceof HTMLElement) {
    codeEditor = editor.create(codeEditorElement, {
      language: 'mysql',
    });
  }

  function selectQuery(query) {
    activeQuery = query;
    activeResult = queryResult.find(item => item.statement === query);
  }

  async function execute() {
    const query = codeEditor.getValue();
    try {
      queryResult = await post('/api/maintenance/database/query', { query });
      if (queryResult.length > 0) {
        selectQuery(queryResult[0].statement);
      }
    } catch (e) {
      await jinyaAlert($_('database.query_tool.error.title'), e.message, $_('alert.dismiss'));
    }
  }
</script>

<div class="jinya-horizontal-split">
    <div class="jinya-code-editor__container">
        <div class="jinya-code-editor" bind:this={codeEditorElement}></div>
        <div class="cosmo-button__container jinya-code-editor__execute">
            <button on:click={execute} class="cosmo-button">{$_('database.query_tool.execute')}</button>
        </div>
    </div>
    <div class="jinya-code-editor__result">
        <div class="cosmo-tab-control cosmo-tab-control--query-tool">
            <div class="cosmo-tab-control__tabs">
                {#each queryResult as result (result.statement)}
                    <a on:click={() => selectQuery(result.statement)}
                       class:cosmo-tab-control__tab-link--active={result.statement === activeQuery}
                       class="cosmo-tab-control__tab-link">{result.statement}</a>
                {/each}
            </div>
            {#if activeQuery}
                <div class="cosmo-tab-control__content">
                    {#if activeResult}
                        {#if activeResult.result.length === 0}
                            {$_('database.query_tool.no_result')}
                        {:else if activeResult.result.length > 0}
                            <table class="cosmo-table">
                                <thead>
                                <tr>
                                    {#each Object.keys(activeResult.result[0]) as key (key)}
                                        <th>{key}</th>
                                    {/each}
                                </tr>
                                </thead>
                                <tbody>
                                {#each activeResult.result as row (row)}
                                    <tr>
                                        {#each Object.keys(activeResult.result[0]) as key (key)}
                                            <td>{row[key]}</td>
                                        {/each}
                                    </tr>
                                {/each}
                                </tbody>
                            </table>
                        {:else if !isNaN(activeResult.result)}
                            {$_('database.query_tool.rows_affected', { values: { count: activeResult.result } })}
                        {/if}
                    {/if}
                </div>
            {/if}
        </div>
    </div>
</div>
