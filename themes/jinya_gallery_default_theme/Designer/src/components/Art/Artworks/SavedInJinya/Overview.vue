<template>
    <section class="jinya-art-overview" :class="{'jinya-art-overview--loading': loading}" @click="$emit('close')">
        <jinya-card-list :class="{'jinya-card-list--loading': loading}">
            <jinya-card :header="artwork.name" v-for="artwork in artworks" v-if="!loading">
                <img class="jinya-art-picture" :src="artwork.picture"/>
                <jinya-card-button @click="details(artwork)" slot="footer" icon="monitor" type="details"/>
                <jinya-card-button @click="edit(artwork)" slot="footer" icon="pencil" type="edit"/>
                <jinya-card-button @click="remove(artwork)" slot="footer" icon="delete" type="delete"/>
            </jinya-card>
        </jinya-card-list>
        <jinya-pager @previous="load(control.previous)" @next="load(control.next)" v-if="!loading" :offset="offset"
                     :count="count"/>
        <jinya-modal @close="showDelete = false" title="Hello World" v-if="showDelete"/>
    </section>
</template>

<script>
  import ArtworksBase from './ArtworksBase';
  import JinyaRequest from "../../../Framework/Ajax/JinyaRequest";
  import JinyaCardList from "../../../Framework/Markup/Listing/Card/CardList";
  import JinyaCard from "../../../Framework/Markup/Listing/Card/Card";
  import JinyaPager from "../../../Framework/Markup/Listing/Pager";
  import JinyaCardButton from "../../../Framework/Markup/Listing/Card/CardButton";
  import JinyaModal from "../../../Framework/Markup/Modal/Modal";

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
      JinyaModal,
      JinyaCardButton,
      JinyaPager,
      JinyaCard,
      JinyaCardList
    },
    extends: ArtworksBase,
    name: "overview",
    methods: {
      load(url) {
        load.call(this, url);
      },
      details(artwork) {

      },
      edit(artwork) {

      },
      remove(artwork) {
        this.showDelete = true;
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
        loading: true,
        showDelete: false
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
</style>