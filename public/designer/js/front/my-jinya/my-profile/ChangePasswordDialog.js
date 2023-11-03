import html from '../../../../lib/jinya-html.js';
import { post } from '../../../foundation/http/request.js';
import localize from '../../../foundation/localize.js';
import alert from '../../../foundation/ui/alert.js';

export default class ChangePasswordDialog {
  // eslint-disable-next-line class-methods-use-this
  show() {
    const container = document.createElement('div');
    container.innerHTML = html`
      <div class="cosmo-modal__backdrop"></div>
      <form class="cosmo-modal__container" id="change-password-dialog">
        <div class="cosmo-modal">
          <h1 class="cosmo-modal__title">${localize({ key: 'my_jinya.my_profile.change_password.title' })}</h1>
          <div class="cosmo-modal__content">
            <div class="cosmo-input__group">
              <label for="oldPassword" class="cosmo-label">
                ${localize({ key: 'my_jinya.my_profile.change_password.old_password' })}
              </label>
              <input required type="password" id="oldPassword" class="cosmo-input" />
              <label for="newPassword" class="cosmo-label">
                ${localize({ key: 'my_jinya.my_profile.change_password.new_password' })}
              </label>
              <input required type="password" id="newPassword" class="cosmo-input" />
              <label for="newPasswordRepeat" class="cosmo-label">
                ${localize({ key: 'my_jinya.my_profile.change_password.new_password_repeat' })}
              </label>
              <input required type="password" id="newPasswordRepeat" class="cosmo-input" />
            </div>
          </div>
          <div class="cosmo-modal__button-bar">
            <button class="cosmo-button" type="button" id="cancel-password-change">
              ${localize({ key: 'my_jinya.my_profile.change_password.keep' })}
            </button>
            <button class="cosmo-button" type="submit">
              ${localize({ key: 'my_jinya.my_profile.change_password.change' })}
            </button>
          </div>
        </div>
      </form>
    `;
    document.body.append(container);

    document.getElementById('cancel-password-change').addEventListener('click', () => container.remove());
    document.getElementById('change-password-dialog').addEventListener('submit', async (e) => {
      e.preventDefault();
      const oldPassword = document.getElementById('oldPassword').value;
      const newPassword = document.getElementById('newPassword').value;
      const newPasswordRepeat = document.getElementById('newPasswordRepeat').value;
      if (newPassword !== newPasswordRepeat) {
        await alert({
          title: localize({ key: 'my_jinya.my_profile.change_password.not_match.title' }),
          message: localize({ key: 'my_jinya.my_profile.change_password.not_match.message' }),
        });
      } else {
        try {
          await post('/api/account/password', { oldPassword, password: newPassword });
          container.remove();
          document.dispatchEvent(new CustomEvent('logout'));
        } catch (e) {
          if (e.status === 403) {
            await alert({
              title: localize({ key: 'my_jinya.my_profile.change_password.error.title' }),
              message: localize({ key: 'my_jinya.my_profile.change_password.error.forbidden' }),
            });
          } else {
            await alert({
              title: localize({ key: 'my_jinya.my_profile.change_password.error.title' }),
              message: localize({ key: 'my_jinya.my_profile.change_password.error.generic' }),
            });
          }
        }
      }
    });
  }
}
