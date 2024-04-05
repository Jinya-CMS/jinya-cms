import html from '../../../../lib/jinya-html.js';
import { put } from '../../../foundation/http/request.js';
import localize from '../../../foundation/localize.js';
import alert from '../../../foundation/ui/alert.js';

export default class EditArtistDialog {
  constructor({ onHide, id, artistName, email, roles }) {
    this.onHide = onHide;
    this.id = id;
    this.artistName = artistName;
    this.email = email;
    this.roles = roles;
  }

  // eslint-disable-next-line class-methods-use-this
  show() {
    const container = document.createElement('div');
    container.innerHTML = html` <form class="cosmo-modal__container" id="edit-artist-dialog">
      <div class="cosmo-modal">
        <h1 class="cosmo-modal__title">${localize({ key: 'artists.edit.title' })}</h1>
        <div class="cosmo-modal__content">
          <div class="cosmo-input__group">
            <label for="editArtistName" class="cosmo-label">${localize({ key: 'artists.edit.name' })}</label>
            <input value="${this.artistName}" required type="text" id="editArtistName" class="cosmo-input" />
            <label for="editArtistEmail" class="cosmo-label">${localize({ key: 'artists.edit.email' })}</label>
            <input value="${this.email}" required type="email" id="editArtistEmail" class="cosmo-input" />
            <label for="editArtistPassword" class="cosmo-label"> ${localize({ key: 'artists.edit.password' })} </label>
            <input type="password" id="editArtistPassword" class="cosmo-input" />
            <span class="cosmo-label is--checkbox"> ${localize({ key: 'artists.edit.roles' })} </span>
            <div class="cosmo-input__group is--checkbox">
              <input
                ${this.roles.includes('ROLE_READER') ? 'checked' : ''}
                type="checkbox"
                id="editArtistIsReader"
                class="cosmo-checkbox"
              />
              <label for="editArtistIsReader">${localize({ key: 'artists.edit.is_reader' })}</label>
            </div>
            <div class="cosmo-input__group is--checkbox">
              <input
                ${this.roles.includes('ROLE_WRITER') ? 'checked' : ''}
                type="checkbox"
                id="editArtistIsWriter"
                class="cosmo-checkbox"
              />
              <label for="editArtistIsWriter">${localize({ key: 'artists.edit.is_writer' })}</label>
            </div>
            <div class="cosmo-input__group is--checkbox">
              <input
                ${this.roles.includes('ROLE_ADMIN') ? 'checked' : ''}
                type="checkbox"
                id="editArtistIsAdmin"
                class="cosmo-checkbox"
              />
              <label for="editArtistIsAdmin">${localize({ key: 'artists.edit.is_admin' })}</label>
            </div>
          </div>
        </div>
        <div class="cosmo-modal__button-bar">
          <button class="cosmo-button" id="cancel-artist-dialog">${localize({ key: 'artists.edit.cancel' })}</button>
          <button class="cosmo-button" type="submit">${localize({ key: 'artists.edit.update' })}</button>
        </div>
      </div>
    </form>`;
    document.body.append(container);

    document.getElementById('cancel-artist-dialog').addEventListener('click', () => container.remove());
    document.getElementById('edit-artist-dialog').addEventListener('submit', async () => {
      const artistName = document.getElementById('editArtistName').value;
      const password = document.getElementById('editArtistPassword').value;
      const email = document.getElementById('editArtistEmail').value;
      const isReader = document.getElementById('editArtistIsReader').checked;
      const isWriter = document.getElementById('editArtistIsWriter').checked;
      const isAdmin = document.getElementById('editArtistIsAdmin').checked;

      const roles = [];
      if (isReader) {
        roles.push('ROLE_READER');
      }
      if (isWriter) {
        roles.push('ROLE_WRITER');
      }
      if (isAdmin) {
        roles.push('ROLE_ADMIN');
      }
      const data = {
        artistName,
        email,
        roles,
      };

      if (password) {
        data.password = password;
      }

      try {
        await put(`/api/artist/${this.id}`, data);
        this.onHide({
          ...data,
          id: this.id,
        });
        container.remove();
      } catch (e) {
        if (e.status === 409) {
          await alert({
            title: localize({ key: 'artists.edit.error.title' }),
            message: localize({ key: 'artists.edit.error.conflict' }),
            negative: true,
          });
        } else {
          await alert({
            title: localize({ key: 'artists.edit.error.title' }),
            message: localize({ key: 'artists.edit.error.generic' }),
            negative: true,
          });
        }
      }
    });
  }
}
