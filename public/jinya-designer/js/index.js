import { head } from './foundation/http/request.js';
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

document.addEventListener('DOMContentLoaded', async () => {
  try {
    await head('/api/login');
  } catch (e) {
    deleteJinyaApiKey();
    await renderLogin();
  }
});
