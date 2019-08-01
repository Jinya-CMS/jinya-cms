<template>
    <jinya-editor>
        <jinya-message :message="message" :state="state" v-if="state">
        </jinya-message>
        <jinya-form :cancel-label="cancelLabel" :enable="enable" :save-label="saveLabel" @back="back"
                    @submit="save" class="jinya-form--video" v-if="!(hideOnError && state === 'error')">
            <jinya-editor-pane>
                <video :poster="video.poster" :src="video.video" controls v-if="video.video || video.poster"></video>
            </jinya-editor-pane>
            <jinya-editor-pane>
                <jinya-input :enable="enable" :required="true"
                             :validation-message="'art.videos.video_form.name.empty'|jvalidator"
                             label="art.videos.video_form.name"
                             v-model="video.name"/>
                <jinya-file-input :enable="enable" @picked="posterPicked" label="art.videos.video_form.poster"
                                  v-model="video.poster"/>
                <jinya-tiny-mce :content="video.description" :enable="enable" height="300px"
                                label="art.videos.video_form.description" v-model="video.description"/>
            </jinya-editor-pane>
        </jinya-form>
    </jinya-editor>
</template>

<script>
  import JinyaForm from '@/framework/Markup/Form/Form';
  import JinyaInput from '@/framework/Markup/Form/Input';
  import JinyaButton from '@/framework/Markup/Button';
  import Routes from '@/router/Routes';
  import JinyaMessage from '@/framework/Markup/Validation/Message';
  import JinyaMessageActionBar from '@/framework/Markup/Validation/MessageActionBar';
  import JinyaEditor from '@/framework/Markup/Form/Editor';
  import JinyaEditorPane from '@/framework/Markup/Form/EditorPane';
  import JinyaTinyMce from '@/framework/Markup/Form/TinyMce';
  import JinyaFileInput from '@/framework/Markup/Form/FileInput';
  import FileUtils from '@/framework/IO/FileUtils';

  export default {
    components: {
      JinyaFileInput,
      JinyaTinyMce,
      JinyaInput,
      JinyaEditorPane,
      JinyaForm,
      JinyaButton,
      JinyaMessageActionBar,
      JinyaMessage,
      JinyaEditor,
    },
    name: 'jinya-video-form',
    props: {
      message: {
        type: String,
        default() {
          return '';
        },
      },
      state: {
        type: String,
        default() {
          return '';
        },
      },
      isStatic: {
        type: Boolean,
        default() {
          return false;
        },
      },
      enable: {
        type: Boolean,
        default() {
          return true;
        },
      },
      hideOnError: {
        type: Boolean,
        default() {
          return false;
        },
      },
      saveLabel: {
        type: String,
        default() {
          return 'art.videos.video_form.save';
        },
      },
      cancelLabel: {
        type: String,
        default() {
          return 'art.videos.video_form.back';
        },
      },
      video: {
        type: Object,
        default() {
          return {
            name: '',
            description: '',
            poster: '',
            uploadedPoster: undefined,
          };
        },
      },
    },
    methods: {
      back() {
        this.$router.push(Routes.Art.Videos.SavedInJinya.Overview);
      },
      async posterPicked(files) {
        const file = files.item(0);

        this.video.poster = await FileUtils.getAsDataUrl(file);
        this.video.uploadedPoster = file;
      },
      save() {
        const video = {
          name: this.video.name,
          description: this.video.description,
          poster: this.video.uploadedPoster,
        };

        this.$emit('save', video);
      },
    },
  };
</script>
