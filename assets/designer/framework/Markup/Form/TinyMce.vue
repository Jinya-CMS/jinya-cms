<template>
    <tiny-mce :init="tinyMceOptions" :initial-value="data" @input="input" v-model="data"/>
</template>

<script>
  import TinyMce from '@tinymce/tinymce-vue';
  import JinyaRequest from '@/framework/Ajax/JinyaRequest';
  import ConflictError from '@/framework/Ajax/Error/ConflictError';

  import 'tinymce/tinymce';

  import 'tinymce/themes/silver';

  import 'tinymce/plugins/advlist';
  import 'tinymce/plugins/anchor';
  import 'tinymce/plugins/autolink';
  import 'tinymce/plugins/charmap';
  import 'tinymce/plugins/code';
  import 'tinymce/plugins/colorpicker';
  import 'tinymce/plugins/contextmenu';
  import 'tinymce/plugins/fullscreen';
  import 'tinymce/plugins/help';
  import 'tinymce/plugins/hr';
  import 'tinymce/plugins/image';
  import 'tinymce/plugins/link';
  import 'tinymce/plugins/lists';
  import 'tinymce/plugins/media';
  import 'tinymce/plugins/paste';
  import 'tinymce/plugins/searchreplace';
  import 'tinymce/plugins/table';
  import 'tinymce/plugins/textcolor';
  import 'tinymce/plugins/visualblocks';
  import 'tinymce/plugins/wordcount';

  export default {
    name: 'jinya-tiny-mce',
    components: {
      TinyMce,
    },
    watch: {
      content(newValue) {
        this.data = newValue;
      },
    },
    props: {
      content: {
        type: String,
      },
      height: {
        type: String,
        default() {
          return '600px';
        },
      },
      required: {
        type: Boolean,
        default() {
          return false;
        },
      },
    },
    methods: {
      input($event) {
        this.$emit('input', $event);
        if (this.required && !$event) {
          const validityState = { valueMissing: true };
          this.$emit('invalid', validityState);
        } else {
          this.$emit('valid');
        }
      },
    },
    data() {
      const { height } = this;
      return {
        data: this.content,
        tinyMceOptions: {
          skin_url: '/tinymce/skins/ui/oxide',
          language_url: '/tinymce/langs/de.js',
          language: 'de',
          object_resizing: true,
          relative_urls: false,
          image_advtab: true,
          remove_script_host: false,
          convert_urls: true,
          height,
          width: '100%',
          async image_list(success) {
            const files = await JinyaRequest.get('/api/media/file');
            success(files.items.map((item) => ({ title: item.name, value: `/api/media/file/${item.id}/content` })));
          },
          plugins: [
            'advlist',
            'anchor',
            'autolink',
            'charmap',
            'code',
            'colorpicker',
            'contextmenu',
            'fullscreen',
            'help',
            'hr',
            'image',
            'link',
            'lists',
            'media',
            'paste',
            'searchreplace',
            'table',
            'textcolor',
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
                const { id } = await JinyaRequest.post('/api/media/file', { name: file.name });
                await JinyaRequest.post(`/api/media/file/${id}/content`);
                await JinyaRequest.upload(`/api/media/file/${id}/content/0`, file);
                await JinyaRequest.put(`/api/media/file/${id}/content/finish`);
                const uploadedFile = await JinyaRequest.get(`/api/media/file/${id}`);

                cb(`/api/media/file/${uploadedFile.id}/content`, { title: file.name });
              } catch (e) {
                if (e instanceof ConflictError) {
                  const files = await JinyaRequest.get(`/api/media/file?keyword=${encodeURIComponent(file.name)}`);
                  const selectedFile = files.items[0];

                  cb(`/api/media/file/${selectedFile.id}/content`, {
                    title: selectedFile.name,
                  });
                }
              }
            };

            input.click();
          },
        },
      };
    },
  };
</script>

<style lang="scss">
    .tox.tox-tinymce {
        margin-bottom: 1em;
    }

    .tox .tox-editor-header {
        z-index: unset !important;
    }

    .tox-tinymce-aux {
        z-index: 99999;
    }
</style>
