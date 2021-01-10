<script>
  import { getJinyaApiKey } from '../../storage/authentication/storage';
  import { _, date, time } from 'svelte-i18n';
  import { onMount } from 'svelte';
  import { get, httpDelete } from '../../http/request';
  import UAParser from 'ua-parser-js';

  let devices = [];

  function getBrowser(ua) {
    const parser = new UAParser(ua);
    return $_('my_jinya.devices.browser', {
      values: {
        browser: parser.getBrowser().name,
        os: parser.getOS().name,
      },
    });
  }

  function getDevice(ua) {
    const parser = new UAParser(ua);
    if (parser.getDevice().vendor) {
      return $_('my_jinya.devices.device', {
        values: {
          vendor: parser.getDevice().vendor,
          model: parser.getDevice().model,
        },
      });
    }

    return $_('my_jinya.devices.unknown_device');
  }

  function locateIp(ip) {
    return get(`/api/ip-location/${ip}`);
  }

  async function loadDevices() {
    const result = await get('/api/known_device');
    devices = result.items.map(item => ({ ...item, validSince: Date.parse(item.validSince) }));
  }

  async function forget(key) {
    await httpDelete(`/api/known_device/${key}`);
    await loadDevices();
  }

  onMount(async () => {
    await loadDevices();
  });
</script>

<table class="cosmo-table">
    <thead>
    <tr>
        <th>{$_('my_jinya.devices.table.browser')}</th>
        <th>{$_('my_jinya.devices.table.device')}</th>
        <th>{$_('my_jinya.devices.table.place')}</th>
        <th>{$_('my_jinya.devices.table.ip')}</th>
        <th>{$_('my_jinya.devices.table.actions')}</th>
    </tr>
    </thead>
    <tbody>
    {#each devices as device (device.key)}
        <tr>
            <td>{getBrowser(device.userAgent)}</td>
            <td>{getDevice(device.userAgent)}</td>
            <td>
                {#await locateIp(device.remoteAddress)}
                    {$_('my_jinya.devices.locating')}
                {:then result}
                    {#if result.city}
                        {result.city}, {result.region}, {result.country}
                    {:else}
                        {$_('my_jinya.devices.unknown')}
                    {/if}
                {/await}
            </td>
            <td>{device.remoteAddress}</td>
            <td>
                <button disabled={device.key === getJinyaApiKey()} on:click={() => forget(device.key)}
                        class="cosmo-button">{$_('my_jinya.devices.action.forget')}</button>
            </td>
        </tr>
    {/each}
    </tbody>
</table>
