<template>
    <div class="jinya-art-overview">
        <jinya-loader :loading="loading"/>
        <jinya-card-list v-if="!loading">
            <jinya-card :header="artwork.name" v-for="artwork in artworks" v-if="!loading">
                <img class="jinya-art-picture" :src="artwork.picture"/>
                <jinya-card-button @click="details(artwork)" slot="footer" icon="monitor" type="details"/>
                <jinya-card-button @click="edit(artwork)" slot="footer" icon="pencil" type="edit"/>
                <!--suppress JSUnnecessarySemicolon -->
                <jinya-card-button @click="showDeleteModal(artwork)" slot="footer" icon="delete" type="delete"/>
            </jinya-card>
        </jinya-card-list>
        <jinya-pager @previous="load(control.previous)" @next="load(control.next)" v-if="!loading" :offset="offset"
                     :count="count"/>
        <jinya-modal @close="closeDeleteModal()" title="art.artworks.delete.title" v-if="this.delete.show"
                     :loading="this.delete.loading">
            <jinya-message :message="this.delete.error" state="error" v-if="this.delete.error && !this.delete.loading"
                           slot="message"/>
            {{'art.artworks.delete.content'|jmessage({artwork: selectedArtwork.name})}}
            <jinya-modal-button :is-secondary="true" slot="buttons-left" label="art.artworks.delete.no"
                                :closes-modal="true"/>
            <jinya-modal-button :is-danger="true" slot="buttons-right" label="art.artworks.delete.yes" @click="remove"/>
        </jinya-modal>
    </div>
</template>

<script>
  import ArtworksBase from './ArtworksBase';
  import JinyaRequest from "../../../Framework/Ajax/JinyaRequest";
  import JinyaCardList from "../../../Framework/Markup/Listing/Card/CardList";
  import JinyaCard from "../../../Framework/Markup/Listing/Card/Card";
  import JinyaPager from "../../../Framework/Markup/Listing/Pager";
  import JinyaCardButton from "../../../Framework/Markup/Listing/Card/CardButton";
  import JinyaModal from "../../../Framework/Markup/Modal/Modal";
  import JinyaModalButton from "../../../Framework/Markup/Modal/ModalButton";
  import Translator from "../../../Framework/i18n/Translator";
  import JinyaMessage from "../../../Framework/Markup/Validation/Message";
  import JinyaLoader from "../../../Framework/Markup/Loader";

  function load(url) {
    this.loading = true;
    this.currentUrl = url || this.currentUrl;

    JinyaRequest.get(this.currentUrl).then(value => {
      this.artworks = value.items;
      this.control = value.control;
      this.count = value.count;
      this.offset = value.offset;
      this.loading = false;
    });
  }

  export default {
    components: {
      JinyaLoader,
      JinyaMessage,
      JinyaModalButton,
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
      selectArtwork(artwork) {
        this.selectedArtwork = artwork;
      },
      async remove() {
        this.delete.loading = true;
        await JinyaRequest.delete(`/api/artwork/${this.selectedArtwork.slug}`).then(() => {
          this.delete.show = false;
          this.load.call(this);
        }).catch(reason => {
          this.delete.error = Translator.validator(`art.artworks.overview.delete.${reason.message}`);
        });
        this.delete.loading = false;
      },
      showDeleteModal(artwork) {
        this.selectArtwork(artwork);
        this.delete.show = true;
      },
      closeDeleteModal() {
        this.delete.show = false;
        this.delete.loading = false;
        this.delete.error = '';
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
        selectedArtwork: {},
        delete: {
          error: '',
          show: false,
          loading: false
        }
      };
    }
  }
</script>

<style scoped lang="scss">
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