<template>
    <jinya-video-form @save="save" :enable="enable" :message="message" :state="state"/>
</template>

<script>
  import JinyaVideoForm from "@/components/Art/Videos/SavedOnYoutube/VideoForm";
  import JinyaRequest from "@/framework/Ajax/JinyaRequest";
  import Translator from "@/framework/i18n/Translator";
  import Routes from "@/router/Routes";
  import Timing from "@/framework/Utils/Timing";

  // noinspection JSUnusedGlobalSymbols
  export default {
    components: {
      JinyaVideoForm
    },
    data() {
      return {
        message: '',
        state: '',
        loading: false,
        enable: true
      }
    },
    name: "add",
    methods: {
      async save(video) {
        const picture = video.picture;
        try {
          this.enable = false;
          this.state = 'loading';
          this.message = Translator.message('art.videos.add.saving', {name: video.name});

          await JinyaRequest.post('/api/video', {
            name: video.name,
            slug: video.slug,
            description: video.description
          });

          this.message = Translator.message('art.videos.youtube.add.uploading', {name: video.name});
          await JinyaRequest.upload(`/api/video/youtube/${video.slug}/picture`, picture);

          this.state = 'success';
          this.message = Translator.message('art.videos.youtube.add.success', {name: video.name});

          await Timing.wait();
          this.$router.push(Routes.Art.Videos.SavedOnYoutube.Overview);
        } catch (error) {
          this.message = error.message;
          this.state = 'error';
          this.enable = true;
        }
      }
    }
  }
</script>
