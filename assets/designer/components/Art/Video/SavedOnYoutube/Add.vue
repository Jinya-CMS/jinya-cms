<template>
  <jinya-youtube-video-form @save="save" :enable="enable" :message="message" :state="state"/>
</template>

<script>
  import JinyaYoutubeVideoForm from '@/components/Art/Video/SavedOnYoutube/YoutubeVideoForm';
  import JinyaRequest from '@/framework/Ajax/JinyaRequest';
  import Translator from '@/framework/i18n/Translator';
  import Routes from '@/router/Routes';
  import Timing from '@/framework/Utils/Timing';

  export default {
    components: {
      JinyaYoutubeVideoForm,
    },
    data() {
      return {
        message: '',
        state: '',
        loading: false,
        enable: true,
      };
    },
    name: 'add',
    methods: {
      async save(video) {
        try {
          this.enable = false;
          this.state = 'loading';
          this.message = Translator.message('art.videos.youtube.add.saving', { name: video.name });

          await JinyaRequest.post('/api/video/youtube', {
            name: video.name,
            slug: video.slug,
            description: video.description,
            videoKey: video.videoKey,
          });

          this.state = 'success';
          this.message = Translator.message('art.videos.youtube.add.success', { name: video.name });

          await Timing.wait();
          this.$router.push(Routes.Art.Videos.SavedOnYoutube.Overview);
        } catch (error) {
          this.message = `art.videos.youtube.${error.message}`;
          this.state = 'error';
          this.enable = true;
        }
      },
    },
  };
</script>
