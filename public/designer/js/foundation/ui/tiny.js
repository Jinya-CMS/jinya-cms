import ConflictError from '../http/Error/ConflictError.js';
import {
 get, post, put, upload,
} from '../http/request.js';

async function wait({ time }) {
  return new Promise((resolve) => {
    setTimeout(resolve, time);
  });
}

function getThemeMode() {
  if (document.querySelector('.cosmo--dark-theme')) {
    return 'dark';
  }

  if (document.querySelector('.cosmo--light-theme')) {
    return 'light';
  }

  return 'auto';
}

function getSkin() {
  const themeMode = getThemeMode();
  switch (themeMode) {
    case 'dark':
      return 'oxide-dark';
    case 'light':
      return 'oxide';
    default:
      return window.matchMedia('(prefers-color-scheme: dark)').matches ? 'oxide-dark' : 'oxide';
  }
}

function getContentCss() {
  const themeMode = getThemeMode();
  switch (themeMode) {
    case 'dark':
      return 'dark';
    case 'light':
      return 'default';
    default:
      return window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'default';
  }
}

/**
 *
 * @param element
 * @param height
 * @return {Promise<Editor>}
 */
export default async function getEditor({ element, height = '500px' }) {
  // eslint-disable-next-line no-undef
  return (await tinymce.init({
    target: element,
    object_resizing: true,
    relative_urls: false,
    image_advtab: true,
    remove_script_host: false,
    convert_urls: true,
    skin: getSkin(),
    content_css: getContentCss(),
    height,
    width: '100%',
    async image_list(success) {
      const fileResult = await get('/api/media/file');
      const files = fileResult.items.map((item) => ({ title: item.name, url: item.path, value: item.path }));
      success(files);
    },
    plugins: [
      'advlist',
      'anchor',
      'autolink',
      'charmap',
      'code',
      'fullscreen',
      'help',
      'image',
      'link',
      'lists',
      'media',
      'searchreplace',
      'table',
      'visualblocks',
      'wordcount',
    ],
    toolbar:
      'undo redo | '
      + 'styleselect | '
      + 'bold italic | '
      + 'alignleft aligncenter alignright alignjustify | '
      + 'bullist numlist outdent indent | '
      + 'forecolor backcolor | '
      + 'link image | ',
    file_picker_type:
      'image',
    file_picker_callback(cb) {
      const input = document.createElement('input');
      input.setAttribute('type', 'file');
      input.setAttribute('accept', 'image/*');

      input.onchange = async (event) => {
        const file = event.target.files[0];
        try {
          const { id } = await post('/api/media/file', { name: file.name });
          await put(`/api/media/file/${id}/content`);
          await upload(`/api/media/file/${id}/content/0`, file);
          await put(`/api/media/file/${id}/content/finish`);
          const uploadedFile = await get(`/api/media/file/${id}`);

          cb(uploadedFile.path, { title: file.name });
        } catch (e) {
          if (e instanceof ConflictError) {
            const foundFiles = await get(`/api/media/file?keyword=${encodeURIComponent(file.name)}`);
            const selectedFile = foundFiles.items[0];

            cb(selectedFile.path, { title: selectedFile.name });
          }
        }
      };

      input.click();
    },
  }))[0];
}

export function destroyTiny() {
    tinymce.remove();
}
