<template>
  <div class="jinya-artwork-view">
    <jinya-input @keyup="search" class="jinya-input--artwork-search"
                 placeholder="art.galleries.designer.art_view.search"
                 type="search" v-if="!initial" v-model="keyword"/>
    <jinya-card-list class="jinya-card-list--artwork-view" infinite-scroll-disabled="loading"
                     infinite-scroll-distance="10" nothing-found="art.galleries.designer.art_view.nothing_found"
                     v-infinite-scroll="loadMore" v-show="!initial">
      <jinya-card :header="artwork.name" :key="artwork.slug" v-for="artwork in artworks">
        <img :src="artwork.picture" class="jinya-artwork-view__picture"/>
        <jinya-card-button :is-disabled="picked" @click="pick(artwork)" slot="footer"
                           text="art.galleries.designer.art_view.pick" type="details"/>
      </jinya-card>
    </jinya-card-list>
  </div>
</template>

<script>
  import infiniteScroll from 'vue-infinite-scroll';
  import JinyaInput from '@/framework/Markup/Form/Input';
  import JinyaCardList from '@/framework/Markup/Listing/Card/CardList';
  import JinyaCard from '@/framework/Markup/Listing/Card/Card';
  import JinyaCardButton from '@/framework/Markup/Listing/Card/CardButton';
  import JinyaRequest from '@/framework/Ajax/JinyaRequest';

  export default {
    components: {
      JinyaCardButton,
      JinyaCard,
      JinyaCardList,
      JinyaInput,
    },
    directives: {
      infiniteScroll,
    },
    name: 'jinya-gallery-designer-artwork-view',
    data() {
      return {
        artworks: [],
        loading: true,
        initial: true,
        picked: false,
        keyword: '',
      };
    },
    methods: {
      pick(artwork) {
        this.picked = true;
        this.$emit('picked', artwork);
      },
      async load(replace = false) {
        this.$emit('load-start');
        this.loading = true;
        const artworks = await JinyaRequest.get(this.more);
        if (replace) {
          this.artworks = [];
        }

        artworks.items.forEach(item => this.artworks.push(item));
        this.more = artworks.control.next;
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
          this.more = `/api/artwork?keyword=${this.keyword}`;
          await this.load.call(this, [true]);
        }
      },
    },
    async mounted() {
      this.initial = true;
      this.more = '/api/artwork';
      await this.load.call(this);
      this.initial = false;
    },
  };
</script>

<style lang="scss" scoped>
  .jinya-artwork-view {
    width: 50em;
    max-height: 40em;
    margin: -2em;
    padding: 2em 0 0;
    position: relative;

    @include breakpoint-768-height {
      max-height: 30em;
    }
  }

  .jinya-card-list--artwork-view {
    overflow-y: auto;
    max-height: 38em;

    @include breakpoint-768-height {
      max-height: 28em;
    }
  }

  .jinya-artwork-view__picture {
    width: 100%;
    height: 100%;
    object-fit: cover;
    transition: all 0.3s;

    &:hover {
      object-fit: scale-down;
    }
  }

  .jinya-input--artwork-search {
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
  }
</style>
