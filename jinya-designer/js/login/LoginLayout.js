import html from '../../lib/jinya-html.js';
import JinyaDesignerPage from '../foundation/JinyaDesignerPage.js';
import localize from '../foundation/localize.js';

export default class LoginLayout extends JinyaDesignerPage {
  /**
   * @param childPage {JinyaDesignerPage}
   * @param isLogin {boolean}
   * @param isTwoFa {boolean}
   */
  constructor({ childPage, isLogin, isTwoFa }) {
    super();
    this.childPage = childPage;
    this.isLogin = isLogin;
    this.isTwoFa = isTwoFa;
  }

  bindEvents() {
    super.bindEvents();
    this.childPage.bindEvents();
  }

  /**
   * Renders the login page layout used for login and two factor verification
   * @return {string}
   */
  renderToString() {
    return html`
        <div class="cosmo-top-bar">
            <div class="cosmo-profile-picture"></div>
        </div>
        <div class="cosmo-menu-bar">
            <div class="cosmo-menu-bar__touch"></div>
            <button disabled="" type="button" class="cosmo-menu-bar__back-button"></button>
            <nav class="cosmo-menu-bar__menu-collection">
                <div class="cosmo-menu-bar__main-menu">
                    <span class="cosmo-menu-bar__main-item cosmo-menu-bar__main-item--active">${localize({ key: 'login.menu.title' })}</span>
                </div>
                <div class="cosmo-menu-bar__sub-menu">
                    <span class="cosmo-menu-bar__sub-item ${this.isTwoFa ? 'cosmo-menu-bar__sub-item--active' : ''}">${localize({ key: 'login.menu.request_second_factor' })}</span>
                    <span class="cosmo-menu-bar__sub-item ${this.isLogin ? 'cosmo-menu-bar__sub-item--active' : ''}">${localize({ key: 'login.menu.login' })}</span>
                </div>
            </nav>
        </div>
        <div class="cosmo-page-body jinya-page-content--login">
            ${this.childPage.renderToString()}
        </div>`;
  }
}
