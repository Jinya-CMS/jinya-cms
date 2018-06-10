<template>
    <jinya-progress-bar v-if="uploading" class="jinya-video-uploader__indicator"/>
</template>

<script>
  import Lockr from 'lockr';
  import EventBus from "@/framework/Events/EventBus";
  import Events from "@/framework/Events/Events";
  import Translator from "@/framework/i18n/Translator";
  // noinspection ES6CheckImport
  import VideoUploader from "@/worker/VideoUploader";
  import JinyaProgressBar from "@/framework/Markup/Waiting/ProgressBar";

  export default {
    name: "jinya-video-uploader",
    components: {JinyaProgressBar},
    data() {
      return {
        uploading: false
      };
    },
    created() {
      EventBus.$on(Events.video.uploadStarted, async data => {
        if (data.video?.type?.toLowerCase() === 'video/mp4') {
          await Notification.requestPermission();
          const allowNotification = Notification.permission === 'granted';
          const worker = new VideoUploader();
          const workerData = {
            video: data.video,
            slug: data.slug,
            name: data.name,
            apiKey: Lockr.get('JinyaApiKey')
          };

          const title = window.options.pageTitle;
          const icon = window.options.favicon;

          worker.postMessage(workerData);
          worker.onmessage = ev => {
            const message = Translator.message(ev.data.message, workerData);

            if (ev.data.started) this.uploading = true;
            if (ev.data.finished) this.uploading = false;

            if (allowNotification) {
              const notify = new Notification(title, {
                body: message,
                icon: icon
              });
            } else {
              alert(message);
            }
          };
          worker.onerror = ev => {
            const message = ev.message;
            console.error(message);
            if (allowNotification) {
              const notify = new Notification(title, {
                body: Translator.validator(message),
                icon: icon
              });
            } else {
              alert(Translator.validator(message));
            }
          }
        }
      });
    }
  }
</script>

<style scoped lang="scss">
    .jinya-video-uploader__indicator {
        position: fixed;
        bottom: 0;
        width: 100%;
    }
</style>