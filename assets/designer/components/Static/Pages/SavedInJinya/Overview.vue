<template>
    <div class="jinya-pages">
        <jinya-toolbar>
            <jinya-toolbar-button is-primary label="static.pages.overview.toolbar.add"
                                  to="Static.Pages.SavedInJinya.Add"/>
            <jinya-toolbar-button :is-disabled="!pageSelected" :params="{slug: selectedPage.slug}" is-secondary
                                  label="static.pages.overview.toolbar.show_details"
                                  to="Static.Pages.SavedInJinya.Details"/>
            <jinya-toolbar-button :is-disabled="!pageSelected" :params="{slug: selectedPage.slug}" is-secondary
                                  label="static.pages.overview.toolbar.edit" to="Static.Pages.SavedInJinya.Edit"/>
            <jinya-toolbar-button :is-disabled="!pageSelected" @click="showDeleteModal(selectedPage)" is-danger
                                  label="static.pages.overview.toolbar.delete"/>
        </jinya-toolbar>
        <jinya-table :headers="headers" :rows="pages" :selected-row="selectedPage" @selected="selectRow"/>
        <jinya-modal :loading="this.delete.loading" @close="closeDeleteModal()" title="static.pages.delete.title"
                     v-if="this.delete.show">
            <jinya-message :message="this.delete.error" slot="message" state="error"
                           v-if="this.delete.error && !this.delete.loading"/>
            {{'static.pages.delete.content'|jmessage(selectedPage)}}
            <jinya-modal-button :closes-modal="true" :is-disabled="this.delete.loading" :is-secondary="true"
                                label="static.pages.delete.no" slot="buttons-left"/>
            <jinya-modal-button :is-danger="true" :is-disabled="this.delete.loading" @click="remove"
                                label="static.pages.delete.yes"
                                slot="buttons-right"/>
        </jinya-modal>
    </div>
</template>

<script>
  import JinyaModalButton from '@/framework/Markup/Modal/ModalButton';
  import JinyaMessage from '@/framework/Markup/Validation/Message';
  import JinyaModal from '@/framework/Markup/Modal/Modal';
  import JinyaLoader from '@/framework/Markup/Waiting/Loader';
  import JinyaTable from '@/framework/Markup/Table/Table';
  import JinyaRequest from '@/framework/Ajax/JinyaRequest';
  import Routes from '@/router/Routes';
  import Translator from '@/framework/i18n/Translator';
  import Events from '@/framework/Events/Events';
  import EventBus from '@/framework/Events/EventBus';
  import JinyaToolbar from '@/framework/Markup/Toolbar/Toolbar';
  import JinyaToolbarButton from '@/framework/Markup/Toolbar/ToolbarButton';
  import truncate from 'lodash/truncate';

  export default {
    name: 'Overview',
    components: {
      JinyaToolbarButton,
      JinyaToolbar,
      JinyaLoader,
      JinyaModal,
      JinyaMessage,
      JinyaModalButton,
      JinyaTable,
    },
    computed: {
      headers() {
        return [
          {
            name: 'title',
            title: Translator.message('static.pages.overview.table.header.name'),
          },
          {
            name: 'content',
            title: Translator.message('static.pages.overview.table.header.content'),
            template(row) {
              const elem = document.createElement('div');
              elem.innerHTML = row.content;

              return truncate(elem.innerText, {
                length: 50,
                omission: '…',
              });
            },
          },
        ];
      },
    },
    methods: {
      selectRow(row) {
        this.selectedPage = row;
        this.pageSelected = true;
      },
      load(target) {
        const url = new URL(target, window.location.href);

        this.$router.push({
          name: Routes.Static.Pages.SavedInJinya.Overview.name,
          query: {
            offset: url.searchParams.get('offset'),
            count: url.searchParams.get('count'),
            keyword: url.searchParams.get('keyword'),
          },
        });
      },
      async fetchPages(offset = 0, count = Number.MAX_SAFE_INTEGER, keyword = '') {
        this.currentUrl = `/api/page?offset=${offset}&count=${count}&keyword=${keyword}`;

        const value = await JinyaRequest.get(this.currentUrl);
        this.pages = value.items;
        this.control = value.control;
        this.count = value.count;
        this.offset = value.offset;
      },
      selectPage(page) {
        this.selectedPage = page;
      },
      async remove() {
        this.delete.loading = true;
        try {
          await JinyaRequest.delete(`/api/page/${this.selectedPage.slug}`);
          this.delete.show = false;
          const url = new URL(window.location.href);
          await this.fetchPages(0, Number.MAX_SAFE_INTEGER, url.searchParams.get('keyword'));
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
      },
    },
    async mounted() {
      const offset = this.$route.query.offset || 0;
      const count = this.$route.query.count || Number.MAX_SAFE_INTEGER;
      const keyword = this.$route.query.keyword || '';
      await this.fetchPages(offset, count, keyword);

      EventBus.$on(Events.search.triggered, (value) => {
        this.$router.push({
          name: Routes.Static.Pages.SavedInJinya.Overview.name,
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
      await this.fetchPages(to.query.offset || 0, to.query.count || Number.MAX_SAFE_INTEGER, to.query.keyword || '');
      next();
    },
    data() {
      return {
        pages: [],
        control: { next: false, previous: false },
        count: 0,
        offset: 0,
        keyword: '',
        selectedPage: {},
        pageSelected: false,
        delete: {
          error: '',
          show: false,
          loading: false,
        },
      };
    },
  };
</script>

<style lang="scss" scoped>
    .jinya-pages {
        padding-top: 3rem;
    }
</style>
