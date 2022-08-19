import {
  get, post, put, upload,
} from '../http/request.js';
import ConflictError from '../http/Error/ConflictError.js';

let tinyInitialized = false;

async function wait({ time }) {
  return new Promise((resolve) => {
    setTimeout(resolve, time);
  });
}

export default async function getEditor({ element, height = '500px' }) {
  if (!tinyInitialized) {
    const scriptTag = document.createElement('script');
    scriptTag.src = '/jinya-designer/lib/tinymce/tinymce.min.js';
    document.head.append(scriptTag);
    await wait({ time: 1000 });
  }
  tinyInitialized = true;
  // eslint-disable-next-line no-undef
  return (await tinymce.init({
    target: element,
    object_resizing: true,
    relative_urls: false,
    image_advtab: true,
    remove_script_host: false,
    convert_urls: true,
    skin: (window.matchMedia('(prefers-color-scheme: dark)').matches ? 'oxide-dark' : 'oxide'),
    content_css: (window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'default'),
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
