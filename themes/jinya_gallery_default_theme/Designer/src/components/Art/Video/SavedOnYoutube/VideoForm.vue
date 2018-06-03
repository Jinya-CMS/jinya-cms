<template>
    <jinya-editor>
        <jinya-message :message="message" :state="state" v-if="state">
            <jinya-message-action-bar class="jinya-message__action-bar" v-if="state === 'error'">
                <jinya-button label="art.videos.video_form.back" to="Art.Videos.SavedOnYoutube.Overview"
                              :is-danger="true"/>
                <jinya-button label="art.videos.video_form.search" to="Art.Videos.SavedOnYoutube.Overview"
                              :query="{keyword: $route.params.slug}" :is-secondary="true"/>
            </jinya-message-action-bar>
        </jinya-message>
        <jinya-form v-if="!(hideOnError && state === 'error')" @submit="save" class="jinya-form--video" @back="back"
                    :enable="enable" :cancel-label="cancelLabel" :save-label="saveLabel">
            <jinya-editor-pane>
                <jinya-editor-preview-image :src="video.picture"/>
            </jinya-editor-pane>
            <jinya-editor-pane>
                <jinya-input :static="static" :enable="enable" label="art.videos.video_form.name"
                             v-model="video.name" @change="nameChanged"/>
                <jinya-input :static="static" :enable="enable" label="art.videos.video_form.slug"
                             v-model="video.slug" @change="slugChanged"/>
                <jinya-file-input v-if="!static" :enable="enable" accept="image/*" @picked="picturePicked"
                                  label="art.videos.video_form.video"/>
                <jinya-textarea :static="static" :enable="enable" label="art.videos.video_form.description"
                                v-model="video.description"/>
            </jinya-editor-pane>
            <template slot="buttons">
                <slot name="buttons"/>
            </template>
        </jinya-form>
    </jinya-editor>
</template>

<script>
  import JinyaForm from "@/framework/Markup/Form/Form";
  import JinyaInput from "@/framework/Markup/Form/Input";
  import JinyaButton from "@/framework/Markup/Button";
  import JinyaFileInput from "@/framework/Markup/Form/FileInput";
  import FileUtils from "@/framework/IO/FileUtils";
  import JinyaTextarea from "@/framework/Markup/Form/Textarea";
  import slugify from "slugify";
  import Routes from "../../../../router/Routes";
  import JinyaMessage from "@/framework/Markup/Validation/Message";
  import JinyaMessageActionBar from "@/framework/Markup/Validation/MessageActionBar";
  import JinyaEditor from "@/framework/Markup/Form/Editor";
  import JinyaEditorPreviewImage from "@/framework/Markup/Form/EditorPreviewImage";
  import JinyaEditorPane from "@/framework/Markup/Form/EditorPane";

  export default {
    components: {
      JinyaTextarea,
      JinyaFileInput,
      JinyaInput,
      JinyaEditorPreviewImage,
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
            picture: '',
            name: '',
            slug: '',
            description: ''
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
        this.$router.push(Routes.Art.Videos.SavedOnYoutube.Overview);
      },
      async picturePicked(files) {
        const file = files.item(0);

        this.video.picture = await FileUtils.getAsDataUrl(file);
        this.video.uploadedFile = file;
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
      save() {
        const video = {
          name: this.video.name,
          slug: this.video.slug,
          picture: this.video.uploadedFile,
          description: this.video.description
        };

        this.$emit('save', video)
      }
    }
  }
</script>
