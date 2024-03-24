import html from '../../lib/jinya-html.js';
import JinyaDesignerLayout from '../foundation/JinyaDesignerLayout.js';
import localize from '../foundation/localize.js';

export default class LoginLayout extends JinyaDesignerLayout {
  /**
   * @param isLogin {boolean}
   * @param isTwoFa {boolean}
   */
  constructor({
                isLogin,
                isTwoFa,
              }) {
    super();
    this.isLogin = isLogin;
    this.isTwoFa = isTwoFa;
  }

  bindEvents() {
    super.bindEvents();
  }

  async getTemplate() {
    return html`
      <div class="cosmo-menu is--top">
        <div class="cosmo-profile-picture"></div>
      </div>
      <div class="cosmo-menu">
        <button disabled="" type="button" class="cosmo-back-button"></button>
        <nav class="cosmo-menu__collection">
          <div class="cosmo-menu__row is--main">
            <span class="cosmo-menu__item is--active"
            >${localize({ key: 'login.menu.title' })}</span
            >
          </div>
          <div class="cosmo-menu__row is--sub">
            <span class="cosmo-menu__item ${this.isTwoFa ? 'is--active' : ''}"
            >${localize({ key: 'login.menu.request_second_factor' })}</span
            >
            <span class="cosmo-menu__item ${this.isLogin ? 'is--active' : ''}"
            >${localize({ key: 'login.menu.login' })}</span
            >
          </div>
        </nav>
      </div>
      <div class="cosmo-page__body jinya-page-content--login">%%%child%%%</div>`;
  }
}
