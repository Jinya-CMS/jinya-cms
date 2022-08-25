import html from '../../../../lib/jinya-html.js';
import { post, put } from '../../../foundation/http/request.js';
import localize from '../../../foundation/localize.js';
import alert from '../../../foundation/ui/alert.js';

export default class AddArtistDialog {
  constructor({ onHide }) {
    this.onHide = onHide;
  }

  // eslint-disable-next-line class-methods-use-this
  show() {
    const container = document.createElement('div');
    container.innerHTML = html`
        <div class="cosmo-modal__backdrop"></div>
        <form class="cosmo-modal__container" id="create-artist-dialog">
            <div class="cosmo-modal">
                <h1 class="cosmo-modal__title">${localize({ key: 'artists.create.title' })}</h1>
                <div class="cosmo-modal__content">
                    <div class="cosmo-input__group">
                        <label for="createArtistName" class="cosmo-label">
                            ${localize({ key: 'artists.create.name' })}
                        </label>
                        <input required type="text" id="createArtistName" class="cosmo-input">
                        <label for="createArtistEmail" class="cosmo-label">
                            ${localize({ key: 'artists.create.email' })}
                        </label>
                        <input required type="email" id="createArtistEmail" class="cosmo-input">
                        <label for="createArtistPassword" class="cosmo-label">
                            ${localize({ key: 'artists.create.password' })}
                        </label>
                        <input required type="password" id="createArtistPassword" class="cosmo-input">
                        <span class="cosmo-input__header cosmo-input__header--small">
                            ${localize({ key: 'artists.create.roles' })}
                        </span>
                        <div class="cosmo-checkbox__group">
                            <input checked type="checkbox" id="createArtistIsReader" class="cosmo-checkbox">
                            <label for="createArtistIsReader">
                                ${localize({ key: 'artists.create.is_reader' })}
                            </label>
                        </div>
                        <div class="cosmo-checkbox__group">
                            <input checked type="checkbox" id="createArtistIsWriter" class="cosmo-checkbox">
                            <label for="createArtistIsWriter">
                                ${localize({ key: 'artists.create.is_writer' })}
                            </label>
                        </div>
                        <div class="cosmo-checkbox__group">
                            <input type="checkbox" id="createArtistIsAdmin" class="cosmo-checkbox">
                            <label for="createArtistIsAdmin">
                                ${localize({ key: 'artists.create.is_admin' })}
                            </label>
                        </div>
                    </div>
                </div>
                <div class="cosmo-modal__button-bar">
                    <button class="cosmo-button" id="cancel-artist-dialog">
                        ${localize({ key: 'artists.create.cancel' })}
                    </button>
                    <button class="cosmo-button" type="submit">
                        ${localize({ key: 'artists.create.create' })}
                    </button>
                </div>
            </div>
        </form>`;
    document.body.append(container);

    document.getElementById('cancel-artist-dialog').addEventListener('click', () => container.remove());
    document.getElementById('create-artist-dialog').addEventListener('submit', async () => {
      const artistName = document.getElementById('createArtistName').value;
      const password = document.getElementById('createArtistPassword').value;
      const email = document.getElementById('createArtistEmail').value;
      const isReader = document.getElementById('createArtistIsReader').checked;
      const isWriter = document.getElementById('createArtistIsWriter').checked;
      const isAdmin = document.getElementById('createArtistIsAdmin').checked;

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
      try {
        const artist = await post('/api/artist', {
          artistName,
          email,
          password,
          roles,
        });
        await put(`/api/artist/${artist.id}/activation`);
        artist.enabled = true;
        this.onHide(artist);
        container.remove();
      } catch (e) {
        if (e.status === 409) {
          await alert({
            title: localize({ key: 'artists.create.error.title' }),
            message: localize({ key: 'artists.create.error.conflict' }),
          });
        } else {
          await alert({
            title: localize({ key: 'artists.create.error.title' }),
            message: localize({ key: 'artists.create.error.generic' }),
          });
        }
      }
    });
  }
}
