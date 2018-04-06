<template>
    <tiny-mce :init="tinyMceOptions" :initial-value="content" v-model="data" @input="$emit('input', data)"/>
</template>

<script>
  import TinyMce from '@tinymce/tinymce-vue';
  import FileUtils from "@/framework/IO/FileUtils";

  export default {
    name: "jinya-tiny-mce",
    components: {
      TinyMce
    },
    props: {
      content: {
        type: String
      }
    },
    data() {
      return {
        data: this.content
      }
    },
    computed: {
      tinyMceOptions() {
        return {
          plugins: [
            'anchor',
            'autolink',
            'colorpicker',
            'contextmenu',
            'fullscreen',
            'help',
            'image',
            'table',
            'textcolor'
          ],
          height: '100%',
          menubar: 'edit insert view format table tools help',
          toolbar: "undo redo | styleselect | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | forecolor backcolor",
          file_picker_type: 'image',
          file_picker_callback(cb) {
            const input = document.createElement('input');
            input.setAttribute('type', 'file');
            input.setAttribute('accept', 'image/*');

            input.onchange = async (event) => {
              const file = event.target.files[0];
              const data = await FileUtils.getAsDataUrl(file);
              const blobCache = tinymce.activeEditor.editorUpload.blobCache;
              const blobInfo = blobCache.create(`blobid-${(new Date()).getTime()}`, file, data.split(',')[1]);
              blobCache.add(blobInfo);

              cb(blobInfo.blobUri(), {title: file.name});
            };

            input.click();
          }
        }
      }
    }
  }
</script>

<style scoped>

</style>