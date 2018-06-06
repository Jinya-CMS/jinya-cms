<template>
    <jinya-editor>
        <jinya-message :message="message" :state="state" v-if="state">
            <jinya-message-action-bar class="jinya-message__action-bar" v-if="state === 'error'">
                <jinya-button label="art.videos.video_form.back" to="Art.Videos.SavedInJinya.Overview"
                              :is-danger="true"/>
                <jinya-button label="art.videos.video_form.search" to="Art.Videos.SavedInJinya.Overview"
                              :query="{keyword: $route.params.slug}" :is-secondary="true"/>
            </jinya-message-action-bar>
        </jinya-message>
        <jinya-form v-if="!(hideOnError && state === 'error')" @submit="save" class="jinya-form--video" @back="back"
                    :enable="enable" :cancel-label="cancelLabel" :save-label="saveLabel">
            <jinya-editor-pane>
                <video :src="video.video" :poster="video.poster" v-if="video.video || video.poster"></video>
            </jinya-editor-pane>
            <jinya-editor-pane>
                <jinya-input :enable="enable" label="art.videos.video_form.name" v-model="video.name"
                             @change="nameChanged"/>
                <jinya-input :enable="enable" label="art.videos.video_form.slug" v-model="video.slug"
                             @change="slugChanged"/>
                <jinya-file-input :enable="enable" label="art.videos.video_form.poster" v-model="video.poster"
                                  @picked="posterPicked"/>
                <jinya-tiny-mce :enable="enable" label="art.videos.video_form.description" height="300px"
                                v-model="video.description" :content="video.description"/>
            </jinya-editor-pane>
        </jinya-form>
    </jinya-editor>
</template>

<script>
  import JinyaForm from "@/framework/Markup/Form/Form";
  import JinyaInput from "@/framework/Markup/Form/Input";
  import JinyaButton from "@/framework/Markup/Button";
  import slugify from "slugify";
  import Routes from "@/router/Routes";
  import JinyaMessage from "@/framework/Markup/Validation/Message";
  import JinyaMessageActionBar from "@/framework/Markup/Validation/MessageActionBar";
  import JinyaEditor from "@/framework/Markup/Form/Editor";
  import JinyaEditorPane from "@/framework/Markup/Form/EditorPane";
  import JinyaTinyMce from "@/framework/Markup/Form/TinyMce";
  import JinyaFileInput from "@/framework/Markup/Form/FileInput";
  import FileUtils from "@/framework/IO/FileUtils";

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
      JinyaEditor
    },
    name: "jinya-video-form",
    props: {
      message: {
        type: String,
        default() {
          return '';
        }
      },
      state: {
        type: String,
        default() {
          return '';
        }
      },
      static: {
        type: Boolean,
        default() {
          return false;
        }
      },
      enable: {
        type: Boolean,
        default() {
          return true;
        }
      },
      hideOnError: {
        type: Boolean,
        default() {
          return false;
        }
      },
      saveLabel: {
        type: String,
        default() {
          return 'art.videos.video_form.save';
        }
      },
      cancelLabel: {
        type: String,
        default() {
          return 'art.videos.video_form.back';
        }
      },
      video: {
        type: Object,
        default() {
          return {
            name: '',
            slug: '',
            description: '',
            poster: '',
            uploadedPoster: undefined
          };
        }
      },
      slugifyEnabled: {
        type: Boolean,
        default() {
          return true;
        }
      }
    },
    methods: {
      back() {
        this.$router.push(Routes.Art.Videos.SavedInJinya.Overview);
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
      async posterPicked(files) {
        const file = files.item(0);

        this.video.poster = await FileUtils.getAsDataUrl(file);
        this.video.uploadedPoster = file;
      },
      save() {
        const video = {
          name: this.video.name,
          slug: this.video.slug,
          description: this.video.description,
          poster: this.video.uploadedPoster
        };

        this.$emit('save', video)
      }
    }
  }
</script>
