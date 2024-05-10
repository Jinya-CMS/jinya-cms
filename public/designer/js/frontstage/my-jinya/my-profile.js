import { Alpine } from '../../../../lib/alpine.js';
import { changePassword, logout } from '../../foundation/api/authentication.js';
import localize from '../../foundation/utils/localize.js';
import { getMyProfile, updateAboutMe, updateProfile, updateProfilePicture } from '../../foundation/api/my-jinya.js';

import '../../foundation/ui/components/toolbar-editor.js';

Alpine.data('profileData', () => ({
  init() {
    this.aboutMe.data = Alpine.store('artist').aboutMe;
  },
  openChangePasswordDialog() {
    this.changePassword.oldPassword = '';
    this.changePassword.newPassword = '';
    this.changePassword.newPasswordRepeat = '';
    this.changePassword.open = true;
    this.changePassword.error.reset();
  },
  openProfileDialog() {
    this.edit.artistName = Alpine.store('artist').artistName;
    this.edit.email = Alpine.store('artist').email;
    this.edit.open = true;
    this.edit.error.reset();
  },
  async updatePassword() {
    try {
      await changePassword(this.changePassword.oldPassword, this.changePassword.newPassword);
      await logout(false);
    } catch (e) {
      this.changePassword.error.title = localize({ key: 'my_jinya.my_profile.change_password.error.title' });
      this.changePassword.error.hasError = true;
      if (e.status === 403) {
        this.changePassword.error.message = localize({ key: 'my_jinya.my_profile.change_password.error.forbidden' });
      } else {
        this.changePassword.error.message = localize({ key: 'my_jinya.my_profile.change_password.error.generic' });
      }
    }
  },
  async saveAboutMe() {
    try {
      this.aboutMe.message.reset();
      await updateAboutMe(this.aboutMe.data);

      this.aboutMe.message.hasMessage = true;
      this.aboutMe.message.isNegative = false;
      this.aboutMe.message.title = localize({ key: 'my_jinya.my_profile.about_me.success.title' });
      this.aboutMe.message.content = localize({ key: 'my_jinya.my_profile.about_me.success.message' });
      setTimeout(() => {
        this.aboutMe.message.hasMessage = false;
      }, 30000);
    } catch (e) {
      this.aboutMe.message.hasMessage = true;
      this.aboutMe.message.isNegative = true;
      this.aboutMe.message.title = localize({ key: 'my_jinya.my_profile.about_me.error.title' });
      this.aboutMe.message.content = localize({ key: 'my_jinya.my_profile.about_me.error.message' });
    }
  },
  resetAboutMe() {
    this.aboutMe.data = Alpine.store('artist').aboutMe;
  },
  async updateProfile() {
    try {
      await updateProfile(this.edit.artistName, this.edit.email);
      await updateProfilePicture(this.edit.profilePicture);

      const myProfile = await getMyProfile();
      Alpine.store('artist')
        .setArtist(myProfile);
      this.edit.open = false;
    } catch (e) {
      this.edit.error.hasError = true;
      this.edit.error.title = localize({ key: 'my_jinya.my_profile.edit.error.title' });
      this.edit.error.message = localize({ key: 'my_jinya.my_profile.edit.error.message' });
    }
  },
  changePassword: {
    open: false,
    oldPassword: '',
    newPassword: '',
    newPasswordRepeat: '',
    error: {
      reset() {
        this.hasError = false;
        this.title = '';
        this.message = '';
      },
      hasError: false,
      title: '',
      message: '',
    },
  },
  aboutMe: {
    data: '',
    message: {
      reset() {
        this.hasError = false;
        this.title = '';
        this.content = '';
      },
      hasError: false,
      isNegative: false,
      title: '',
      content: '',
    },
  },
  edit: {
    open: false,
    artistName: '',
    email: '',
    profilePicture: null,
    selectProfilePicture(files) {
      if (files.length >= 1) {
        this.profilePicture = files.item(0);
      }
    },
    error: {
      reset() {
        this.hasError = false;
        this.title = '';
        this.content = '';
      },
      hasError: false,
      title: '',
      content: '',
    },
  },
}));
