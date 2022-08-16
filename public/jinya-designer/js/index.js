import { head } from './foundation/http/request.js';
import { navigateFrontstage } from './foundation/navigation/navigator.js';
import urlSplitter from './foundation/navigation/urlSplitter.js';
import { deleteJinyaApiKey, getDeviceCode } from './foundation/storage.js';

async function renderLogin() {
  const deviceCode = getDeviceCode();
  const { default: LoginLayoutPage } = await import('./login/LoginLayout.js');
  const loginLayout = new LoginLayoutPage({ isLogin: true, isTwoFa: false });
  try {
    await head(`/api/known_device/${deviceCode}`);
    const { default: LoginPage } = await import('./login/LoginPage.js');
    const login = new LoginPage({ loginLayout, isTwoFa: false });
    login.display();
  } catch (e) {
    const { default: LoginPage } = await import('./login/LoginPage.js');
    const login = new LoginPage({ loginLayout, isTwoFa: true });
    login.display();
  }
}

/**
 * @return {Promise<void>}
 */
async function hashChanged() {
  if (window.location.hash === '#login') {
    document.querySelector('.cosmo-menu-bar__back-button')?.setAttribute('disabled', 'disabled');
    await renderLogin();
  } else {
    document.querySelector('.cosmo-menu-bar__back-button')?.removeAttribute('disabled');
    const split = urlSplitter();
    if (split.stage === 'front') {
      const { default: FrontstageLayout } = await import('./front/FrontstageLayout.js');
      const layout = new FrontstageLayout();
      await navigateFrontstage({ layout, ...split });
    }
  }
}

document.addEventListener('DOMContentLoaded', async () => {
  try {
    await head('/api/login');
    window.addEventListener('hashchange', hashChanged);

    await hashChanged();
  } catch (e) {
    window.location.hash = '';
    deleteJinyaApiKey();
    window.addEventListener('hashchange', hashChanged);
    await renderLogin();
  }
});
