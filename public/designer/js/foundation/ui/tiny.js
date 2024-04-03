import filePicker from './filePicker.js';
import localize from '../localize.js';
import { getFiles } from '../api/files.js';

import '../../../lib/tiny/tinymce.min.js';

function getTheme() {
  if (document.body.classList.contains('is--light')) {
    return 'light';
  }
  if (document.body.classList.contains('is--dark')) {
    return 'dark';
  }

  return 'auto';
}

function getSkin() {
  const themeMode = getTheme();
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
  const themeMode = getTheme();
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
export default async function getEditor({
                                          element,
                                          height = '500px',
                                        }) {
  // eslint-disable-next-line no-undef
  const tiny = await tinymce.init({
    license_key: 'gpl',
    target: element,
    resize: false,
    language: navigator.language.substring(0, 2),
    object_resizing: true,
    relative_urls: false,
    remove_script_host: false,
    convert_urls: true,
    skin: getSkin(),
    content_css: getContentCss(),
    height,
    promotion: false,
    width: '100%',
    base_url: '/designer/lib/tiny',
    suffix: '.min',
    async image_list(success) {
      const fileResult = await getFiles();
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
      'undo redo | '
      + 'styleselect | '
      + 'bold italic | '
      + 'alignleft aligncenter alignright alignjustify | '
      + 'bullist numlist outdent indent | '
      + 'forecolor backcolor | '
      + 'link image | ',
    file_picker_type: 'image',
    async file_picker_callback(cb, value, meta) {
      const files = await getFiles();
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
  });

  return tiny[0];
}
