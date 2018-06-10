<template>
    <jinya-loader v-if="loading" :loading="loading"/>
    <div v-else class="jinya-video__uploader">
        <jinya-message :message="'art.videos.uploader.queued'|jmessage" state="success"
                       v-if="uploadQueued"></jinya-message>
        <h1>{{'art.videos.uploader.title'|jmessage(video)}}</h1>
        <jinya-file-input :enable="true" accept="video/mp4" label="art.videos.uploader.file"
                          @picked="videoFile = $event.item(0)"/>
        <jinya-button :is-primary="true" label="art.videos.uploader.upload" @click="queueUpload"/>
    </div>
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

  export default {
    name: "Uploader",
    components: {JinyaMessage, JinyaLoader, JinyaButton, JinyaFileInput},
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
      }
    }
  }
</script>

<style scoped lang="scss">
    .jinya-video__uploader {
        padding-top: 1rem;
    }
</style>