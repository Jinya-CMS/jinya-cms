<script>
  import { onMount } from 'svelte';
  import { _ } from 'svelte-i18n';
  import { get } from '../../http/request';

  let phpInfo = null;
  let activeExtension = null;
  let activeExtensionData = null;

  function selectExtension(extension) {
    if (extension === null) {
      activeExtension = null;
    } else {
      activeExtension = extension;
      activeExtensionData = phpInfo.extensions[extension];
    }
  }

  onMount(async () => {
    phpInfo = await get('/api/phpinfo/full');
  });
</script>

<div class="cosmo-list">
    {#if phpInfo}
        <nav class="cosmo-list__items">
            <a class:cosmo-list__item--active={null === activeExtension} class="cosmo-list__item"
               on:click={() => selectExtension(null)}>{$_('maintenance.php.system_and_server')}</a>
            {#each Object.keys(phpInfo.extensions) as extensionName (extensionName)}
                <a class:cosmo-list__item--active={extensionName === activeExtension} class="cosmo-list__item"
                   on:click={() => selectExtension(extensionName)}>{extensionName}</a>
            {/each}
        </nav>
        <div class="cosmo-list__content">
            {#if activeExtension === null}
                <span class="cosmo-title">{$_('maintenance.php.system_and_server')}</span>
                <h2>{$_('maintenance.php.about')}</h2>
                <dl class="cosmo-key-value-list">
                    {#each Object.keys(phpInfo.about) as key(key)}
                        <dt class="cosmo-key-value-list__key">{key}</dt>
                        <dd class="cosmo-key-value-list__value">{phpInfo.about[key]}</dd>
                    {/each}
                </dl>
                {#if phpInfo.apache}
                    <h2>{$_('maintenance.php.apache')}</h2>
                    <dl class="cosmo-key-value-list">
                        {#each Object.keys(phpInfo.apache) as key(key)}
                            <dt class="cosmo-key-value-list__key">{key}</dt>
                            <dd class="cosmo-key-value-list__value">{phpInfo.apache[key]}</dd>
                        {/each}
                    </dl>
                {/if}
            {:else}
                <span class="cosmo-title">{activeExtension}</span>
                <dl class="cosmo-key-value-list">
                    {#each Object.keys(activeExtensionData) as key(key)}
                        {#if key !== 'ini'}
                            <dt class="cosmo-key-value-list__key">{key}</dt>
                            <dd class="cosmo-key-value-list__value">{activeExtensionData[key]}</dd>
                        {/if}
                    {/each}
                </dl>
                {#if Object.keys(activeExtensionData.ini).length > 0}
                    <h2>{$_('maintenance.php.extension.ini_values')}</h2>
                    <table class="cosmo-table">
                        <thead>
                        <tr>
                            <th>{$_('maintenance.php.ini.name')}</th>
                            <th>{$_('maintenance.php.ini.value')}</th>
                        </tr>
                        </thead>
                        <tbody>
                        {#each Object.keys(activeExtensionData.ini) as key (key)}
                            <tr>
                                <td>{key}</td>
                                <td>{activeExtensionData.ini[key]}</td>
                            </tr>
                        {/each}
                        </tbody>
                    </table>
                {/if}
            {/if}
        </div>
    {/if}
</div>
