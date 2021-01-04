<script>
  import page from 'page';
  import { hasDeviceCode } from '../storage/authentication/storage';
  import RequestSecondFactor from './2fa-flow/RequestSecondFactor.svelte';
  import TwoFaLogin from './2fa-flow/Login.svelte';
  import DeviceCodeLogin from './device-code-flow/Login.svelte';
  import { createEventDispatcher, onMount } from 'svelte';
  import { _ } from 'svelte-i18n';

  const dispatch = createEventDispatcher();
  let activeRoute;
  const twoFactorApproved = hasDeviceCode();

  page('/designer/login', () => {
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

<main class="jinya-page-layout">
    <div class="jinya-top-bar">
        <div class="jinya-profile-picture"></div>
    </div>
    <div class="jinya-menu-bar">
        <div class="jinya-menu-bar__touch"></div>
        <button disabled type="button" class="jinya-menu-bar__back-button"></button>
        <nav class="jinya-menu-bar__menu-collection">
            <div class="jinya-menu-bar__main-menu">
                <span class="jinya-menu-bar__main-item jinya-menu-bar__main-item--active">{$_('login.menu.title')}</span>
            </div>
            <div class="jinya-menu-bar__sub-menu">
                <span class="jinya-menu-bar__sub-item"
                      class:jinya-menu-bar__sub-item--active={activeRoute === '2fa'}>{$_('login.menu.request_second_factor')}</span>
                <span class="jinya-menu-bar__sub-item"
                      class:jinya-menu-bar__sub-item--active={activeRoute === 'login'}>{$_('login.menu.login')}</span>
            </div>
        </nav>
    </div>
    <div class="jinya-page-body jinya-page-content--login">
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
