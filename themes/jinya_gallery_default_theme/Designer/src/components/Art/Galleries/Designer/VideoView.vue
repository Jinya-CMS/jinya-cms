<template>
    <div class="jinya-video-view">
        <jinya-input placeholder="art.galleries.designer.video_view.search" type="search" @keyup="search"
                     v-if="!initial" v-model="keyword" class="jinya-input--video-search"/>
        <jinya-card-list nothing-found="art.galleries.designer.video_view.nothing_found" v-infinite-scroll="loadMore"
                         v-show="!initial" infinite-scroll-disabled="loading" infinite-scroll-distance="10"
                         class="jinya-card-list--video-view">
            <jinya-card :header="video.name" v-for="video in videos" :key="video.slug">
                <video v-if="video.type === 'jinya'" class="jinya-video-view__video" :src="video.video"
                       controls :poster="video.poster"></video>
                <iframe v-else-if="video.type === 'youtube'" class="jinya-video-view__video"
                        :src="`https://www.youtube-nocookie.com/embed/${video.videoKey}`"></iframe>
                <jinya-card-button text="art.galleries.designer.video_view.pick" type="details" slot="footer"
                                   @click="pick(video)" :is-disabled="picked"/>
            </jinya-card>
        </jinya-card-list>
    </div>
</template>

<script>
  import infiniteScroll from 'vue-infinite-scroll'
  import JinyaInput from "@/framework/Markup/Form/Input";
  import JinyaCardList from "@/framework/Markup/Listing/Card/CardList";
  import JinyaCard from "@/framework/Markup/Listing/Card/Card";
  import JinyaCardButton from "@/framework/Markup/Listing/Card/CardButton";
  import JinyaRequest from "@/framework/Ajax/JinyaRequest";

  export default {
    components: {
      JinyaCardButton,
      JinyaCard,
      JinyaCardList,
      JinyaInput
    },
    directives: {
      infiniteScroll
    },
    name: "jinya-gallery-designer-video-view",
    data() {
      return {
        videos: [],
        loading: true,
        initial: true,
        picked: false,
        keyword: ''
      };
    },
    methods: {
      pick(video) {
        this.picked = true;
        this.$emit('picked', video);
      },
      async load(replace = false) {
        this.$emit('load-start');
        this.loading = true;
        const videos = await JinyaRequest.get(this.more);
        if (replace) {
          this.videos = [];
        }

        videos.items.forEach(item => this.videos.push(item));
        this.more = videos.control.next;
        this.loading = false;
        this.$emit('load-end');
      },
      async loadMore() {
        if (this.more) {
          await this.load.call(this);
        }
      },
      async search($event) {
        if ($event.key.toLowerCase() === 'enter') {
          this.more = `/api/video/any?keyword=${this.keyword}`;
          await this.load.call(this, [true]);
        }
      }
    },
    async mounted() {
      this.initial = true;
      this.more = '/api/video/any';
      await this.load.call(this);
      this.initial = false;
    }
  }
</script>

<style scoped lang="scss">
    .jinya-video-view {
        width: 50em;
        max-height: 40em;
        margin: -2em;
        padding: 2em 0 0;
        position: relative;

        @include breakpoint-768-height {
            max-height: 30em;
        }
    }

    .jinya-card-list--video-view {
        overflow-y: auto;
        max-height: 38em;

        @include breakpoint-768-height {
            max-height: 28em;
        }
    }

    .jinya-video-view__video {
        width: 100%;
        height: 100%;
        transition: all 0.3s;
    }

    .jinya-input--video-search {
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
    }
</style>