<template>
    <div class="jinya-video-overview">
        <jinya-loader :loading="loading"/>
        <jinya-card-list :nothing-found="nothingFound" v-if="!loading">
            <jinya-card :header="video.name" v-for="video in videos" v-if="!loading" :key="video.slug">
                <iframe class="jinya-video__youtube" :src="`https://www.youtube-nocookie.com/embed/${video.videoKey}`"
                        frameborder="0"></iframe>
                <jinya-card-button :to="{name: detailsRoute, params: {slug: video.slug}}" slot="footer" icon="monitor"
                                   type="details"/>
                <jinya-card-button :to="{name: editRoute, params: {slug: video.slug}}" slot="footer" icon="pencil"
                                   type="edit"/>
                <!--suppress JSUnnecessarySemicolon -->
                <jinya-card-button @click="showDeleteModal(video)" slot="footer" icon="delete" type="delete"/>
            </jinya-card>
        </jinya-card-list>
        <jinya-pager @previous="load(control.previous)" @next="load(control.next)" v-if="!loading" :offset="offset"
                     :count="count"/>
        <jinya-modal @close="closeDeleteModal()" title="art.videos.youtube.delete.title" v-if="this.delete.show"
                     :loading="this.delete.loading">
            <jinya-message :message="this.delete.error" state="error" v-if="this.delete.error && !this.delete.loading"
                           slot="message"/>
            {{'art.videos.youtube.delete.content'|jmessage({video: selectedVideo.name})}}
            <jinya-modal-button :is-secondary="true" slot="buttons-left" label="art.videos.youtube.delete.no"
                                :closes-modal="true" :is-disabled="this.delete.loading"/>
            <jinya-modal-button :is-danger="true" slot="buttons-right" label="art.videos.youtube.delete.yes"
                                @click="remove" :is-disabled="this.delete.loading"/>
        </jinya-modal>
        <jinya-floating-action-button v-if="!loading" :is-primary="true" icon="plus" :to="addRoute"/>
    </div>
</template>

<script>
  import JinyaRequest from '@/framework/Ajax/JinyaRequest';
  import JinyaCardList from '@/framework/Markup/Listing/Card/CardList';
  import JinyaCard from '@/framework/Markup/Listing/Card/Card';
  import JinyaPager from '@/framework/Markup/Listing/Pager';
  import JinyaCardButton from '@/framework/Markup/Listing/Card/CardButton';
  import JinyaModal from '@/framework/Markup/Modal/Modal';
  import JinyaModalButton from '@/framework/Markup/Modal/ModalButton';
  import Translator from '@/framework/i18n/Translator';
  import JinyaMessage from '@/framework/Markup/Validation/Message';
  import JinyaLoader from '@/framework/Markup/Waiting/Loader';
  import EventBus from '@/framework/Events/EventBus';
  import Events from '@/framework/Events/Events';
  import Routes from '@/router/Routes';
  import JinyaFloatingActionButton from '@/framework/Markup/FloatingActionButton';

  export default {
    components: {
      JinyaFloatingActionButton,
      JinyaLoader,
      JinyaMessage,
      JinyaModalButton,
      JinyaModal,
      JinyaCardButton,
      JinyaPager,
      JinyaCard,
      JinyaCardList,
    },
    name: 'jinya-videos-saved-on-youtube-overview',
    computed: {
      addRoute() {
        return Routes.Art.Videos.SavedOnYoutube.Add;
      },
    },
    methods: {
      load(target) {
        const url = new URL(target, location.href);

        this.$router.push({
          name: Routes.Art.Videos.SavedOnYoutube.Overview.name,
          query: {
            offset: url.searchParams.get('offset'),
            count: url.searchParams.get('count'),
            keyword: url.searchParams.get('keyword'),
          },
        });
      },
      async fetchVideos(offset = 0, count = 10, keyword = '') {
        this.loading = true;
        this.currentUrl = `/api/video/youtube?offset=${offset}&count=${count}&keyword=${keyword}`;

        const value = await JinyaRequest.get(this.currentUrl);
        this.videos = value.items;
        this.control = value.control;
        this.count = value.count;
        this.offset = value.offset;
        this.loading = false;
      },
      selectVideo(video) {
        this.selectedVideo = video;
      },
      async remove() {
        this.delete.loading = true;
        try {
          await JinyaRequest.delete(`/api/video/youtube/${this.selectedVideo.slug}`);
          this.delete.show = false;
          const url = new URL(location.href);
          await this.fetchVideos(0, 10, url.searchParams.get('keyword'));
        } catch (reason) {
          this.delete.error = Translator.validator(`art.videos.youtube.overview.delete.${reason.message}`);
        }
        this.delete.loading = false;
      },
      showDeleteModal(video) {
        this.selectVideo(video);
        this.delete.show = true;
      },
      closeDeleteModal() {
        this.delete.show = false;
        this.delete.loading = false;
        this.delete.error = '';
      },
    },
    async mounted() {
      const offset = this.$route.query.offset || 0;
      const count = this.$route.query.count || 10;
      const keyword = this.$route.query.keyword || '';
      await this.fetchVideos(offset, count, keyword);

      EventBus.$on(Events.search.triggered, (value) => {
        this.$router.push({
          name: Routes.Art.Videos.SavedOnYoutube.Overview.name,
          query: {
            offset: 0,
            count: this.$route.query.count,
            keyword: value.keyword,
          },
        });
      });
    },
    beforeDestroy() {
      EventBus.$off(Events.search.triggered);
    },
    async beforeRouteUpdate(to, from, next) {
      await this.fetchVideos(to.query.offset || 0, to.query.count || 10, to.query.keyword || '');
      next();
    },
    data() {
      return {
        videos: [],
        control: { next: false, previous: false },
        count: 0,
        offset: 0,
        loading: true,
        keyword: '',
        selectedVideo: {},
        delete: {
          error: '',
          show: false,
          loading: false,
        },
        editRoute: Routes.Art.Videos.SavedOnYoutube.Edit.name,
        detailsRoute: Routes.Art.Videos.SavedOnYoutube.Details.name,
        nothingFound: this.$route.query.keyword ? 'art.videos.youtube.overview.nothing_found' : 'art.videos.youtube.overview.no_videos',
      };
    },
  };
</script>

<style scoped lang="scss">
    .jinya-video__youtube {
        width: 100%;
        height: 100%;
    }
</style>
