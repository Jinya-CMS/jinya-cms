import html from '../../../lib/jinya-html.js';
import { get } from '../../foundation/http/request.js';
import JinyaDesignerPage from '../../foundation/JinyaDesignerPage.js';
import localize from '../../foundation/localize.js';

export default class MyProfilePage extends JinyaDesignerPage {
  constructor({ layout }) {
    super({ layout });
    this.artist = {};
  }

  // eslint-disable-next-line class-methods-use-this
  toString() {
    return html`
        <div class="jinya-profile-view">
            <div class="cosmo-toolbar jinya-toolbar--media">
                <div class="cosmo-toolbar__group">
                    <button id="edit-profile" class="cosmo-button" type="button">
                        ${localize({ key: 'my_jinya.my_profile.action.edit_profile' })}
                    </button>
                </div>
                <div class="cosmo-toolbar__group">
                    <button class="cosmo-button" id="change-password" type="button">
                        ${localize({ key: 'my_jinya.my_profile.action.change_password' })}
                    </button>
                </div>
            </div>
            <div class="jinya-profile__container">
                <div class="jinya-profile__sidebar">
                    <img src="" id="artist-profile-picture" class="jinya-profile__picture" alt="">
                </div>
                <div class="jinya-profile__about-me">
                    <div class="cosmo-title">
                        <span id="artist-name"></span>
                        (<span id="artist-email"></span>)
                    </div>
                    <div id="about-me"></div>
                </div>
            </div>
        </div>`;
  }

  displayProfile() {
    document.getElementById('artist-email').innerText = this.artist.email;
    document.getElementById('artist-name').innerText = this.artist.artistName;
    document.getElementById('about-me').innerHTML = this.artist.aboutMe;
    const profilePicture = document.getElementById('artist-profile-picture');
    profilePicture.src = this.artist.profilePicture;
    profilePicture.alt = this.artist.artistName;
  }

  async displayed() {
    await super.displayed();
    this.artist = await get('/api/me');
    this.displayProfile();
  }

  bindEvents() {
    super.bindEvents();
    document.getElementById('change-password').addEventListener('click', async () => {
      const { default: ChangePasswordDialog } = await import('./my-profile/ChangePasswordDialog.js');
      const dialog = new ChangePasswordDialog();
      dialog.show();
    });
    document.getElementById('edit-profile').addEventListener('click', async () => {
      const { default: EditProfileDialog } = await import('./my-profile/EditProfileDialog.js');
      const dialog = new EditProfileDialog({
        artist: this.artist,
        onHide: async () => {
          this.artist = await get('/api/me');
          this.displayProfile();
          document.querySelector('.cosmo-profile-picture').src = this.artist.profilePicture;
          document.querySelector('.cosmo-profile-picture').alt = this.artist.artistName;
        },
      });
      await dialog.show();
    });
  }
}
