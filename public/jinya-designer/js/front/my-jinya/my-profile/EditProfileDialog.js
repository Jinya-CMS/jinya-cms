import html from '../../../../lib/jinya-html.js';
import { put, upload } from '../../../foundation/http/request.js';
import localize from '../../../foundation/localize.js';
import getEditor from '../../../foundation/ui/tiny.js';

export default class EditProfileDialog {
  constructor({ artist, onHide }) {
    this.artist = artist;
    this.onHide = onHide;
  }

  async show() {
    const container = document.createElement('div');
    container.style.display = 'none';
    container.innerHTML = html`
        <div class="cosmo-modal__backdrop"></div>
        <form class="cosmo-modal__container" id="edit-profile-dialog">
            <div class="cosmo-modal">
                <h1 class="cosmo-modal__title">
                    ${localize({ key: 'my_jinya.my_profile.edit.title' })}
                </h1>
                <div class="cosmo-modal__content">
                    <div class="cosmo-input__group">
                        <label for="artistName" class="cosmo-label">
                            ${localize({ key: 'my_jinya.my_profile.artist_name' })}
                        </label>
                        <input required value="${this.artist.artistName}" type="text" id="artistName"
                               class="cosmo-input">
                        <label for="email" class="cosmo-label">
                            ${localize({ key: 'my_jinya.my_profile.email' })}
                        </label>
                        <input required value="${this.artist.email}" type="email" id="email" class="cosmo-input">
                        <label for="profilePictureFile" class="cosmo-label">
                            ${localize({ key: 'my_jinya.my_profile.profile_picture' })}
                        </label>
                        <input type="file" class="cosmo-input" id="profilePictureFile">
                        <label for="aboutMe" class="cosmo-label cosmo-label--textarea">
                            ${localize({ key: 'my_jinya.my_profile.about_me' })}
                        </label>
                        <textarea class="jinya-profile__about-me" id="aboutMe"></textarea>
                    </div>
                    <div class="cosmo-modal__button-bar">
                        <button class="cosmo-button" type="button" id="discardProfile">
                            ${localize({ key: 'my_jinya.my_profile.action.discard_profile' })}
                        </button>
                        <button class="cosmo-button" type="submit">
                            ${localize({ key: 'my_jinya.my_profile.action.save_profile' })}
                        </button>
                    </div>
                </div>
            </div>
        </form>`;
    document.body.append(container);
    const tiny = await getEditor({ element: document.getElementById('aboutMe') });
    tiny.setContent(this.artist.aboutMe);
    container.style.display = 'inherit';

    document.getElementById('discardProfile').addEventListener('click', () => container.remove());
    document.getElementById('edit-profile-dialog').addEventListener('submit', async (e) => {
      e.preventDefault();
      const artistName = document.getElementById('artistName').value;
      const email = document.getElementById('email').value;
      const aboutMe = tiny.getContent();
      await put('/api/me', { email, artistName, aboutMe });
      const profilePictureInput = document.getElementById('profilePictureFile');
      if (profilePictureInput.files.length > 0) {
        const pic = profilePictureInput.files[0];
        await upload('/api/me/profilepicture', pic);
      }
      tinymce.remove();
      container.remove();
      this.onHide({ artist: { artistName, email, aboutMe } });
    });
  }
}
