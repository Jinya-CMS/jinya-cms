import { init } from 'tinymce';

import 'tinymce';
import 'tinymce/icons/default';
import 'tinymce/models/dom/model';
import 'tinymce/plugins/advlist';
import 'tinymce/plugins/anchor';
import 'tinymce/plugins/autolink';
import 'tinymce/plugins/charmap';
import 'tinymce/plugins/code';
import 'tinymce/plugins/fullscreen';
import 'tinymce/plugins/help';
import 'tinymce/plugins/image';
import 'tinymce/plugins/link';
import 'tinymce/plugins/lists';
import 'tinymce/plugins/media';
import 'tinymce/plugins/searchreplace';
import 'tinymce/plugins/table';
import 'tinymce/plugins/visualblocks';
import 'tinymce/plugins/wordcount';
import 'tinymce/themes/silver';
import ConflictError from '../http/Error/ConflictError';
import { get, getHost, post, put, upload } from '../http/request';

/**
 *
 * @param element
 * @param height
 * @returns {Promise<Editor>}
 */
export async function createTiny(element, height = '500px') {
  return (await init({
    target: element,
    object_resizing: true,
    relative_urls: false,
    image_advtab: true,
    content_css: ['/static/cosmo/variables.css', '/static/cosmo/lato.css', '/static/css/tiny/content/content.css'],
    remove_script_host: false,
    convert_urls: true,
    skin_url: '/static/css/tiny/ui',
    height,
    width: '100%',
    async image_list(success) {
      const files = await get('/api/media/file');
      success(files.items.map((item) => ({title: item.name, value: `${getHost()}${item.path}`})));
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
    toolbar: 'undo redo | '
      + 'styleselect | '
      + 'bold italic | '
      + 'alignleft aligncenter alignright alignjustify | '
      + 'bullist numlist outdent indent | '
      + 'forecolor backcolor | '
      + 'link image | ',
    file_picker_type: 'image',
    file_picker_callback(cb) {
      const input = document.createElement('input');
      input.setAttribute('type', 'file');
      input.setAttribute('accept', 'image/*');

      input.onchange = async (event) => {
        const file = event.target.files[0];
        try {
          const {id} = await post('/api/media/file', {name: file.name});
          await put(`/api/media/file/${id}/content`);
          await upload(`/api/media/file/${id}/content/0`, file);
          await put(`/api/media/file/${id}/content/finish`);
          const uploadedFile = await get(`/api/media/file/${id}`);

          cb(`${getHost()}${uploadedFile.path}`, {title: file.name});
        } catch (e) {
          if (e instanceof ConflictError) {
            const files = await get(`/api/media/file?keyword=${encodeURIComponent(file.name)}`);
            const selectedFile = files.items[0];

            cb(`${getHost()}${selectedFile.path}`, {title: selectedFile.name,});
          }
        }
      };

      input.click();
    },
  }))[0];
}
