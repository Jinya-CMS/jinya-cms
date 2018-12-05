<template>
  <div class="jinya-video__youtube">
    <jinya-message :message="message" :state="state" v-if="state"/>
    <div class="jinya-video__video-details" v-else>
      <iframe :src="`https://www.youtube-nocookie.com/embed/${video.videoKey}`" frameborder="0" width="960"
              height="540" class="jinya-video__video--youtube"></iframe>
      <div class="jinya-video__video-description" v-html="video.description"></div>
      <jinya-floating-action-button icon="pencil" @click="edit"/>
    </div>
  </div>
</template>

<script>
  import JinyaRequest from '@/framework/Ajax/JinyaRequest';
  import Routes from '@/router/Routes';
  import Translator from '@/framework/i18n/Translator';
  import DOMUtils from '@/framework/Utils/DOMUtils';
  import JinyaMessage from '@/framework/Markup/Validation/Message';
  import JinyaFloatingActionButton from '@/framework/Markup/FloatingActionButton';
  import EventBus from '@/framework/Events/EventBus';
  import Events from '@/framework/Events/Events';

  export default {
    components: {
      JinyaFloatingActionButton,
      JinyaMessage,
    },
    name: 'details',
    data() {
      return {
        message: '',
        state: '',
        video: {
          background: '',
          name: '',
          slug: '',
          description: '',
        },
        overviewRoute: Routes.Art.Videos.SavedOnYoutube.Overview.name,
      };
    },
    async mounted() {
      this.state = 'loading';
      this.message = Translator.message('art.videos.youtube.details.loading');
      try {
        const video = await JinyaRequest.get(`/api/video/youtube/${this.$route.params.slug}`);
        this.video = video.item;
        this.state = '';
        EventBus.$emit(Events.header.change, this.video.name);
        DOMUtils.changeTitle(this.video.name);
      } catch (error) {
        this.state = 'error';
        this.message = Translator.validator(`art.videos.youtube.${error.message}`);
      }
    },
    methods: {
      edit() {
        this.$router.push({
          name: Routes.Art.Videos.SavedOnYoutube.Edit.name,
          params: {
            slug: this.video.slug,
          },
        });
      },
    },
  };
</script>

<style lang="scss">
  .jinya-video__youtube {
    padding-top: 1rem;
  }

  .jinya-video__video-details {
    display: flex;
  }

  .jinya-video__video-description {
    margin-left: 1rem;
  }

  .jinya-video__video {
    &.jinya-video__video--youtube {
      width: 960px;
    }
  }
</style>
