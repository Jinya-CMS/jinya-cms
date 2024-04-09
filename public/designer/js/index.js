import { Alpine } from '../../lib/alpine.js';
import PineconeRouter from '../../lib/pinecone-router.js';
import { fetchScript, needsAdmin, needsLogin, needsLogout } from './foundation/router.js';
import localize from './foundation/localize.js';
import { getMyProfile, setColorScheme } from './foundation/api/my-jinya.js';
import { logout } from './foundation/api/authentication.js';
import { dataUrlReader } from './foundation/utils/blob.js';
import FileUploadedEvent from './frontstage/media/uploading/FileUploadedEvent.js';
import { getJinyaApiKey } from './foundation/storage.js';

document.addEventListener('DOMContentLoaded', async () => {
  window.Alpine = Alpine;

  Alpine.plugin(PineconeRouter);

  Alpine.directive(
    'localize',
    (
      el,
      {
        value,
        expression,
        modifiers,
      },
      {
        evaluateLater,
        effect,
      },
    ) => {
      const getValues = expression ? evaluateLater(expression) : (load) => load();
      effect(() => {
        getValues((values) => {
          if (modifiers.includes('html')) {
            // eslint-disable-next-line no-param-reassign
            el.innerHTML = localize({
              key: value,
              values,
            });
          } else {
            // eslint-disable-next-line no-param-reassign
            el.textContent = localize({
              key: value,
              values,
            });
          }
        });
      });
    },
  );
  Alpine.directive(
    'active-route',
    (
      el,
      {
        expression,
        modifiers,
      },
      {
        // eslint-disable-next-line no-shadow
        Alpine,
        effect,
      },
    ) => {
      effect(() => {
        const {
          page,
          area,
        } = Alpine.store('navigation');
        if ((modifiers.includes('area') && area === expression) || (!modifiers.includes('area') && page === expression)) {
          el.classList.add('is--active');
        } else {
          el.classList.remove('is--active');
        }
      });
    },
  );
  Alpine.directive(
    'blob-src',
    (
      el,
      {
        expression,
      },
      {
        evaluateLater,
        effect,
      },
    ) => {
      const getValues = expression ? evaluateLater(expression) : (load) => load();
      effect(() => {
        getValues(async (values) => {
          // eslint-disable-next-line no-param-reassign
          el.src = await dataUrlReader({ file: values });
        });
      });
    },
  );

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
      Alpine.store('artist')
        .setArtist({
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
    setArtist({
                profilePicture,
                artistName,
                email,
                aboutMe,
                colorScheme,
              }) {
      this.profilePicture = profilePicture;
      this.artistName = artistName;
      this.email = email;
      this.aboutMe = aboutMe;
      this.colorScheme = colorScheme;
    },
  });
  Alpine.store('navigation', {
    fetchScript,
    stage: 'frontstage',
    area: 'media',
    page: 'files',
    navigate({
               stage,
               area,
               page,
             }) {
      this.stage = stage;
      this.area = area;
      this.page = page;
    },
  });
  Alpine.store('loaded', false);
  Alpine.store('colorScheme', {
    get button() {
      const { colorScheme } = Alpine.store('artist');
      switch (colorScheme) {
        case 'light':
          return '<svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="4"/><path d="M12 3v1"/><path d="M12 20v1"/><path d="M3 12h1"/><path d="M20 12h1"/><path d="m18.364 5.636-.707.707"/><path d="m6.343 17.657-.707.707"/><path d="m5.636 5.636.707.707"/><path d="m17.657 17.657.707.707"/></svg>';
        case 'dark':
          return '<svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 3a6 6 0 0 0 9 9 9 9 0 1 1-9-9Z"/><path d="M19 3v4"/><path d="M21 5h-4"/></svg>';
        default:
          return '<svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><path d="M12 2a7 7 0 1 0 10 10"/></svg>';
      }
    },
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
    async updateTheme() {
      const { colorScheme } = Alpine.store('artist');
      let newScheme = '';
      switch (colorScheme) {
        case 'light':
          newScheme = 'dark';
          break;
        case 'dark':
          newScheme = 'auto';
          break;
        default:
          newScheme = 'light';
          break;
      }
      await setColorScheme(newScheme);
      Alpine.store('artist').colorScheme = newScheme;
    },
  });

  document.addEventListener('alpine:init', () => {
    window.PineconeRouter.settings.basePath = '/designer';
    window.PineconeRouter.settings.templateTargetId = 'app';
  });

  Alpine.start();

  try {
    const myProfile = await getMyProfile();

    Alpine.store('authentication')
      .login({
        loggedIn: true,
        roles: myProfile.roles,
      });
    Alpine.store('artist')
      .setArtist(myProfile);
  } catch {
    Alpine.store('authentication')
      .logout();
  }

  Alpine.store('loaded', true);
  Alpine.store('uploadProgress', {
    filesUploaded: 0,
    filesToUpload: 0,
    errorMessage: '',
    status: '',
    uploaded(file) {
      document.dispatchEvent(new FileUploadedEvent({ file }));
      this.filesUploaded += 1;
    },
    start(name) {
      this.status = localize({
        key: 'bottom_bar.status',
        values: { name },
      });
    },
    failed(error, name) {
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
      this.filesUploaded += 1;
    },
    addFiles(count) {
      this.filesToUpload += count;
    },
  });

  const fileUploadWorker = new Worker('/designer/js/frontstage/media/uploading/UploadWorker.js');
  fileUploadWorker.addEventListener('message', (e) => {
    const {
      type,
      file,
      error,
      name,
    } = e.data;
    if (type === 'upload-finish') {
      Alpine.store('uploadProgress')
        .uploaded(file);
    } else if (type === 'upload-failed') {
      Alpine.store('uploadProgress')
        .failed(error, name);
    } else if (type === 'upload-start') {
      Alpine.store('uploadProgress')
        .start(name);
    }
  });
  fileUploadWorker.postMessage({
    apiKey: getJinyaApiKey(),
  });
  document.addEventListener('enqueue-files', (e) => {
    Alpine.store('uploadProgress')
      .addFiles(e.files.length);
    fileUploadWorker.postMessage({
      files: e.files,
      tags: e.tags,
    });
  });
});
