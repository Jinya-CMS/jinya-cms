import { Alpine } from '../../lib/alpine.js';
import PineconeRouter from '../../lib/pinecone-router.js';
import { fetchScript, needsAdmin, needsLogin, needsLogout } from './foundation/utils/router.js';
import localize from './foundation/utils/localize.js';
import { getMyProfile } from './foundation/api/my-jinya.js';
import { logout } from './foundation/api/authentication.js';
import { dataUrlReader } from './foundation/utils/blob.js';
import { getFileDatabase } from './foundation/database/file.js';

document.addEventListener('DOMContentLoaded', async () => {
  let myProfile = null;
  try {
    myProfile = await getMyProfile();
  } catch {
    console.log('Not logged in, continue initialization but logout at the end');
  }

  const fileDatabase = getFileDatabase();
  window.Alpine = Alpine;

  Alpine.plugin(PineconeRouter);

  Alpine.directive('localize', (el, { value, expression, modifiers }, { evaluateLater, effect }) => {
    const getValues = expression ? evaluateLater(expression) : (load) => load();
    effect(() => {
      getValues((values) => {
        const localized = localize({
          key: value,
          values,
        });

        if (modifiers.includes('html')) {
          el.innerHTML = localized;
        } else if (modifiers.includes('title')) {
          el.setAttribute('title', localized);
        } else {
          el.textContent = localized;
        }
      });
    });
  });
  Alpine.directive('active-route', (el, { expression, modifiers }, { Alpine, effect }) => {
    effect(() => {
      const { page, area } = Alpine.store('navigation');
      if ((modifiers.includes('area') && area === expression) || (!modifiers.includes('area') && page === expression)) {
        el.classList.add('is--active');
      } else {
        el.classList.remove('is--active');
      }
    });
  });
  Alpine.directive('blob-src', (el, { expression }, { evaluateLater, effect }) => {
    const getValues = expression ? evaluateLater(expression) : (load) => load();
    effect(() => {
      getValues(async (values) => {
        el.src = await dataUrlReader({ file: values });
      });
    });
  });

  Alpine.store('authentication', {
    needsLogin,
    needsLogout,
    needsAdmin,
    loggedIn: false,
    roles: [],
    login({ roles }) {
      this.loggedIn = true;
      this.roles = roles;
    },
    logout(fully = false) {
      Alpine.store('artist').setArtist({
        profilePicture: '',
        artistName: '',
        email: '',
        aboutMe: '',
        colorScheme: '',
      });
      logout(fully);
      window.PineconeRouter.context.navigate('/login');
      this.loggedIn = false;
      this.roles = [];
    },
  });
  Alpine.store('artist', {
    profilePicture: '',
    artistName: '',
    colorScheme: '',
    email: '',
    aboutMe: '',
    totpMode: '',
    loginMailEnabled: true,
    newDeviceMailEnabled: true,
    setArtist({
      profilePicture,
      artistName,
      email,
      aboutMe,
      colorScheme,
      totpMode,
      loginMailEnabled,
      newDeviceMailEnabled,
    }) {
      this.profilePicture = profilePicture;
      this.artistName = artistName;
      this.email = email;
      this.aboutMe = aboutMe;
      this.colorScheme = colorScheme;
      this.totpMode = totpMode;
      this.loginMailEnabled = loginMailEnabled;
      this.newDeviceMailEnabled = newDeviceMailEnabled;
    },
  });
  Alpine.store('navigation', {
    fetchScript,
    stage: 'frontstage',
    area: 'statistics',
    page: 'month',
    navigate({ stage, area, page }) {
      this.stage = stage;
      this.area = area;
      this.page = page;
    },
  });
  Alpine.store('loaded', false);
  Alpine.store('colorScheme', {
    get bodyClass() {
      const { colorScheme } = Alpine.store('artist');
      switch (colorScheme) {
        case 'light':
          return 'is--light';
        case 'dark':
          return 'is--dark';
        default:
          return '';
      }
    },
  });
  Alpine.store('loaded', true);
  Alpine.store('uploadProgress', {
    filesUploaded: 0,
    filesToUpload: 0,
    errorMessage: '',
    status: '',
    init() {
      fileDatabase.watchUploadedFilesCount().subscribe({
        next: ({ value: count }) => {
          this.filesUploaded = count;
        },
      });
      fileDatabase.watchUploadingFilesCount().subscribe({
        next: ({ value: count }) => {
          this.filesToUpload = count;
        },
      });
      fileDatabase.watchUploadError().subscribe({
        next: ({ value: { error, name } }) => {
          if (!error) {
            return;
          }

          if (error.status === 409) {
            this.errorMessage = localize({
              key: 'bottom_bar.error.conflict',
              values: { name },
            });
          } else {
            console.error(error);
            this.errorMessage = localize({
              key: 'bottom_bar.error.generic',
              values: { name },
            });
          }
        },
      });
      fileDatabase.watchCurrentUpload().subscribe({
        next: ({ value: name }) => {
          this.status = localize({
            key: 'bottom_bar.status',
            values: { name },
          });
        },
      });
    },
  });

  document.addEventListener('alpine:init', () => {
    window.PineconeRouter.settings.basePath = '/designer';
    window.PineconeRouter.settings.templateTargetId = 'app';
  });

  await import('./bottom-bar.js');

  Alpine.start();

  if (myProfile) {
    Alpine.store('authentication').login({
      loggedIn: true,
      roles: myProfile.roles,
    });
    Alpine.store('artist').setArtist(myProfile);
  } else {
    Alpine.store('authentication').logout();
  }

  const fileUploadWorker = new Worker('/designer/js/background/uploading/UploadWorker.js', { type: 'module' });
  fileUploadWorker.postMessage({
    verb: 'subscribe',
  });

  try {
    await navigator.serviceWorker.register('/designer-file-cache-service-worker.js');
  } catch (e) {
    console.error('Failed to register cache worker');
    console.error(e);
  }
});
