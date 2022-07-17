<script>
  import { onMount } from 'svelte';
  import { _ } from 'svelte-i18n';
  import { get } from '../../http/request';

  let phpInfo = null;
  let activeExtension = null;
  let activeExtensionVersion = '';
  let activeIniValues = [];
  let type = '';
  let additionalData = [];

  function selectExtension(extension) {
    if (extension === null) {
      activeExtension = null;
      activeIniValues = [];
    } else {
      activeExtension = extension.name;
      activeExtensionVersion = extension.version;
      activeIniValues = extension.iniValues;
      type = extension.additionalData.type;
      additionalData = extension.additionalData;
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
                <span class="cosmo-title">{activeExtension}</span>
                <dl class="cosmo-key-value-list">
                    <dt class="cosmo-key-value-list__key">{$_('maintenance.php.extension.version')}</dt>
                    <dd class="cosmo-key-value-list__value">{activeExtensionVersion}</dd>
                </dl>
                {#if activeIniValues.length > 0}
                    <h2>{$_('maintenance.php.extension.ini_values')}</h2>
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
                {#if type === 'apcu'}
                    <h2>{$_('maintenance.php.extension.more_info')}</h2>
                    <dl class="cosmo-key-value-list">
                        <dt class="cosmo-key-value-list__key">{$_('maintenance.php.extension.apcu.enabled')}</dt>
                        <dd class="cosmo-key-value-list__value">{additionalData.enabled}</dd>
                        <dt class="cosmo-key-value-list__key">{$_('maintenance.php.extension.apcu.numberSlots')}</dt>
                        <dd class="cosmo-key-value-list__value">{additionalData.numberSlots}</dd>
                        <dt class="cosmo-key-value-list__key">{$_('maintenance.php.extension.apcu.numberEntries')}</dt>
                        <dd class="cosmo-key-value-list__value">{additionalData.numberEntries}</dd>
                        <dt class="cosmo-key-value-list__key">{$_('maintenance.php.extension.apcu.memorySize')}</dt>
                        <dd class="cosmo-key-value-list__value">{additionalData.memorySize} MB</dd>
                        <dt class="cosmo-key-value-list__key">{$_('maintenance.php.extension.apcu.memoryType')}</dt>
                        <dd class="cosmo-key-value-list__value">{additionalData.memoryType}</dd>
                    </dl>
                {:else if type === 'date'}
                    <h2>{$_('maintenance.php.extension.more_info')}</h2>
                    <dl class="cosmo-key-value-list">
                        <dt class="cosmo-key-value-list__key">{$_('maintenance.php.extension.date.enabled')}</dt>
                        <dd class="cosmo-key-value-list__value">{additionalData.enabled}</dd>
                        <dt class="cosmo-key-value-list__key">{$_('maintenance.php.extension.date.default_timezone')}</dt>
                        <dd class="cosmo-key-value-list__value">{additionalData.defaultTimezone}</dd>
                        <dt class="cosmo-key-value-list__key">{$_('maintenance.php.extension.date.database_version')}</dt>
                        <dd class="cosmo-key-value-list__value">{additionalData.databaseVersion}</dd>
                    </dl>
                {:else if type === 'hash'}
                    <h2>{$_('maintenance.php.extension.more_info')}</h2>
                    <dl class="cosmo-key-value-list">
                        <dt class="cosmo-key-value-list__key">{$_('maintenance.php.extension.hash.enabled')}</dt>
                        <dd class="cosmo-key-value-list__value">{additionalData.enabled}</dd>
                        <dt class="cosmo-key-value-list__key">{$_('maintenance.php.extension.hash.algos')}</dt>
                        <dd class="cosmo-key-value-list__value">{additionalData.algos}</dd>
                    </dl>
                {:else if type === 'imagick'}
                    <h2>{$_('maintenance.php.extension.more_info')}</h2>
                    <dl class="cosmo-key-value-list">
                        <dt class="cosmo-key-value-list__key">{$_('maintenance.php.extension.imagick.enabled')}</dt>
                        <dd class="cosmo-key-value-list__value">{additionalData.enabled}</dd>
                        <dt class="cosmo-key-value-list__key">{$_('maintenance.php.extension.imagick.version')}</dt>
                        <dd class="cosmo-key-value-list__value">{additionalData.version}</dd>
                        <dt class="cosmo-key-value-list__key">{$_('maintenance.php.extension.imagick.copyright')}</dt>
                        <dd class="cosmo-key-value-list__value">{additionalData.copyright}</dd>
                        <dt class="cosmo-key-value-list__key">{$_('maintenance.php.extension.imagick.package')}</dt>
                        <dd class="cosmo-key-value-list__value">{additionalData.package}</dd>
                        <dt class="cosmo-key-value-list__key">{$_('maintenance.php.extension.imagick.release_date')}</dt>
                        <dd class="cosmo-key-value-list__value">{additionalData.releaseDate}</dd>
                    </dl>
                {:else if type === 'intl'}
                    <h2>{$_('maintenance.php.extension.more_info')}</h2>
                    <dl class="cosmo-key-value-list">
                        <dt class="cosmo-key-value-list__key">{$_('maintenance.php.extension.intl.enabled')}</dt>
                        <dd class="cosmo-key-value-list__value">{additionalData.enabled}</dd>
                        <dt class="cosmo-key-value-list__key">{$_('maintenance.php.extension.intl.ids')}</dt>
                        <dd class="cosmo-key-value-list__value">{additionalData.ids}</dd>
                    </dl>
                {:else if type === 'mbstring'}
                    <h2>{$_('maintenance.php.extension.more_info')}</h2>
                    <dl class="cosmo-key-value-list">
                        <dt class="cosmo-key-value-list__key">{$_('maintenance.php.extension.mbstring.enabled')}</dt>
                        <dd class="cosmo-key-value-list__value">{additionalData.enabled}</dd>
                        <dt class="cosmo-key-value-list__key">{$_('maintenance.php.extension.mbstring.encodings')}</dt>
                        <dd class="cosmo-key-value-list__value">{additionalData.encodings}</dd>
                    </dl>
                {:else if type === 'opcache'}
                    <h2>{$_('maintenance.php.extension.more_info')}</h2>
                    <dl class="cosmo-key-value-list">
                        <dt class="cosmo-key-value-list__key">{$_('maintenance.php.extension.opcache.enabled')}</dt>
                        <dd class="cosmo-key-value-list__value">{additionalData.enabled}</dd>
                        <dt class="cosmo-key-value-list__key">{$_('maintenance.php.extension.opcache.full')}</dt>
                        <dd class="cosmo-key-value-list__value">{additionalData.full}</dd>
                        <dt class="cosmo-key-value-list__key">{$_('maintenance.php.extension.opcache.usedMemory')}</dt>
                        <dd class="cosmo-key-value-list__value">{additionalData.usedMemory} MB</dd>
                        <dt class="cosmo-key-value-list__key">{$_('maintenance.php.extension.opcache.freeMemory')}</dt>
                        <dd class="cosmo-key-value-list__value">{additionalData.freeMemory} MB</dd>
                        <dt class="cosmo-key-value-list__key">{$_('maintenance.php.extension.opcache.wastedMemory')}</dt>
                        <dd class="cosmo-key-value-list__value">{additionalData.wastedMemory} MB</dd>
                        <dt class="cosmo-key-value-list__key">{$_('maintenance.php.extension.opcache.currentWastedMemoryPercentage')}</dt>
                        <dd class="cosmo-key-value-list__value">{additionalData.currentWastedMemoryPercentage} %</dd>
                        <dt class="cosmo-key-value-list__key">{$_('maintenance.php.extension.opcache.jitEnabled')}</dt>
                        <dd class="cosmo-key-value-list__value">{additionalData.jitEnabled}</dd>
                    </dl>
                {/if}
            {/if}
        </div>
    {/if}
</div>
