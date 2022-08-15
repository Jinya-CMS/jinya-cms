import html from '../../lib/jinya-html.js';
import { post } from '../foundation/http/request.js';
import JinyaDesignerPage from '../foundation/JinyaDesignerPage.js';
import localize from '../foundation/localize.js';
import { setDeviceCode, setJinyaApiKey, setRoles } from '../foundation/storage.js';
import alert from '../foundation/ui/alert.js';
import LoginLayout from './LoginLayout.js';

export default class LoginPage extends JinyaDesignerPage {
  /**
   * @param isTwoFa {boolean}
   */
  constructor({ isTwoFa }) {
    super();
    this.isTwoFa = isTwoFa;
  }

  bindEvents() {
    super.bindEvents();
    document.getElementById('jinya-login-form').addEventListener('submit', async (e) => {
      e.preventDefault();
      if (e.target.checkValidity()) {
        try {
          const payload = {
            password: document.getElementById('password').value,
            username: document.getElementById('email').value,
          };
          const response = await post(this.isTwoFa ? '/api/2fa' : '/api/login', payload);
          if (this.isTwoFa) {
            const { default: TwoFactorPage } = await import('./TwoFactorPage.js');
            const page = new TwoFactorPage(payload);
            const layout = new LoginLayout({ childPage: page, isLogin: true, isTwoFa: false });
            layout.renderToScreen();
          } else {
            setDeviceCode(response.deviceCode);
            setJinyaApiKey(response.apiKey);
            setRoles(response.roles);
          }
        } catch (err) {
          await alert({
            title: localize({ key: 'login.error.login_failed.title' }),
            message: localize({ key: 'login.error.login_failed.message' }),
          });
        }
      }
    });
  }

  renderToString() {
    return html`
        <form id="jinya-login-form">
            <div class="cosmo-input__group">
                <label for="email" class="cosmo-label">${localize({ key: 'login.page.label.email' })}</label>
                <input required="" type="email" id="email" class="cosmo-input">
                <label for="password" class="cosmo-label">${localize({ key: 'login.page.label.password' })}</label>
                <input required="" type="password" id="password" class="cosmo-input">
            </div>
            <div class="cosmo-button__container">
                <button class="cosmo-button" type="submit">
                    ${!this.isTwoFa ? localize({ key: 'login.page.action.login' }) : localize({ key: 'login.page.action.request' })}
                </button>
            </div>
        </form>`;
  }
}
