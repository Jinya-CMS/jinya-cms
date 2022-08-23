import { head } from './foundation/http/request.js';
import JinyaLayout from './foundation/JinyaLayout.js';
import { navigate } from './foundation/navigation/navigator.js';
import urlSplitter from './foundation/navigation/urlSplitter.js';
import { deleteJinyaApiKey, getDeviceCode } from './foundation/storage.js';
import { destroyTiny } from './foundation/ui/tiny.js';

const layout = new JinyaLayout();

async function renderLogin(redirect) {
  const deviceCode = getDeviceCode();
  const { default: LoginLayoutPage } = await import('./login/LoginLayout.js');
  const loginLayout = new LoginLayoutPage({ isLogin: true, isTwoFa: false });
  try {
    await head(`/api/known_device/${deviceCode}`);
    const { default: LoginPage } = await import('./login/LoginPage.js');
    const login = new LoginPage({ loginLayout, isTwoFa: false, redirect });
    login.display();
  } catch (e) {
    const { default: LoginPage } = await import('./login/LoginPage.js');
    const login = new LoginPage({ loginLayout, isTwoFa: true, redirect });
    login.display();
  }
}

/**
 * @return {Promise<void>}
 */
async function hashChanged({ redirect = 'front/statistics/matomo-stats' }) {
  destroyTiny();
  if (window.location.hash === '#login') {
    document.querySelector('.cosmo-menu-bar__back-button')?.setAttribute('disabled', 'disabled');
    await renderLogin(redirect);
  } else {
    document.querySelector('.cosmo-menu-bar__back-button')?.removeAttribute('disabled');
    const split = urlSplitter();
    await navigate({ layout, ...split });
  }
}

document.addEventListener('DOMContentLoaded', async () => {
  document.addEventListener('logout', () => {
    let redirect = window.location.hash;
    if (window.location.hash === '#login' || window.location.hash === '') {
      redirect = 'front/statistics/matomo-stats';
    }
    window.location.hash = '#login';
    hashChanged({ redirect });
  });
  try {
    await head('/api/login');
    window.addEventListener('hashchange', hashChanged);

    await hashChanged();
  } catch (e) {
    let redirect = window.location.hash;
    if (window.location.hash === '#login' || window.location.hash === '') {
      redirect = 'front/statistics/matomo-stats';
    }
    window.location.hash = '#login';
    deleteJinyaApiKey();
    window.addEventListener('hashchange', hashChanged);
    await renderLogin(redirect);
  }
});
