import { head } from './foundation/http/request.js';
import { deleteJinyaApiKey, getDeviceCode } from './foundation/storage.js';

async function renderLogin() {
  const deviceCode = getDeviceCode();
  const { default: LoginLayoutPage } = await import('./login/LoginLayout.js');
  try {
    await head(`/api/known_device/${deviceCode}`);
    const { default: LoginPage } = await import('./login/LoginPage.js');
    const login = new LoginPage({ isTwoFa: false });
    const layout = new LoginLayoutPage({ childPage: login, isLogin: true, isTwoFa: false });
    layout.renderToScreen();
  } catch (e) {
    const { default: LoginPage } = await import('./login/LoginPage.js');
    const login = new LoginPage({ isTwoFa: true });
    const layout = new LoginLayoutPage({ childPage: login, isLogin: false, isTwoFa: true });
    layout.renderToScreen();
  }
}

document.addEventListener('DOMContentLoaded', async () => {
  try {
    await head('/api/login');
  } catch (e) {
    deleteJinyaApiKey();
    await renderLogin();
  }
});
