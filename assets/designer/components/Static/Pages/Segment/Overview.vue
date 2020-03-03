<template>
    <div class="jinya-segment-pages">
        <jinya-toolbar>
            <jinya-toolbar-button is-primary label="static.pages.segment.overview.toolbar.add"
                                  to="Static.Pages.Segment.Add"/>
            <jinya-toolbar-button :is-disabled="!pageSelected" :params="{slug: selectedPage.slug}" is-secondary
                                  label="static.pages.segment.overview.toolbar.show_details"
                                  to="Static.Pages.Segment.Details"/>
            <jinya-toolbar-button :is-disabled="!pageSelected" :params="{slug: selectedPage.slug}" is-secondary
                                  label="static.pages.segment.overview.toolbar.edit" to="Static.Pages.Segment.Edit"/>
            <jinya-toolbar-button :is-disabled="!pageSelected" :params="{slug: selectedPage.slug}" is-secondary
                                  label="static.pages.segment.overview.toolbar.editor"
                                  to="Static.Pages.Segment.Editor"/>
            <jinya-toolbar-button :is-disabled="!pageSelected" @click="showDeleteModal(selectedPage)" is-danger
                                  label="static.pages.segment.overview.toolbar.delete"/>
        </jinya-toolbar>
        <jinya-table :headers="headers" :rows="pages" :selected-row="selectedPage" @selected="selectRow"/>
        <jinya-modal :loading="this.delete.loading" @close="closeDeleteModal()"
                     title="static.pages.segment.delete.title"
                     v-if="this.delete.show">
            <jinya-message :message="this.delete.error" slot="message" state="error"
                           v-if="this.delete.error && !this.delete.loading"/>
            {{'static.pages.segment.delete.content'|jmessage(selectedPage)}}
            <jinya-modal-button :closes-modal="true" :is-disabled="this.delete.loading" :is-secondary="true"
                                label="static.pages.segment.delete.no" slot="buttons-left"/>
            <jinya-modal-button :is-danger="true" :is-disabled="this.delete.loading" @click="remove"
                                label="static.pages.segment.delete.yes"
                                slot="buttons-right"/>
        </jinya-modal>
    </div>
</template>

<script>
  import JinyaFloatingActionButton from '@/framework/Markup/FloatingActionButton';
  import JinyaModalButton from '@/framework/Markup/Modal/ModalButton';
  import JinyaMessage from '@/framework/Markup/Validation/Message';
  import JinyaModal from '@/framework/Markup/Modal/Modal';
  import JinyaRequest from '@/framework/Ajax/JinyaRequest';
  import Routes from '@/router/Routes';
  import Translator from '@/framework/i18n/Translator';
  import Events from '@/framework/Events/Events';
  import EventBus from '@/framework/Events/EventBus';
  import JinyaToolbarButton from '@/framework/Markup/Toolbar/ToolbarButton';
  import JinyaToolbar from '@/framework/Markup/Toolbar/Toolbar';
  import JinyaTable from '@/framework/Markup/Table/Table';

  export default {
    name: 'Overview',
    components: {
      JinyaToolbar,
      JinyaToolbarButton,
      JinyaTable,
      JinyaModal,
      JinyaMessage,
      JinyaModalButton,
      JinyaFloatingActionButton,
    },
    computed: {
      headers() {
        return [
          {
            name: 'name',
            title: Translator.message('static.pages.segment.overview.table.header.name'),
          },
          {
            name: 'segmentCount',
            title: Translator.message('static.pages.segment.overview.table.header.segment_count'),
            template(row) {
              return row.segments.length;
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
          name: Routes.Static.Pages.Segment.Overview.name,
          query: {
            keyword: url.searchParams.get('keyword'),
          },
        });
      },
      async fetchPages(keyword = '') {
        this.currentUrl = `/api/segment_page?keyword=${keyword}`;

        const value = await JinyaRequest.get(this.currentUrl);
        this.pages = value.items;
      },
      selectPage(page) {
        this.selectedPage = page;
      },
      async remove() {
        this.delete.loading = true;
        try {
          await JinyaRequest.delete(`/api/segment_page/${this.selectedPage.slug}`);
          this.delete.show = false;
          this.pages.splice(this.pages.findIndex((page) => page.slug === this.selectedPage.slug), 1);
          this.selectedPage = {};
          this.pageSelected = false;
        } catch (reason) {
          this.delete.error = Translator.validator(`static.pages.segment.overview.delete.${reason.message}`);
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
      const keyword = this.$route.query.keyword || '';
      await this.fetchPages(keyword);

      EventBus.$on(Events.search.triggered, (value) => {
        this.$router.push({
          name: Routes.Static.Pages.Segment.Overview.name,
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
      await this.fetchPages();
      next();
    },
    data() {
      return {
        pages: [],
        keyword: '',
        pageSelected: false,
        selectedPage: {},
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
    .jinya-segment-pages {
        padding-top: 3rem;
    }
</style>
