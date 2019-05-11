<template>
  <jinya-editor>
    <jinya-message :message="message" :state="state" v-if="state"></jinya-message>
    <jinya-form :cancel-label="cancelLabel" :enable="enable" :save-label="saveLabel" @back="back"
                @submit="save" class="jinya-form--video" v-if="!(hideOnError && state === 'error')">
      <jinya-editor-pane>
        <iframe :src="videoUrl" height="315" width="560"></iframe>
      </jinya-editor-pane>
      <jinya-editor-pane>
        <jinya-input :enable="enable" :required="true"
                     :validation-message="'art.videos.youtube.video_form.name.empty'|jvalidator"
                     @change="nameChanged" label="art.videos.youtube.video_form.name"
                     v-model="video.name"/>
        <jinya-input :enable="enable" :required="true"
                     :validation-message="'art.videos.youtube.video_form.slug.empty'|jvalidator"
                     @change="slugChanged" label="art.videos.youtube.video_form.slug"
                     v-model="video.slug"/>
        <jinya-input :enable="enable" :required="true"
                     :validation-message="'art.videos.youtube.video_form.video_key_or_url.empty'|jvalidator"
                     @change="videoKeyChanged" label="art.videos.youtube.video_form.video_key_or_url"
                     v-model="videoKeyOrUrl"/>
        <jinya-tiny-mce :content="video.description" :enable="enable" height="300px"
                        label="art.videos.youtube.video_form.description" v-model="video.description"/>
      </jinya-editor-pane>
    </jinya-form>
  </jinya-editor>
</template>

<script>
  import JinyaForm from '@/framework/Markup/Form/Form';
  import JinyaInput from '@/framework/Markup/Form/Input';
  import JinyaButton from '@/framework/Markup/Button';
  import slugify from 'slugify';
  import Routes from '@/router/Routes';
  import JinyaMessage from '@/framework/Markup/Validation/Message';
  import JinyaMessageActionBar from '@/framework/Markup/Validation/MessageActionBar';
  import JinyaEditor from '@/framework/Markup/Form/Editor';
  import JinyaEditorPane from '@/framework/Markup/Form/EditorPane';
  import JinyaTinyMce from '@/framework/Markup/Form/TinyMce';

  export default {
    components: {
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
          return 'art.videos.youtube.video_form.save';
        },
      },
      cancelLabel: {
        type: String,
        default() {
          return 'art.videos.youtube.video_form.back';
        },
      },
      video: {
        type: Object,
        default() {
          return {
            videoKey: '',
            name: '',
            slug: '',
            description: '',
          };
        },
      },
      slugifyEnabled: {
        type: Boolean,
        default() {
          return true;
        },
      },
    },
    watch: {
      video(newValue) {
        this.videoKeyOrUrl = newValue.videoKey;
      },
    },
    computed: {
      videoUrl() {
        return `https://www.youtube-nocookie.com/embed/${this.video.videoKey}?rel=0`;
      },
    },
    data() {
      return {
        videoKeyOrUrl: '',
      };
    },
    methods: {
      back() {
        this.$router.push(Routes.Art.Videos.SavedOnYoutube.Overview);
      },
      nameChanged(value) {
        if (this.slugifyEnabled) {
          this.video.slug = slugify(value);
        }
      },
      slugChanged(value) {
        this.slugifyEnabled = false;
        this.video.slug = slugify(value);
      },
      videoKeyChanged(value) {
        this.video.videoKey = value
          .replace(/https:\/\/(www\.youtube\.com\/(watch\?v=|embed)|youtu\.be\/)/g, '')
          .replace(/&.*/g, '');
      },
      save() {
        const video = {
          name: this.video.name,
          slug: this.video.slug,
          videoKey: this.video.videoKey,
          description: this.video.description,
        };

        this.$emit('save', video);
      },
    },
  };
</script>
