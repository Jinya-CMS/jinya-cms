import html from '../../../lib/jinya-html.js';
import clearChildren from '../../foundation/html/clearChildren.js';
import { get, httpDelete, put } from '../../foundation/http/request.js';
import JinyaDesignerPage from '../../foundation/JinyaDesignerPage.js';
import localize from '../../foundation/localize.js';
import confirm from '../../foundation/ui/confirm.js';

export default class ArtistPage extends JinyaDesignerPage {
  constructor({ layout }) {
    super({ layout });
    this.artists = [];
    this.selectedArtist = null;
    this.me = null;
  }

  selectArtist({ id }) {
    this.selectedArtist = this.artists.find((a) => a.id === id);
  }

  displaySelectedArtist() {
    if (this.me.id === this.selectedArtist.id) {
      document.getElementById('edit-artist').disabled = true;
      document.getElementById('delete-artist').disabled = true;
      document.getElementById('disable-artist').disabled = true;
      document.getElementById('enable-artist').disabled = true;
    } else {
      document.getElementById('edit-artist').disabled = false;
      document.getElementById('delete-artist').disabled = false;
      document.getElementById('disable-artist').disabled = !this.selectedArtist.enabled;
      document.getElementById('enable-artist').disabled = this.selectedArtist.enabled;
    }
    document.getElementById('artist-profile-picture').src = this.selectedArtist.profilePicture;
    document.getElementById('artist-profile-picture').alt = this.selectedArtist.artistName;
    document.getElementById('artist-name').innerText = this.selectedArtist.artistName;
    document.getElementById('artist-email').innerText = this.selectedArtist.email;
    document.getElementById('about-me').innerHTML = this.selectedArtist.aboutMe;
    document
      .querySelectorAll('.cosmo-side-list__item.is--active')
      .forEach((item) => item.classList.remove('is--active'));
    document.querySelector(`[data-id="${this.selectedArtist.id}"]`)
      .classList
      .add('is--active');
  }

  displayArtists() {
    document.getElementById('edit-artist').disabled = true;
    document.getElementById('delete-artist').disabled = true;
    document.getElementById('disable-artist').disabled = true;
    document.getElementById('enable-artist').disabled = true;
    let list = '';
    for (const artist of this.artists) {
      list += `<a class="cosmo-side-list__item" data-id="${artist.id}">${artist.artistName}</a>`;
    }
    clearChildren({ parent: document.getElementById('artist-list') });
    document.getElementById('artist-list').innerHTML = `${list}
                <button id="new-artist-button" class="cosmo-button is--full-width">
                    ${localize({ key: 'artists.action.new' })}
                </button>`;
    document.querySelectorAll('.cosmo-side-list__item')
      .forEach((item) => {
        item.addEventListener('click', async () => {
          this.selectArtist({ id: parseInt(item.getAttribute('data-id'), 10) });
          this.displaySelectedArtist();
        });
      });
    document.getElementById('new-artist-button')
      .addEventListener('click', async () => {
        const { default: AddArtistDialog } = await import('./artists/AddArtistDialog.js');
        const dialog = new AddArtistDialog({
          onHide: async (artist) => {
            this.artists.push(artist);
            this.displayArtists();
            this.selectArtist({ id: artist.id });
            this.displaySelectedArtist();
          },
        });
        dialog.show();
      });
  }

  // eslint-disable-next-line class-methods-use-this
  toString() {
    return html` 
      <div class="cosmo-side-list">
      <nav class="cosmo-side-list__items" id="artist-list"></nav>
      <div class="cosmo-side-list__content jinya-designer">
        <div class="jinya-profile-view">
          <div class="cosmo-toolbar jinya-toolbar--media">
            <div class="cosmo-toolbar__group">
              <button disabled id="edit-artist" class="cosmo-button">
                ${localize({ key: 'artists.action.edit' })}
              </button>
              <button disabled id="enable-artist" class="cosmo-button">
                ${localize({ key: 'artists.action.enable' })}
              </button>
              <button disabled id="disable-artist" class="cosmo-button">
                ${localize({ key: 'artists.action.disable' })}
              </button>
              <button disabled id="delete-artist" class="cosmo-button">
                ${localize({ key: 'artists.action.delete' })}
              </button>
            </div>
          </div>
          <div class="jinya-profile__container">
            <div class="jinya-profile__sidebar">
              <img src="" id="artist-profile-picture" class="jinya-profile__picture" alt="" />
            </div>
            <div class="jinya-profile__about-me">
              <div class="cosmo-title">
                <span id="artist-name"></span>
                (<span id="artist-email"></span>)
              </div>
              <div id="about-me"></div>
            </div>
          </div>
        </div>
      </div>
    </div>`;
  }

  async displayed() {
    await super.displayed();
    const { items } = await get('/api/artist');
    this.me = await get('/api/me');
    this.artists = items;
    this.displayArtists();
    this.selectArtist({ id: this.artists[0].id });
    this.displaySelectedArtist();
  }

  bindEvents() {
    super.bindEvents();
    document.getElementById('enable-artist')
      .addEventListener('click', async () => {
        const result = await confirm({
          title: localize({ key: 'artists.enable.title' }),
          message: localize({
            key: 'artists.enable.message',
            values: this.selectedArtist,
          }),
          approveLabel: localize({ key: 'artists.enable.delete' }),
          declineLabel: localize({ key: 'artists.enable.keep' }),
        });
        if (result) {
          const { id } = this.selectedArtist;
          await put(`/api/artist/${id}/activation`);
          const artist = this.artists.find((item) => item.id === id);
          artist.enabled = true;
          this.selectArtist({ id });
          this.displaySelectedArtist();
        }
      });
    document.getElementById('disable-artist')
      .addEventListener('click', async () => {
        const result = await confirm({
          title: localize({ key: 'artists.disable.title' }),
          message: localize({
            key: 'artists.disable.message',
            values: this.selectedArtist,
          }),
          approveLabel: localize({ key: 'artists.disable.delete' }),
          declineLabel: localize({ key: 'artists.disable.keep' }),
          negative: true,
        });
        if (result) {
          const { id } = this.selectedArtist;
          await httpDelete(`/api/artist/${id}/activation`);
          const artist = this.artists.find((item) => item.id === id);
          artist.enabled = false;
          this.selectArtist({ id });
          this.displaySelectedArtist();
        }
      });
    document.getElementById('delete-artist')
      .addEventListener('click', async () => {
        const result = await confirm({
          title: localize({ key: 'artists.delete.title' }),
          message: localize({
            key: 'artists.delete.message',
            values: this.selectedArtist,
          }),
          approveLabel: localize({ key: 'artists.delete.delete' }),
          declineLabel: localize({ key: 'artists.delete.keep' }),
          negative: true,
        });
        if (result) {
          const { id } = this.selectedArtist;
          await httpDelete(`/api/artist/${id}`);
          const artistIdx = this.artists.findIndex((item) => item.id === id);
          this.artists.splice(artistIdx, 1);
          this.displayArtists();
          this.selectArtist({ id });
          this.displaySelectedArtist();
        }
      });
    document.getElementById('edit-artist')
      .addEventListener('click', async () => {
        const { default: EditArtistDialog } = await import('./artists/EditArtistDialog.js');
        const dialog = new EditArtistDialog({
          onHide: async ({
                           id,
                           artistName,
                           email,
                           roles,
                         }) => {
            const currentArtist = this.artists.find((a) => a.id === id);
            currentArtist.artistName = artistName;
            currentArtist.email = email;
            currentArtist.roles = roles;
            this.displayArtists();
            this.selectArtist({ id });
            this.displaySelectedArtist();
          },
          ...this.selectedArtist,
        });
        dialog.show();
      });
  }
}
