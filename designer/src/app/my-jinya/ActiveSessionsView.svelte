<script>
  import { getJinyaApiKey } from '../../storage/authentication/storage';
  import { _, date, time } from 'svelte-i18n';
  import { onMount } from 'svelte';
  import { get, httpDelete } from '../../http/request';
  import UAParser from 'ua-parser-js';

  let sessions = [];

  function getBrowser(ua) {
    const parser = new UAParser(ua);
    return $_('my_jinya.sessions.browser', {
      values: {
        browser: parser.getBrowser().name,
        os: parser.getOS().name,
      },
    });
  }

  function getDevice(ua) {
    const parser = new UAParser(ua);
    if (parser.getDevice().vendor) {
      return $_('my_jinya.sessions.device', {
        values: {
          vendor: parser.getDevice().vendor,
          model: parser.getDevice().model,
        },
      });
    }

    return $_('my_jinya.sessions.unknown_device');
  }

  function locateIp(ip) {
    return get(`/api/ip-location/${ip}`);
  }

  async function loadSessions() {
    const result = await get('/api/api_key');
    sessions = result.items.map(item => ({ ...item, validSince: Date.parse(item.validSince) }));
  }

  async function logout(key) {
    await httpDelete(`/api/api_key/${key}`);
    await loadSessions();
  }

  onMount(async () => {
    await loadSessions();
  });
</script>

<table class="cosmo-table">
    <thead>
    <tr>
        <th>{$_('my_jinya.sessions.table.valid_since')}</th>
        <th>{$_('my_jinya.sessions.table.browser')}</th>
        <th>{$_('my_jinya.sessions.table.device')}</th>
        <th>{$_('my_jinya.sessions.table.place')}</th>
        <th>{$_('my_jinya.sessions.table.ip')}</th>
        <th>{$_('my_jinya.sessions.table.actions')}</th>
    </tr>
    </thead>
    <tbody>
    {#each sessions as session (session.key)}
        <tr>
            <td>{$date(session.validSince)} {$time(session.validSince)}</td>
            <td>{getBrowser(session.userAgent)}</td>
            <td>{getDevice(session.userAgent)}</td>
            <td>
                {#await locateIp(session.remoteAddress)}
                    {$_('my_jinya.sessions.locating')}
                {:then result}
                    {#if result.city}
                        {result.city}, {result.region}, {result.country}
                    {:else}
                        {$_('my_jinya.sessions.unknown')}
                    {/if}
                {/await}
            </td>
            <td>{session.remoteAddress}</td>
            <td>
                <button disabled={session.key === getJinyaApiKey()} on:click={() => logout(session.key)}
                        class="cosmo-button">{$_('my_jinya.sessions.action.logout')}</button>
            </td>
        </tr>
    {/each}
    </tbody>
</table>
