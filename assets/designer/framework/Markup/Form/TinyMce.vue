<template>
    <tiny-mce :init="tinyMceOptions" :initial-value="data" @input="input" v-model="data"/>
</template>

<script>
  import TinyMce from '@tinymce/tinymce-vue';
  import FileUtils from '@/framework/IO/FileUtils';

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
            'link',
            'lists',
            'table',
            'textcolor',
          ],
          height,
          menubar: 'edit insert view format table tools help',
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
              const data = await FileUtils.getAsDataUrl(file);
              // eslint-disable-next-line no-undef
              const { blobCache } = tinymce.activeEditor.editorUpload;
              const blobInfo = blobCache.create(`blobid-${(new Date()).getTime()}`, file, data.split(',')[1]);
              blobCache.add(blobInfo);

              cb(blobInfo.blobUri(), { title: file.name });
            };

            input.click();
          },
        },
      };
    },
  };
</script>

<style lang="scss">
    .mce-container.mce-panel.mce-tinymce {
        margin-bottom: 1em;
    }
</style>
