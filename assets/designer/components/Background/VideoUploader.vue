<template>
  <div>
    <jinya-progress-bar class="jinya-video-uploader__indicator" v-if="uploading"/>
    <jinya-modal @close="showReupload = false" title="background.video.exists.title" v-if="showReupload">
      <span>{{reuploadMessage}}</span>
      <jinya-modal-button :closes-modal="true" :is-success="true" @click="reupload"
                          label="background.video.exists.reupload" slot="buttons-right"></jinya-modal-button>
      <jinya-modal-button :closes-modal="true" :is-secondary="true" label="background.video.exists.cancel"
                          slot="buttons-left"></jinya-modal-button>
    </jinya-modal>
  </div>
</template>

<script>
  import EventBus from '@/framework/Events/EventBus';
  import Events from '@/framework/Events/Events';
  import Translator from '@/framework/i18n/Translator';
  import VideoUploader from '@/worker/VideoUploader';
  import JinyaProgressBar from '@/framework/Markup/Waiting/ProgressBar';
  import JinyaModal from '@/framework/Markup/Modal/Modal';
  import JinyaModalButton from '@/framework/Markup/Modal/ModalButton';
  import JinyaRequest from '@/framework/Ajax/JinyaRequest';
  import { getApiKey } from '@/framework/Storage/AuthStorage';
  import Favicon from '@/img/favicon.ico';

  export default {
    name: 'jinya-video-uploader',
    components: { JinyaModalButton, JinyaModal, JinyaProgressBar },
    data() {
      return {
        uploading: false,
        showReupload: false,
        currentWorkerData: {
          name: '',
        },
      };
    },
    computed: {
      reuploadMessage() {
        return Translator.message('background.video.exists.message', this.currentWorkerData);
      },
    },
    created() {
      EventBus.$on(Events.video.uploadStarted, async (data) => {
        if (data.video?.type?.toLowerCase() === 'video/mp4') {
          await Notification.requestPermission();
          const allowNotification = Notification.permission === 'granted';
          const worker = new VideoUploader();
          const workerData = {
            video: data.video,
            slug: data.slug,
            name: data.name,
            apiKey: getApiKey(),
          };

          const title = window.messages.app.name;

          worker.postMessage(workerData);
          worker.onmessage = (ev) => {
            if (ev.data.error) {
              this.showReupload = true;
              this.currentWorker = worker;
              this.currentWorkerData = workerData;
            } else {
              const message = Translator.message(ev.data.message, workerData);

              if (ev.data.started) this.uploading = true;
              if (ev.data.finished) this.uploading = false;

              if (allowNotification) {
                // eslint-disable-next-line no-new
                new Notification(title, {
                  body: message,
                  Favicon,
                });
              } else {
                // eslint-disable-next-line no-alert
                alert(message);
              }
            }
          };
          worker.onerror = (ev) => {
            const { message } = ev;
            // eslint-disable-next-line no-console
            console.error(message);
            this.uploading = false;

            if (allowNotification) {
              // eslint-disable-next-line no-new
              new Notification(title, {
                body: Translator.validator(message),
                Favicon,
              });
            } else {
              // eslint-disable-next-line no-alert
              alert(Translator.validator(message));
            }
          };
        }
      });
    },
    methods: {
      async reupload() {
        await JinyaRequest.delete(`/api/video/jinya/${this.currentWorkerData.slug}/video/state`);
        this.currentWorker.postMessage(this.currentWorkerData);
        delete this.currentWorker;
        delete this.currentWorkerData;
      },
    },
  };
</script>

<style lang="scss" scoped>
  .jinya-video-uploader__indicator {
    position: fixed;
    bottom: 0;
    width: 100%;
  }
</style>
