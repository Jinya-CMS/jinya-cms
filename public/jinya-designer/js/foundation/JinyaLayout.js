import html from '../../lib/jinya-html.js';
import FileUploadedEvent from '../front/media/files/FileUploadedEvent.js';
import { get, put } from './http/request.js';
import JinyaDesignerLayout from './JinyaDesignerLayout.js';
import localize from './localize.js';
import urlSplitter from './navigation/urlSplitter.js';
import { deleteJinyaApiKey, getJinyaApiKey, getRoles } from './storage.js';

export default class JinyaLayout extends JinyaDesignerLayout {
  constructor() {
    super();
    this.artist = null;
    this.filesToUpload = 0;
    this.filesUploaded = 0;
    this.fileUploadWorker = new Worker('./js/front/media/files/UploadWorker.js');
    this.fileUploadWorker.addEventListener('message', (e) => {
      const { type, file } = e.data;
      this.bumpUploadCounter();
      if (type === 'file-uploaded') {
        document.dispatchEvent(new FileUploadedEvent({ file }));
      }
    });
    document.addEventListener('filesSelected', ({ files }) => {
      this.filesToUpload += files.length;
      document.getElementById('file-upload-progress').max = this.filesToUpload;
      document.getElementById('file-upload-progress-bottom-label').innerText = localize({
        key: 'bottom_bar.upload_progress',
        values: {
          filesToUpload: this.filesToUpload,
          filesUploaded: this.filesUploaded,
        },
      });
      this.fileUploadWorker.postMessage({ files, apiKey: getJinyaApiKey() });
      document.querySelector('.cosmo-bottom-bar__item--center.jinya-progress').removeAttribute('style');
    });
  }

  getThemeMode() {
    switch (this.artist.colorScheme) {
      case 'light':
        return 'mdi-weather-sunny';
      case 'dark':
        return 'mdi-weather-night';
      default:
        return 'mdi-theme-light-dark';
    }
  }

  async getTemplate() {
    if (this.artist === null) {
      this.artist = await get('/api/me');
    }

    switch (this.artist.colorScheme) {
      case 'light':
        document.body.classList.add('cosmo--light-theme');
        break;
      case 'dark':
        document.body.classList.add('cosmo--dark-theme');
        break;
      default:
        document.body.classList.add('cosmo--auto-theme');
        break;
    }
    const roles = getRoles();

    return html`
        <div class="cosmo-top-bar">
            ${roles.includes('ROLE_ADMIN') ? html`
                <div class="cosmo-top-bar__menu" data-stage="front">
                    <a href="#back/maintenance/update" class="cosmo-top-bar__menu-item">
                        ${localize({ key: 'maintenance.menu.title' })}
                    </a>
                    <a href="#back/database/mysql-info" class="cosmo-top-bar__menu-item">
                        ${localize({ key: 'database.menu.title' })}
                    </a>
                    <a href="#back/artists/index" class="cosmo-top-bar__menu-item">
                        ${localize({ key: 'artists.menu.title' })}
                    </a>
                </div>` : ''}
            <img src="${this.artist.profilePicture}" class="cosmo-profile-picture" alt="Imanuel Ulbricht">
            <a class="cosmo-top-bar__menu-item jinya-top-bar__menu-item--logout" id="jinya-logout">
                ${localize({ key: 'top_menu.logout' })}
            </a>
        </div>
        <div class="cosmo-menu-bar">
            <div class="cosmo-menu-bar__touch"></div>
            <button type="button" class="cosmo-menu-bar__back-button"></button>
            <nav class="cosmo-menu-bar__menu-collection" data-stage="front">
                <div class="cosmo-menu-bar__main-menu">
                    <a href="#front/statistics/matomo-stats" data-section="statistics" data-stage="front"
                       class="cosmo-menu-bar__main-item">
                        ${localize({ key: 'statistics.menu.title' })}
                    </a>
                    <a href="#front/media/files" data-section="media" data-stage="front"
                       class="cosmo-menu-bar__main-item">
                        ${localize({ key: 'media.menu.title' })}
                    </a>
                    <a href="#front/pages-and-forms/simple-pages" class="cosmo-menu-bar__main-item"
                       data-section="pages-and-forms" data-stage="front">
                        ${localize({ key: 'pages_and_forms.menu.title' })}
                    </a>
                    <a href="#front/blog/categories" class="cosmo-menu-bar__main-item" data-section="blog"
                       data-stage="front">
                        ${localize({ key: 'blog.menu.title' })}
                    </a>
                    <a href="#front/design/menus" class="cosmo-menu-bar__main-item" data-section="design"
                       data-stage="front">
                        ${localize({ key: 'design.menu.title' })}
                    </a>
                    <a href="#front/my-jinya/my-profile" class="cosmo-menu-bar__main-item"
                       data-section="my-jinya" data-stage="front">
                        ${localize({ key: 'my_jinya.menu.title' })}
                    </a>
                </div>
                <div class="cosmo-menu-bar__sub-menu">
                    <a href="#front/statistics/matomo-stats" data-section="statistics" hidden
                       data-stage="front" data-page="matomo-stats" class="cosmo-menu-bar__sub-item">
                        ${localize({ key: 'statistics.menu.matomo' })}
                    </a>
                    <a href="#front/statistics/database" data-section="statistics" data-stage="front" hidden
                       data-page="database" class="cosmo-menu-bar__sub-item">
                        ${localize({ key: 'statistics.menu.database' })}
                    </a>
                    <a href="#front/media/files" data-section="media" data-stage="front" hidden
                       data-page="files" class="cosmo-menu-bar__sub-item">
                        ${localize({ key: 'media.menu.files' })}
                    </a>
                    <a href="#front/media/galleries" data-section="media" data-stage="front" hidden
                       data-page="galleries" class="cosmo-menu-bar__sub-item">
                        ${localize({ key: 'media.menu.galleries' })}
                    </a>
                    <a href="#front/pages-and-forms/simple-pages" data-section="pages-and-forms" data-stage="front"
                       hidden data-page="simple-pages" class="cosmo-menu-bar__sub-item">
                        ${localize({ key: 'pages_and_forms.menu.simple_pages' })}
                    </a>
                    <a href="#front/pages-and-forms/segment-pages" data-section="pages-and-forms" data-stage="front"
                       hidden data-page="segment-pages" class="cosmo-menu-bar__sub-item">
                        ${localize({ key: 'pages_and_forms.menu.segment_pages' })}
                    </a>
                    <a href="#front/pages-and-forms/forms" data-section="pages-and-forms" data-stage="front" hidden
                       data-page="forms" class="cosmo-menu-bar__sub-item">
                        ${localize({ key: 'pages_and_forms.menu.forms' })}
                    </a>
                    <a href="#front/blog/categories" data-section="blog" data-stage="front" hidden
                       data-page="categories" class="cosmo-menu-bar__sub-item">
                        ${localize({ key: 'blog.menu.categories' })}
                    </a>
                    <a href="#front/blog/posts-overview" data-section="blog" data-stage="front" hidden
                       data-page="posts-overview" class="cosmo-menu-bar__sub-item">
                        ${localize({ key: 'blog.menu.posts' })}
                    </a>
                    <a href="#front/design/menus" data-section="design" data-stage="front" hidden data-page="menus"
                       class="cosmo-menu-bar__sub-item">
                        ${localize({ key: 'design.menu.menus' })}
                    </a>
                </div>
            </nav>
        </div>
        <div class="cosmo-page-body jinya-page-body--app">
            <div class="cosmo-page-body__content">%%%child%%%</div>
        </div>
        <div class="cosmo-bottom-bar cosmo-bottom-bar--three-column">
            <div class="cosmo-bottom-bar__item cosmo-bottom-bar__item--center jinya-progress" style="display: none">
                <span class="cosmo-progress-bar__top-label" id="file-upload-progress-top-label">
                    ${localize({ key: 'bottom_bar.upload_title.uploading' })}
                </span>
                <progress id="file-upload-progress" class="cosmo-progress-bar" value="0" max="0"></progress>
                <span class="cosmo-progress-bar__bottom-label" id="file-upload-progress-bottom-label">
                </span>
            </div>
            <div class="cosmo-bottom-bar__item cosmo-bottom-bar__item--right">
                <button id="toggle-theme-button" class="cosmo-circular-button cosmo-circular-button--large">
                    <span class="mdi ${this.getThemeMode()}"></span>
                </button>
            </div>
        </div>`;
  }

  bumpUploadCounter() {
    this.filesUploaded += 1;
    if (this.filesUploaded === this.filesToUpload) {
      document.getElementById('file-upload-progress-top-label').innerText = localize({ key: 'bottom_bar.upload_title.uploaded' });
    } else {
      document.getElementById('file-upload-progress-top-label').innerText = localize({ key: 'bottom_bar.upload_title.uploading' });
    }
    document.getElementById('file-upload-progress').value = this.filesUploaded;
    document.getElementById('file-upload-progress-bottom-label').innerText = localize({
      key: 'bottom_bar.upload_progress',
      values: {
        filesToUpload: this.filesToUpload,
        filesUploaded: this.filesUploaded,
      },
    });
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
    document.getElementById('toggle-theme-button').addEventListener('click', async () => {
      let colorScheme = '';
      if (document.body.classList.contains('cosmo--light-theme')) {
        document.body.classList.replace('cosmo--light-theme', 'cosmo--dark-theme');
        colorScheme = 'dark';
        document.querySelector('#toggle-theme-button .mdi')
          .classList
          .replace('mdi-weather-sunny', 'mdi-weather-night');
      } else if (document.body.classList.contains('cosmo--dark-theme')) {
        document.body.classList.replace('cosmo--dark-theme', 'cosmo--auto-theme');
        colorScheme = 'auto';
        document.querySelector('#toggle-theme-button .mdi')
          .classList
          .replace('mdi-weather-night', 'mdi-theme-light-dark');
      } else {
        document.body.classList.remove('cosmo--auto-theme');
        document.body.classList.add('cosmo--light-theme');
        colorScheme = 'light';
        document.querySelector('#toggle-theme-button .mdi')
          .classList
          .replace('mdi-theme-light-dark', 'mdi-weather-sunny');
      }

      await put('/api/me/colorscheme', { colorScheme });
    });
  }

  async afterRender() {
    await super.afterRender();
    const { stage, section, page } = urlSplitter();
    document
      .querySelectorAll('.cosmo-menu-bar__main-item--active')
      .forEach((item) => item.classList.remove('cosmo-menu-bar__main-item--active'));
    document
      .querySelector(`.cosmo-menu-bar__main-item[data-stage="${stage}"][data-section="${section}"]`)?.classList
      .add('cosmo-menu-bar__main-item--active');

    document
      .querySelectorAll('.cosmo-menu-bar__sub-item')
      .forEach((item) => item.setAttribute('hidden', 'hidden'));
    document
      .querySelectorAll(`.cosmo-menu-bar__sub-item[data-stage="${stage}"][data-section="${section}"]`)
      .forEach((item) => item.removeAttribute('hidden', 'hidden'));

    document
      .querySelectorAll('.cosmo-menu-bar__sub-item--active')
      .forEach((item) => item.classList.remove('cosmo-menu-bar__sub-item--active'));
    document
      .querySelector(`[data-stage="${stage}"][data-section="${section}"][data-page=${page}]`)?.classList
      .add('cosmo-menu-bar__sub-item--active');
  }
}
