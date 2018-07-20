<template>
    <jinya-editor>
        <jinya-message :message="message" :state="state" v-if="state">
            <jinya-message-action-bar class="jinya-message__action-bar" v-if="state === 'error'">
                <jinya-button label="art.videos.youtube.video_form.back" to="Art.Videos.SavedOnYoutube.Overview"
                              :is-danger="true"/>
                <jinya-button label="art.videos.youtube.video_form.search" to="Art.Videos.SavedOnYoutube.Overview"
                              :query="{keyword: $route.params.slug}" :is-secondary="true"/>
            </jinya-message-action-bar>
        </jinya-message>
        <jinya-form v-if="!(hideOnError && state === 'error')" @submit="save" class="jinya-form--video" @back="back"
                    :enable="enable" :cancel-label="cancelLabel" :save-label="saveLabel">
            <jinya-editor-pane>
                <iframe :src="videoUrl" frameborder="0" width="560" height="315"></iframe>
            </jinya-editor-pane>
            <jinya-editor-pane>
                <jinya-input :enable="enable" label="art.videos.youtube.video_form.name" v-model="video.name"
                             @change="nameChanged"/>
                <jinya-input :enable="enable" label="art.videos.youtube.video_form.slug" v-model="video.slug"
                             @change="slugChanged"/>
                <jinya-input :enable="enable" label="art.videos.youtube.video_form.video_key_or_url"
                             v-model="videoKeyOrUrl" @change="videoKeyChanged"/>
                <jinya-tiny-mce :enable="enable" label="art.videos.youtube.video_form.description" height="300px"
                                v-model="video.description" :content="video.description"/>
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
