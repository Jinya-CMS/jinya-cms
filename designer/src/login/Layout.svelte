<script>
  import page from 'page';
  import { deleteDeviceCode, getDeviceCode, hasDeviceCode } from '../storage/authentication/storage';
  import RequestSecondFactor from './2fa-flow/RequestSecondFactor.svelte';
  import TwoFaLogin from './2fa-flow/Login.svelte';
  import DeviceCodeLogin from './device-code-flow/Login.svelte';
  import { createEventDispatcher, onMount, tick } from 'svelte';
  import { _ } from 'svelte-i18n';
  import { head } from '../http/request';

  const dispatch = createEventDispatcher();
  let activeRoute;
  let twoFactorApproved = hasDeviceCode();
  let login = false;

  page('/designer/login', async (ctx, next) => {
    try {
      if (hasDeviceCode()) {
        await head(`/api/known_device/${getDeviceCode()}`);
      }
      next();
    } catch (e) {
      twoFactorApproved = false;
      deleteDeviceCode();
      await tick();
      page('/designer/2fa');
    }
  }, () => {
    activeRoute = 'login';
  });
  page('/designer/2fa', () => {
    activeRoute = '2fa';
  });

  onMount(() => {
    page.start();
    if (twoFactorApproved) {
      page('/designer/login');
    } else {
      page('/designer/2fa');
    }
  });
</script>

<main class="cosmo-page-layout">
    <div class="cosmo-top-bar">
        <div class="cosmo-profile-picture"></div>
    </div>
    <div class="cosmo-menu-bar">
        <div class="cosmo-menu-bar__touch"></div>
        <button disabled type="button" class="cosmo-menu-bar__back-button"></button>
        <nav class="cosmo-menu-bar__menu-collection">
            <div class="cosmo-menu-bar__main-menu">
                <span class="cosmo-menu-bar__main-item cosmo-menu-bar__main-item--active">{$_('login.menu.title')}</span>
            </div>
            <div class="cosmo-menu-bar__sub-menu">
                <span class="cosmo-menu-bar__sub-item"
                      class:cosmo-menu-bar__sub-item--active={activeRoute === '2fa'}>{$_('login.menu.request_second_factor')}</span>
                <span class="cosmo-menu-bar__sub-item"
                      class:cosmo-menu-bar__sub-item--active={activeRoute === 'login'}>{$_('login.menu.login')}</span>
            </div>
        </nav>
    </div>
    <div class="cosmo-page-body jinya-page-content--login">
        {#if twoFactorApproved}
            <DeviceCodeLogin on:authenticated={() => dispatch('authenticated')} />
        {:else}
            {#if activeRoute === '2fa'}
                <RequestSecondFactor />
            {:else}
                <TwoFaLogin on:authenticated={() => dispatch('authenticated')} />
            {/if}
        {/if}
    </div>
</main>
