<template>
    <jinya-video-form @save="save" :enable="enable" :message="message" :state="state"/>
</template>

<script>
  import JinyaVideoForm from "@/components/Art/Video/SavedInJinya/VideoForm";
  import JinyaRequest from "@/framework/Ajax/JinyaRequest";
  import Translator from "@/framework/i18n/Translator";
  import Routes from "@/router/Routes";
  import Timing from "@/framework/Utils/Timing";

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
        try {
          this.enable = false;
          this.state = 'loading';
          this.message = Translator.message('art.videos.add.saving', video);

          await JinyaRequest.post('/api/video', {
            name: video.name,
            slug: video.slug,
            description: video.description
          });

          if (video.poster) {
            this.message = Translator.message('art.videos.add.upload_poster', video);
            await JinyaRequest.upload(`/api/video/${video.slug}/poster`, video.poster);
          }

          this.state = 'success';
          this.message = Translator.message('art.videos.add.success', video);

          await Timing.wait();
          this.$router.push(Routes.Art.Videos.SavedInJinya.Overview);
        } catch (error) {
          this.message = error.message;
          this.state = 'error';
          this.enable = true;
        }
      }
    }
  }
</script>
