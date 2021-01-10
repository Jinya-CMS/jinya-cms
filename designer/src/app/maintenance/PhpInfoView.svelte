<script>
  import { _ } from 'svelte-i18n';
  import { onMount } from 'svelte';
  import { get } from '../../http/request';

  let phpInfo = null;
  let activeExtension = null;
  let activeIniValues = [];

  function selectExtension(extension) {
    if (extension === '') {
      activeExtension = '';
      activeIniValues = phpInfo.php.iniValues;
    } else if (extension === null) {
      activeExtension = null;
      activeIniValues = [];
    } else {
      activeExtension = extension.name;
      activeIniValues = extension.iniValues;
    }
  }

  onMount(async () => {
    phpInfo = await get('/api/phpinfo');
  });
</script>

<div class="cosmo-list">
    {#if phpInfo}
        <nav class="cosmo-list__items">
            <a class:cosmo-list__item--active={null === activeExtension} class="cosmo-list__item"
               on:click={() => selectExtension(null)}>{$_('maintenance.php.system_and_server')}</a>
            <a class:cosmo-list__item--active={'' === activeExtension} class="cosmo-list__item"
               on:click={() => selectExtension('')}>{$_('maintenance.php.configuration')}</a>
            {#each phpInfo.php.extensions as extension (extension.name)}
                <a class:cosmo-list__item--active={extension.name === activeExtension} class="cosmo-list__item"
                   on:click={() => selectExtension(extension)}>{extension.name}</a>
            {/each}
        </nav>
        <div class="cosmo-list__content">
            {#if activeExtension === null}
                <dl class="cosmo-key-value-list">
                    <dt class="cosmo-key-value-list__key">{$_('maintenance.php.operating_system')}</dt>
                    <dd class="cosmo-key-value-list__value">{phpInfo.system.uname}</dd>
                    <dt class="cosmo-key-value-list__key">{$_('maintenance.php.zend_engine')}</dt>
                    <dd class="cosmo-key-value-list__value">{phpInfo.zend.version}</dd>
                    {#if phpInfo.apache.version}
                        <dt class="cosmo-key-value-list__key">{$_('maintenance.php.apache')}</dt>
                        <dd class="cosmo-key-value-list__value">{phpInfo.apache.version}</dd>
                    {/if}
                    <dt class="cosmo-key-value-list__key">{$_('maintenance.php.php')}</dt>
                    <dd class="cosmo-key-value-list__value">{phpInfo.php.version}</dd>
                </dl>
            {:else}
                <table class="cosmo-table">
                    <thead>
                    <tr>
                        <th>{$_('maintenance.php.ini.name')}</th>
                        <th>{$_('maintenance.php.ini.value')}</th>
                    </tr>
                    </thead>
                    <tbody>
                    {#each activeIniValues as value (value.name)}
                        <tr>
                            <td>{value.name}</td>
                            <td>{value.value}</td>
                        </tr>
                    {/each}
                    </tbody>
                </table>
            {/if}
        </div>
    {/if}
</div>
