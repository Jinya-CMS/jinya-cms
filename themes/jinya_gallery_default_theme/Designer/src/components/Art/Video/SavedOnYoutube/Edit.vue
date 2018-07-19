<template>
    <jinya-video-form :video="video" @save="save" :enable="enable" :message="message" :state="state"/>
</template>

<script>
  import JinyaVideoForm from './VideoForm';
  import JinyaRequest from '@/framework/Ajax/JinyaRequest';
  import Translator from '@/framework/i18n/Translator';
  import Routes from '@/router/Routes';
  import Timing from '@/framework/Utils/Timing';
  import DOMUtils from '@/framework/Utils/DOMUtils';
  import Events from '@/framework/Events/Events';
  import EventBus from '@/framework/Events/EventBus';

  // noinspection JSUnusedGlobalSymbols
  export default {
    components: {
      JinyaVideoForm,
    },
    data() {
      return {
        message: '',
        state: '',
        loading: false,
        enable: false,
        video: {
          videoKey: '',
          name: '',
          slug: '',
          description: '',
        },
      };
    },
    name: 'edit',
    async mounted() {
      this.state = 'loading';
      this.enable = false;
      this.message = Translator.message('art.videos.youtube.details.loading');
      try {
        const video = await JinyaRequest.get(`/api/video/youtube/${this.$route.params.slug}`);
        this.video = video.item;
        this.state = '';
        this.enable = true;
        DOMUtils.changeTitle(Translator.message('art.videos.youtube.edit.title', this.video));
        EventBus.$emit(Events.header.change, Translator.message('art.videos.youtube.edit.title', this.video));
      } catch (error) {
        this.state = 'error';
        this.message = Translator.validator(`art.videos.youtube.${error.message}`);
      }
    },
    methods: {
      async save(video) {
        try {
          this.enable = false;
          this.state = 'loading';
          this.message = Translator.message('art.videos.youtube.edit.saving', { name: video.name });

          await JinyaRequest.put(`/api/video/youtube/${this.$route.params.slug}`, {
            name: video.name,
            slug: video.slug,
            videoKey: video.videoKey,
            description: video.description,
          });

          this.state = 'success';
          this.message = Translator.message('art.videos.youtube.edit.success', { name: video.name });

          await Timing.wait();
          this.$router.push({
            name: Routes.Art.Videos.SavedOnYoutube.Details.name,
            params: {
              slug: this.video.slug,
            },
          });
        } catch (error) {
          this.message = error.message;
          this.state = 'error';
          this.enable = true;
        }
      },
    },
  };
</script>
