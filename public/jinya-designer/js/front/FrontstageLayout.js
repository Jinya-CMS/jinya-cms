import html from '../../lib/jinya-html.js';
import { get } from '../foundation/http/request.js';
import JinyaDesignerLayout from '../foundation/JinyaDesignerLayout.js';
import localize from '../foundation/localize.js';
import urlSplitter from '../foundation/navigation/urlSplitter.js';
import { deleteJinyaApiKey, getRoles } from '../foundation/storage.js';

export default class FrontstageLayout extends JinyaDesignerLayout {
  constructor() {
    super();
    this.artist = null;
    const splitUrl = urlSplitter();
    this.activeSection = splitUrl.section;
    this.activePage = splitUrl.page;
  }

  async getTemplate() {
    if (this.artist === null) {
      this.artist = await get('/api/me');
    }

    const roles = getRoles();

    return html`
        <div class="cosmo-top-bar">
            ${roles.includes('ROLE_ADMIN') ? html`
                <div class="cosmo-top-bar__menu">
                    <a href="#back/maintenance/update"
                       class="cosmo-top-bar__menu-item">${localize({ key: 'maintenance.menu.title' })}</a>
                    <a href="#back/database/mysql-info"
                       class="cosmo-top-bar__menu-item">${localize({ key: 'database.menu.title' })}</a>
                    <a href="#back/artists/index"
                       class="cosmo-top-bar__menu-item">${localize({ key: 'artists.menu.title' })}</a>
                </div>` : ''}
            <img src="${this.artist.profilePicture}" class="cosmo-profile-picture" alt="Imanuel Ulbricht">
            <a class="cosmo-top-bar__menu-item jinya-top-bar__menu-item--logout" id="jinya-logout">
                ${localize({ key: 'top_menu.logout' })}
            </a>
        </div>
        <div class="cosmo-menu-bar">
            <div class="cosmo-menu-bar__touch"></div>
            <button type="button" class="cosmo-menu-bar__back-button"></button>
            <nav class="cosmo-menu-bar__menu-collection">
                <div class="cosmo-menu-bar__main-menu">
                    <a href="#front/statistics/matomo"
                       class="cosmo-menu-bar__main-item cosmo-menu-bar__main-item--active">
                        ${localize({ key: 'statistics.menu.title' })}
                    </a>
                    <a href="#front/media/files" class="cosmo-menu-bar__main-item">
                        ${localize({ key: 'media.menu.title' })}
                    </a>
                    <a href="#front/pages-and-forms/simple-pages" class="cosmo-menu-bar__main-item">
                        ${localize({ key: 'pages_and_forms.menu.title' })}
                    </a>
                    <a href="#front/blog/categories" class="cosmo-menu-bar__main-item">
                        ${localize({ key: 'blog.menu.title' })}
                    </a>
                    <a href="#front/design/menus" class="cosmo-menu-bar__main-item">
                        ${localize({ key: 'design.menu.title' })}
                    </a>
                    <a href="#front/my-jinya/my-profile" class="cosmo-menu-bar__main-item">
                        ${localize({ key: 'my_jinya.menu.title' })}
                    </a>
                </div>
                <div class="cosmo-menu-bar__sub-menu">
                    ${() => {
                        switch (this.activeSection) {
                            default:
                                return html`<a href="#front/statistics/matomo"
                                               class="cosmo-menu-bar__sub-item ${this.activePage === 'index' || this.activePage === 'matomo' ? 'cosmo-menu-bar__sub-item--active' : ''}">
                                    ${localize({ key: 'statistics.menu.matomo' })}
                                </a>
                                <a href="#front/statistics/database"
                                   class="cosmo-menu-bar__sub-item ${this.activePage === 'database' ? 'cosmo-menu-bar__sub-item--active' : ''}">${localize({ key: 'statistics.menu.database' })}</a>`;
                        }
                    }}
                </div>
            </nav>
        </div>
        <div class="cosmo-page-body jinya-page-body--app">
            <div class="cosmo-page-body__content"></div>
        </div>
        <div class="cosmo-bottom-bar cosmo-bottom-bar--three-column">
            <div class="cosmo-bottom-bar__item cosmo-bottom-bar__item--right">
                <button class="cosmo-circular-button cosmo-circular-button--large">
                    <span class="mdi mdi-weather-night"></span>
                </button>
            </div>
        </div>`;
  }

  bindEvents() {
    super.bindEvents();
    document.getElementById('jinya-logout').addEventListener('click', (e) => {
      e.preventDefault();
      deleteJinyaApiKey();
      window.location.hash = 'login';
    });
    document.querySelector('.cosmo-menu-bar__back-button').addEventListener('click', (e) => {
      e.preventDefault();
      window.history.back();
      if (window.history.length === 0) {
        e.target.setAttribute('disabled', 'disabled');
      }
    });
  }
}
