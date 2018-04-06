<template>
    <div class="jinya-page-overview">
        <jinya-loader :loading="loading"/>
        <jinya-card-list :nothing-found="nothingFound" v-if="!loading">
            <jinya-card :header="page.title" v-for="page in pages" v-if="!loading">
                <jinya-card-button :to="{name: detailsRoute, params: {slug: page.slug}}" slot="footer" icon="monitor"
                                   type="details"/>
                <jinya-card-button :to="{name: editRoute, params: {slug: page.slug}}" slot="footer" icon="pencil"
                                   type="edit"/>
                <!--suppress JSUnnecessarySemicolon -->
                <jinya-card-button @click="showDeleteModal(page)" slot="footer" icon="delete" type="delete"/>
            </jinya-card>
        </jinya-card-list>
        <jinya-pager @previous="load(control.previous)" @next="load(control.next)" v-if="!loading" :offset="offset"
                     :count="count"/>
        <jinya-modal @close="closeDeleteModal()" title="static.pages.delete.title" v-if="this.delete.show"
                     :loading="this.delete.loading">
            <jinya-message :message="this.delete.error" state="error" v-if="this.delete.error && !this.delete.loading"
                           slot="message"/>
            {{'static.pages.delete.content'|jmessage(selectedPage)}}
            <jinya-modal-button :is-secondary="true" slot="buttons-left" label="static.pages.delete.no"
                                :closes-modal="true" :is-disabled="this.delete.loading"/>
            <jinya-modal-button :is-danger="true" slot="buttons-right" label="static.pages.delete.yes" @click="remove"
                                :is-disabled="this.delete.loading"/>
        </jinya-modal>
        <jinya-floating-action-button v-if="!loading" :is-primary="true" icon="plus"
                                      to="Static.Pages.SavedInJinya.Add"/>
    </div>
</template>

<script>
  import JinyaCardList from "@/framework/Markup/Listing/Card/CardList";
  import JinyaFloatingActionButton from "@/framework/Markup/FloatingActionButton";
  import JinyaModalButton from "@/framework/Markup/Modal/ModalButton";
  import JinyaMessage from "@/framework/Markup/Validation/Message";
  import JinyaModal from "@/framework/Markup/Modal/Modal";
  import JinyaPager from "@/framework/Markup/Listing/Pager";
  import JinyaCardButton from "@/framework/Markup/Listing/Card/CardButton";
  import JinyaCard from "@/framework/Markup/Listing/Card/Card";
  import JinyaLoader from "@/framework/Markup/Loader";
  import JinyaRequest from "@/framework/Ajax/JinyaRequest";
  import Routes from "@/router/Routes";
  import Translator from "@/framework/i18n/Translator";
  import Events from "@/framework/Events/Events";
  import EventBus from "@/framework/Events/EventBus";

  export default {
    name: "Overview",
    components: {
      JinyaLoader,
      JinyaCard,
      JinyaCardButton,
      JinyaPager,
      JinyaModal,
      JinyaMessage,
      JinyaModalButton,
      JinyaFloatingActionButton,
      JinyaCardList
    },
    methods: {
      load(target) {
        const url = new URL(target, location.href);

        this.$router.push({
          name: Routes.Static.Pages.SavedInJinya.Overview.name,
          query: {
            offset: url.searchParams.get('offset'),
            count: url.searchParams.get('count'),
            keyword: url.searchParams.get('keyword')
          }
        });
      },
      async fetchPages(offset = 0, count = 10, keyword = '') {
        this.loading = true;
        this.currentUrl = `/api/page?offset=${offset}&count=${count}&keyword=${keyword}`;

        const value = await JinyaRequest.get(this.currentUrl);
        this.pages = value.items;
        this.control = value.control;
        this.count = value.count;
        this.offset = value.offset;
        this.loading = false;
      },
      selectPage(page) {
        this.selectedPage = page;
      },
      async remove() {
        this.delete.loading = true;
        try {
          await JinyaRequest.delete(`/api/page/${this.selectedPage.slug}`);
          this.delete.show = false;
          const url = new URL(location.href);
          await this.fetchPages(0, 10, url.searchParams.get('keyword'));
        } catch (reason) {
          this.delete.error = Translator.validator(`static.pages.overview.delete.${reason.message}`);
        }
        this.delete.loading = false;
      },
      showDeleteModal(page) {
        this.selectPage(page);
        this.delete.show = true;
      },
      closeDeleteModal() {
        this.delete.show = false;
        this.delete.loading = false;
        this.delete.error = '';
      }
    },
    async mounted() {
      const offset = this.$route.query.offset || 0;
      const count = this.$route.query.count || 10;
      const keyword = this.$route.query.keyword || '';
      await this.fetchPages(offset, count, keyword);

      EventBus.$on(Events.search.triggered, value => {
        this.$router.push({
          name: Routes.Static.Pages.SavedInJinya.Overview.name,
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
    async beforeRouteUpdate(to, from, next) {
      await this.fetchPages(to.query.offset || 0, to.query.count || 10, to.query.keyword || '');
      next();
    },
    data() {
      return {
        pages: [],
        control: {next: false, previous: false},
        count: 0,
        offset: 0,
        loading: true,
        keyword: '',
        selectedPage: {},
        delete: {
          error: '',
          show: false,
          loading: false
        },
        editRoute: Routes.Static.Pages.SavedInJinya.Edit.name,
        detailsRoute: Routes.Static.Pages.SavedInJinya.Details.name,
        nothingFound: this.$route.query.keyword ? 'static.pages.overview.nothing_found' : 'static.pages.overview.no_pages'
      };
    }
  }
</script>
