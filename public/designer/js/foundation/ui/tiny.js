import { get } from '../http/request.js';
import filePicker from './filePicker.js';
import localize from '../localize.js';

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
  return (
    await tinymce.init({
      target: element,
      object_resizing: true,
      relative_urls: false,
      remove_script_host: false,
      convert_urls: true,
      skin: getSkin(),
      content_css: getContentCss(),
      height,
      width: '100%',
      async image_list(success) {
        const fileResult = await get('/api/media/file');
        const files = fileResult.items.map((item) => ({
          title: item.name,
          url: item.path,
          value: item.path,
        }));
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
        'undo redo | ' +
        'styleselect | ' +
        'bold italic | ' +
        'alignleft aligncenter alignright alignjustify | ' +
        'bullist numlist outdent indent | ' +
        'forecolor backcolor | ' +
        'link image | ',
      file_picker_type: 'image',
      async file_picker_callback(cb, value, meta) {
        const files = await get('/api/media/file');
        const currentFileId = files.items.find((f) => f.name === meta.title)?.id ?? -1;

        const selectedFile = await filePicker({
          title: localize({ key: 'file_picker.title' }),
          selectedFileId: currentFileId,
          cancelLabel: localize({ key: 'file_picker.dismiss' }),
          pickLabel: localize({ key: 'file_picker.pick' }),
        });
        if (selectedFile) {
          cb(selectedFile.path, { alt: selectedFile.name });
        }
      },
    })
  )[0];
}

export function destroyTiny() {
  tinymce.remove();
}
