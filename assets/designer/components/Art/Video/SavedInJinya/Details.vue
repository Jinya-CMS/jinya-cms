<template>
    <div class="jinya-video__jinya">
        <jinya-message :message="message" :state="state" v-if="state"/>
        <div class="jinya-video__video-details" v-else>
            <video :poster="video.poster" :src="video.video" class="jinya-video__video jinya-video__video--jinya"
                   controls></video>
            <div class="jinya-video__video-description" v-html="video.description"></div>
            <jinya-floating-action-button @click="edit" icon="pencil"/>
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
        overviewRoute: Routes.Art.Videos.SavedInJinya.Overview.name,
      };
    },
    async mounted() {
      this.state = 'loading';
      this.message = Translator.message('art.videos.details.loading');
      try {
        const video = await JinyaRequest.get(`/api/video/jinya/${this.$route.params.slug}`);
        this.video = video.item;
        this.state = '';
        EventBus.$emit(Events.header.change, this.video.name);
        DOMUtils.changeTitle(this.video.name);
      } catch (error) {
        this.state = 'error';
        this.message = Translator.validator(`art.videos.${error.message}`);
      }
    },
    methods: {
      edit() {
        this.$router.push({
          name: Routes.Art.Videos.SavedInJinya.Edit.name,
          params: {
            slug: this.video.slug,
          },
        });
      },
    },
  };
</script>

<style lang="scss">
    .jinya-video__jinya {
        padding-top: 1rem;
    }

    .jinya-video__video-details {
        display: flex;
    }

    .jinya-video__video-description {
        margin-left: 1rem;
        width: 50%;
    }

    .jinya-video__video {
        &.jinya-video__video--jinya {
            width: 50%;
        }
    }
</style>
