<template>
    <div class="jinya-art-overview">
        <jinya-loader :loading="loading"/>
        <jinya-card-list :nothing-found="nothingFound" v-if="!loading">
            <jinya-card :header="artwork.name" v-for="artwork in artworks" v-if="!loading">
                <img class="jinya-art-picture" :src="artwork.picture"/>
                <jinya-card-button :to="{name: detailsRoute, params: {slug: artwork.slug}}" slot="footer" icon="monitor"
                                   type="details"/>
                <jinya-card-button :to="{name: editRoute, params: {slug: artwork.slug}}" slot="footer" icon="pencil"
                                   type="edit"/>
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
                                :closes-modal="true" :is-disabled="this.delete.loading"/>
            <jinya-modal-button :is-danger="true" slot="buttons-right" label="art.artworks.delete.yes" @click="remove"
                                :is-disabled="this.delete.loading"/>
        </jinya-modal>
        <jinya-floating-action-button v-if="!loading" :is-primary="true" icon="plus"
                                      to="Art.Artworks.SavedInJinya.Add"/>
    </div>
</template>

<script>
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
  import EventBus from "../../../Framework/Events/EventBus";
  import Events from "../../../Framework/Events/Events";
  import Routes from "../../../../router/Routes";
  import JinyaFloatingActionButton from "../../../Framework/Markup/FloatingActionButton";

  function load(offset = 0, count = 10, keyword = '') {
    this.loading = true;
    this.currentUrl = `/api/artwork?offset=${offset}&count=${count}&keyword=${keyword}`;

    JinyaRequest.get(this.currentUrl).then(value => {
      this.artworks = value.items;
      this.control = value.control;
      this.count = value.count;
      this.offset = value.offset;
      this.loading = false;
    });
  }

  // noinspection JSUnusedGlobalSymbols
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
      JinyaCardList
    },
    name: "jinya-artworks-saved-in-jinya-overview",
    methods: {
      load(target) {
        const url = new URL(target, location.href);

        this.$router.push({
          name: Routes.Art.Artworks.SavedInJinya.Overview.name,
          query: {
            offset: url.searchParams.get('offset'),
            count: url.searchParams.get('count'),
            keyword: url.searchParams.get('keyword')
          }
        });
      },
      selectArtwork(artwork) {
        this.selectedArtwork = artwork;
      },
      async remove() {
        this.delete.loading = true;
        try {
          await JinyaRequest.delete(`/api/artwork/${this.selectedArtwork.slug}`);
          this.delete.show = false;
          this.load.call(this);
        } catch (reason) {
          this.delete.error = Translator.validator(`art.artworks.overview.delete.${reason.message}`);
        }
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
      const offset = this.$route.query.offset || 0;
      const count = this.$route.query.count || 10;
      const keyword = this.$route.query.keyword || '';
      load.call(this, offset, count, keyword);

      EventBus.$on(Events.search.triggered, value => {
        this.$router.push({
          name: Routes.Art.Artworks.SavedInJinya.Overview.name,
          query: {
            offset: 0,
            count: this.$route.query.count,
            keyword: value.keyword
          }
        });
      });
    },
    beforeDestroy() {
      EventBus.$off(Events.search.triggered);
    },
    beforeRouteUpdate(to, from, next) {
      load.call(this, to.query.offset || 0, to.query.count || 10, to.query.keyword || '');
      next();
    },
    data() {
      return {
        artworks: [],
        control: {next: false, previous: false},
        count: 0,
        offset: 0,
        loading: true,
        keyword: '',
        selectedArtwork: {},
        delete: {
          error: '',
          show: false,
          loading: false
        },
        editRoute: Routes.Art.Artworks.SavedInJinya.Edit.name,
        detailsRoute: Routes.Art.Artworks.SavedInJinya.Details.name,
        nothingFound: this.$route.query.keyword ? 'art.artworks.overview.nothing_found' : 'art.artworks.overview.no_artworks'
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