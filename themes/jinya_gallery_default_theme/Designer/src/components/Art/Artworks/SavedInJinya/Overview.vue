<template>
    <section class="jinya-art-overview" :class="{'jinya-art-overview--loading': loading}">
        <jinya-card-list :class="{'jinya-card-list--loading': loading}">
            <jinya-card :header="artwork.name" v-for="artwork in artworks" v-if="!loading">
                <img class="jinya-art-picture" :src="artwork.picture"/>
                <button class="jinya-art-button jinya-art-button--details" slot="footer"><span
                        class="mdi mdi-monitor"></span></button>
                <button class="jinya-art-button jinya-art-button--edit" slot="footer"><span
                        class="mdi mdi-pencil"></span>
                </button>
                <button class="jinya-art-button jinya-art-button--delete" slot="footer"><span
                        class="mdi mdi-delete"></span>
                </button>
            </jinya-card>
        </jinya-card-list>
        <jinya-pager @previous="load(control.previous)" @next="load(control.next)" v-if="!loading" :offset="offset"
                     :count="count"/>
    </section>
</template>

<script>
  import ArtworksBase from './ArtworksBase';
  import JinyaRequest from "../../../Framework/Ajax/JinyaRequest";
  import JinyaCardList from "../../../Framework/Markup/Listing/CardList";
  import JinyaCard from "../../../Framework/Markup/Listing/Card";
  import JinyaPager from "../../../Framework/Markup/Listing/Pager";

  function load(url) {
    this.loading = true;

    JinyaRequest.get(url).then(value => {
      this.artworks = value.items;
      this.control = value.control;
      this.count = value.count;
      this.offset = value.offset;
      this.loading = false;
    });
  }

  export default {
    components: {
      JinyaPager,
      JinyaCard,
      JinyaCardList
    },
    extends: ArtworksBase,
    name: "overview",
    methods: {
      load(url) {
        load.call(this, url);
      }
    },
    beforeCreate() {
      load.call(this, '/api/artwork');
    },
    data() {
      return {
        artworks: [],
        control: {next: false, previous: false},
        count: 0,
        offset: 0,
        loading: true
      };
    }
  }
</script>

<style scoped lang="scss">
    @include card-list-loading('jinya-art-overview');

    .jinya-art-picture {
        width: 100%;
        height: 100%;
        object-fit: cover;
        transition: all 0.3s;

        &:hover {
            object-fit: scale-down;
        }
    }

    @include card-footer-button('jinya-art-button');
</style>