<template>
    <tiny-mce :init="tinyMceOptions" :initial-value="data" @input="input" v-model="data"/>
</template>

<script>
  import TinyMce from '@tinymce/tinymce-vue';
  import JinyaRequest from '@/framework/Ajax/JinyaRequest';
  import ConflictError from '@/framework/Ajax/Error/ConflictError';

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
          plugins: [
            'anchor',
            'autolink',
            'code',
            'colorpicker',
            'contextmenu',
            'fullscreen',
            'help',
            'hr',
            'image',
            'imagetools',
            'link',
            'lists',
            'table',
            'textcolor',
          ],
          relative_urls: false,
          remove_script_host: false,
          convert_urls: true,
          height,
          width: '100%',
          menubar: 'edit insert view format table tools help',
          style_formats: [
            {
              title: 'Image Left',
              selector: 'img',
              styles: {
                float: 'left',
                margin: '0 10px 0 10px',
              },
            },
            {
              title: 'Image Right',
              selector: 'img',
              styles: {
                float: 'right',
                margin: '0 0 10px 10px',
              },
            },
          ],
          toolbar: 'undo redo | '
            + 'styleselect | '
            + 'bold italic | '
            + 'alignleft aligncenter alignright alignjustify | '
            + 'bullist numlist outdent indent | '
            + 'forecolor backcolor',
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

                cb(`${window.location.origin}/api/media/file/${uploadedFile.id}/content`, { title: file.name });
              } catch (e) {
                if (e instanceof ConflictError) {
                  const files = await JinyaRequest.get(`/api/media/file?keyword=${encodeURIComponent(file.name)}`);
                  const selectedFile = files.items[0];

                  cb(`${window.location.origin}/api/media/file/${selectedFile.id}/content`, {
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
</style>
