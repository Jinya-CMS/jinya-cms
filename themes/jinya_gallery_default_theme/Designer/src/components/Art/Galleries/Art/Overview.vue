<template>
    <div class="jinya-gallery-overview">
        <jinya-loader :loading="loading"/>
        <jinya-card-list :nothing-found="nothingFound" v-if="!loading">
            <jinya-card :header="gallery.name" v-for="gallery in galleries" v-if="!loading">
                <p class="jinya-gallery-description">{{gallery.description}}</p>
                <jinya-card-button :to="{name: detailsRoute, params: {slug: gallery.slug}}" slot="footer" icon="monitor"
                                   type="details"/>
                <jinya-card-button :to="{name: editRoute, params: {slug: gallery.slug}}" slot="footer" icon="pencil"
                                   type="edit"/>
                <!--suppress JSUnnecessarySemicolon -->
                <jinya-card-button @click="showDeleteModal(gallery)" slot="footer" icon="delete" type="delete"/>
            </jinya-card>
        </jinya-card-list>
        <jinya-pager @previous="load(control.previous)" @next="load(control.next)" v-if="!loading" :offset="offset"
                     :count="count"/>
        <jinya-modal @close="closeDeleteModal()" title="art.galleries.delete.title" v-if="this.delete.show"
                     :loading="this.delete.loading">
            <jinya-message :message="this.delete.error" state="error" v-if="this.delete.error && !this.delete.loading"
                           slot="message"/>
            {{'art.galleries.delete.content'|jmessage({gallery: selectedGallery.name})}}
            <jinya-modal-button :is-secondary="true" slot="buttons-left" label="art.galleries.delete.no"
                                :closes-modal="true"/>
            <jinya-modal-button :is-danger="true" slot="buttons-right" label="art.galleries.delete.yes"
                                @click="remove"/>
        </jinya-modal>
        <jinya-floating-action-button v-if="!loading" :is-primary="true" icon="plus"
                                      to="Art.Galleries.Art.Add"/>
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
    this.currentUrl = `/api/gallery?offset=${offset}&count=${count}&keyword=${keyword}`;

    JinyaRequest.get(this.currentUrl).then(value => {
      this.galleries = value.items;
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
    name: "jinya-galleries-saved-in-jinya-overview",
    methods: {
      load(target) {
        const url = new URL(target, location.href);

        this.$router.push({
          name: Routes.Art.Galleries.Art.Overview.name,
          query: {
            offset: url.searchParams.get('offset'),
            count: url.searchParams.get('count'),
            keyword: url.searchParams.get('keyword')
          }
        });
      },
      selectGallery(gallery) {
        this.selectedGallery = gallery;
      },
      async remove() {
        this.delete.loading = true;
        try {
          await JinyaRequest.delete(`/api/gallery/${this.selectedGallery.slug}`);
          this.delete.show = false;
          this.load.call(this);
        } catch (reason) {
          this.delete.error = Translator.validator(`art.galleries.overview.delete.${reason.message}`);
        }
        this.delete.loading = false;
      },
      showDeleteModal(gallery) {
        this.selectGallery(gallery);
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
          name: Routes.Art.Galleries.Art.Overview.name,
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
      load.call(this, to.query.offset, to.query.count, to.query.keyword);
      next();
    },
    data() {
      return {
        galleries: [],
        control: {next: false, previous: false},
        count: 0,
        offset: 0,
        loading: true,
        keyword: '',
        selectedGallery: {},
        delete: {
          error: '',
          show: false,
          loading: false
        },
        editRoute: Routes.Art.Galleries.Art.Edit.name,
        detailsRoute: Routes.Art.Galleries.Art.Details.name,
        nothingFound: this.$route.query.keyword ? 'art.galleries.overview.nothing_found' : 'art.galleries.overview.no_galleries'
      };
    }
  }
</script>

<style scoped lang="scss">
    .jinya-gallery-description {
        padding: 0.8em;
        margin: 0;
    }

    .jinya-card__item {
        flex-basis: 15em;
    }
</style>