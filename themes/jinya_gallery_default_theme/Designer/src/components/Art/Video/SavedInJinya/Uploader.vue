<template>
    <jinya-loader v-if="loading" :loading="loading"/>
    <jinya-editor v-else class="jinya-video__uploader">
        <jinya-form save-label="art.videos.uploader.upload" cancel-label="art.videos.uploader.back"
                    @submit="queueUpload">
            <jinya-message :message="'art.videos.uploader.queued'|jmessage" state="success" v-if="uploadQueued"/>
            <jinya-editor-pane>
                <video :src="video.video" :poster="video.poster" v-if="video.video || video.poster" controls></video>
            </jinya-editor-pane>
            <jinya-editor-pane>
                <jinya-file-input :enable="true" accept="video/mp4" label="art.videos.uploader.file"
                                  @picked="filePicked"/>
            </jinya-editor-pane>
        </jinya-form>
    </jinya-editor>
</template>

<script>
  import JinyaFileInput from "@/framework/Markup/Form/FileInput";
  import JinyaButton from "@/framework/Markup/Button";
  import JinyaRequest from "@/framework/Ajax/JinyaRequest";
  import JinyaLoader from "@/framework/Markup/Waiting/Loader";
  import JinyaMessage from "@/framework/Markup/Validation/Message";
  import EventBus from "@/framework/Events/EventBus";
  import Events from "@/framework/Events/Events";
  import Timing from "@/framework/Utils/Timing";
  import Routes from "@/router/Routes";
  import JinyaEditor from "@/framework/Markup/Form/Editor";
  import JinyaForm from "@/framework/Markup/Form/Form";
  import DOMUtils from "@/framework/Utils/DOMUtils";
  import Translator from "@/framework/i18n/Translator";
  import JinyaEditorPane from "@/framework/Markup/Form/EditorPane";
  import FileUtils from "@/framework/IO/FileUtils";

  export default {
    name: "Uploader",
    components: {JinyaEditorPane, JinyaForm, JinyaEditor, JinyaMessage, JinyaLoader, JinyaButton, JinyaFileInput},
    data() {
      return {
        video: {
          name: '',
          slug: ''
        },
        loading: true,
        uploadQueued: false,
        videoFile: null
      };
    },
    async mounted() {
      this.loading = true;
      const video = await JinyaRequest.get(`/api/video/${this.$route.params.slug}`);
      this.video = video.item;
      DOMUtils.changeTitle(Translator.message('art.videos.uploader.title', this.video));
      EventBus.$emit(Events.header.change, Translator.message('art.videos.uploader.title', this.video));
      this.loading = false;
    },
    methods: {
      async queueUpload() {
        EventBus.$emit(Events.video.uploadStarted, {
          slug: this.video.slug,
          name: this.video.name,
          video: this.videoFile
        });
        this.uploadQueued = true;
        await Timing.wait();
        this.$router.push(Routes.Art.Videos.SavedInJinya.Overview);
      },
      async filePicked(files) {
        this.videoFile = files.item(0);
        this.video.video = await FileUtils.getAsDataUrl(this.videoFile);
      }
    }
  }
</script>

<style scoped lang="scss">
    .jinya-video__uploader {
        padding-top: 1rem;
    }
</style>