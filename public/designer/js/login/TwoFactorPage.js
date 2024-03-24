import html from '../../lib/jinya-html.js';
import { post } from '../foundation/http/request.js';
import JinyaDesignerPage from '../foundation/JinyaDesignerPage.js';
import localize from '../foundation/localize.js';
import { setDeviceCode, setJinyaApiKey, setRoles } from '../foundation/storage.js';
import alert from '../foundation/ui/alert.js';

export default class TwoFactorPage extends JinyaDesignerPage {
  /**
   * @param loginLayout {JinyaDesignerLayout}
   * @param username {string}
   * @param password {string}
   * @param redirect {string}
   */
  constructor({
                loginLayout,
                username,
                password,
                redirect,
              }) {
    super({ layout: loginLayout });
    this.layout.isTwoFa = false;
    this.layout.isLogin = true;
    this.username = username;
    this.password = password;
    this.redirect = redirect;
  }

  bindEvents() {
    super.bindEvents();
    document.getElementById('jinya-2fa-form')
      .addEventListener('submit', async (e) => {
        e.preventDefault();
        if (e.target.checkValidity()) {
          try {
            const response = await post('/api/login', {
              username: this.username,
              password: this.password,
              twoFactorCode: document.getElementById('2fa').value,
            });
            setDeviceCode(response.deviceCode);
            setJinyaApiKey(response.apiKey);
            setRoles(response.roles);
            window.location.hash = this.redirect;
          } catch (err) {
            await alert({
              title: localize({ key: 'login.error.2fa_failed.title' }),
              message: localize({ key: 'login.error.2fa_failed.message' }),
            });
          }
        }
      });
  }

  // eslint-disable-next-line class-methods-use-this
  toString() {
    return html`
      <form id="jinya-2fa-form">
        <div class="cosmo-input__group">
          <label for="2fa" class="cosmo-label">${localize({ key: 'login.page.label.two_factor_code' })}</label>
          <input maxlength="6" minlength="6" required="" type="text" id="2fa" class="cosmo-input" />
        </div>
        <div class="cosmo-button__container">
          <button class="cosmo-button is--primary" type="submit">${localize({ key: 'login.page.action.login' })}
          </button>
        </div>
      </form>`;
  }
}
