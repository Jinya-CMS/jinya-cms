<template>
    <div>
        <jinya-progress-bar v-if="uploading" class="jinya-video-uploader__indicator"/>
        <jinya-modal title="background.video.exists.title" @close="showReupload = false" v-if="showReupload">
            <span>{{reuploadMessage}}</span>
            <jinya-modal-button slot="buttons-right" :closes-modal="true" @click="reupload"
                                label="background.video.exists.reupload" :is-success="true"></jinya-modal-button>
            <jinya-modal-button slot="buttons-left" :closes-modal="true" label="background.video.exists.cancel"
                                :is-secondary="true"></jinya-modal-button>
        </jinya-modal>
    </div>
</template>

<script>
  import Lockr from 'lockr';
  import EventBus from "@/framework/Events/EventBus";
  import Events from "@/framework/Events/Events";
  import Translator from "@/framework/i18n/Translator";
  // noinspection ES6CheckImport
  import VideoUploader from "@/worker/VideoUploader";
  import JinyaProgressBar from "@/framework/Markup/Waiting/ProgressBar";
  import JinyaModal from "@/framework/Markup/Modal/Modal";
  import JinyaModalButton from "@/framework/Markup/Modal/ModalButton";
  import JinyaRequest from "@/framework/Ajax/JinyaRequest";

  export default {
    name: "jinya-video-uploader",
    components: {JinyaModalButton, JinyaModal, JinyaProgressBar},
    data() {
      return {
        uploading: false,
        showReupload: false,
        currentWorkerData: {
          name: ''
        }
      };
    },
    computed: {
      reuploadMessage() {
        return Translator.message('background.video.exists.message', this.currentWorkerData);
      }
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
            if (ev.data.error) {
              this.showReupload = true;
              this.currentWorker = worker;
              this.currentWorkerData = workerData;
            } else {
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
            }
          };
          worker.onerror = ev => {
            const message = ev.message;
            console.error(message);
            this.uploading = false;

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
    },
    methods: {
      async reupload() {
        await JinyaRequest.delete(`/api/video/${this.currentWorkerData.slug}/video/state`);
        this.currentWorker.postMessage(this.currentWorkerData);
        delete this.currentWorker;
        delete this.currentWorkerData;
      }
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