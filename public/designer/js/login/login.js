import { Alpine } from '../../../lib/alpine.js';
import { checkKnownDevice, login, requestTwoFactor } from '../foundation/api/authentication.js';
import { deleteRedirect, getRedirect } from '../foundation/utils/storage.js';
import localize from '../foundation/utils/localize.js';
import { getMyProfile } from '../foundation/api/my-jinya.js';

Alpine.store('authentication').logout();
Alpine.data('loginData', () => ({
  email: '',
  password: '',
  twoFactorCode: '',
  errorMessage: null,
  twoFactorCodeRequested: false,
  needsTwoFactor: true,
  async init() {
    const deviceCode = document.cookie
      .split(';')
      .filter((cookie) => cookie.trim().startsWith('JinyaDeviceCode='))
      .map((cookie) => cookie.split('=')[1])
      .shift();
    if (deviceCode) {
      this.needsTwoFactor = !(await checkKnownDevice(deviceCode));
    } else {
      this.needsTwoFactor = true;
    }
  },
  async requestTwoFactorCode() {
    try {
      this.errorMessage = null;
      await requestTwoFactor(this.email, this.password);
      this.twoFactorCodeRequested = true;
    } catch (e) {
      console.error(e);
      this.errorMessage = localize({ key: 'login.error.login_failed.message' });
    }
  },
  async login() {
    try {
      this.errorMessage = null;
      await login(this.email, this.password, this.twoFactorCode);
      const myProfile = await getMyProfile();
      Alpine.store('authentication').login({
        loggedIn: true,
        roles: myProfile.roles,
      });
      Alpine.store('artist').setArtist(myProfile);
      window.PineconeRouter.context.navigate(getRedirect() ?? '/');
      deleteRedirect();
    } catch (e) {
      this.errorMessage = localize({ key: 'login.error.login_failed.message' });
    }
  },
}));
