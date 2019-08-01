<template>
    <jinya-video-form :enable="enable" :message="message" :state="state" :video="video" @save="save"/>
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
          name: '',
          description: '',
        },
      };
    },
    name: 'edit',
    async mounted() {
      this.state = 'loading';
      this.enable = false;
      this.message = Translator.message('art.videos.details.loading');
      try {
        const video = await JinyaRequest.get(`/api/video/jinya/${this.$route.params.slug}`);
        this.video = video.item;
        this.state = '';
        this.enable = true;
        DOMUtils.changeTitle(Translator.message('art.videos.edit.title', this.video));
        EventBus.$emit(Events.header.change, Translator.message('art.videos.edit.title', this.video));
      } catch (error) {
        this.state = 'error';
        this.message = Translator.validator(`art.videos.${error.message}`);
      }
    },
    methods: {
      async save(video) {
        try {
          this.enable = false;
          this.state = 'loading';
          this.message = Translator.message('art.videos.edit.saving', { name: video.name });

          await JinyaRequest.put(`/api/video/jinya/${this.$route.params.slug}`, {
            name: video.name,
            description: video.description,
          });

          if (video.poster) {
            this.message = Translator.message('art.videos.edit.upload_poster', video);
            await JinyaRequest.upload(`/api/video/jinya/${this.video.slug}/poster`, video.poster);
          }

          this.state = 'success';
          this.message = Translator.message('art.videos.edit.success', { name: video.name });

          await Timing.wait();
          this.$router.push({
            name: Routes.Art.Videos.SavedInJinya.Details.name,
            params: {
              slug: this.video.slug,
            },
          });
        } catch (error) {
          this.message = `art.videos.${error.message}`;
          this.state = 'error';
          this.enable = true;
        }
      },
    },
  };
</script>
