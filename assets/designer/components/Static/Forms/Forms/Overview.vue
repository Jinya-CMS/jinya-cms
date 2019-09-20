<template>
    <div class="jinya-forms">
        <jinya-toolbar>
            <jinya-toolbar-button is-primary label="static.forms.forms.overview.toolbar.add"
                                  to="Static.Forms.Forms.Add"/>
            <jinya-toolbar-button :is-disabled="!formSelected" :params="{slug: selectedForm.slug}" is-secondary
                                  label="static.forms.forms.overview.toolbar.edit" to="Static.Forms.Forms.Edit"/>
            <jinya-toolbar-button :is-disabled="!formSelected" :params="{slug: selectedForm.slug}" is-secondary
                                  label="static.forms.forms.overview.toolbar.designer"
                                  to="Static.Forms.Forms.Designer"/>
            <jinya-toolbar-button :is-disabled="!formSelected" :params="{slug: selectedForm.slug}" is-secondary
                                  label="static.forms.forms.overview.toolbar.open_mailbox"
                                  to="Static.Forms.Messages.Form"/>
            <jinya-toolbar-button :is-disabled="!formSelected" @click="showDeleteModal(selectedForm)" is-danger
                                  label="static.forms.forms.overview.toolbar.delete"/>
        </jinya-toolbar>
        <jinya-table :headers="headers" :rows="forms" :selected-row="selectedForm" @selected="selectRow"/>
        <jinya-modal :loading="this.delete.loading" @close="closeDeleteModal()" title="static.forms.forms.delete.title"
                     v-if="this.delete.show">
            <jinya-message :message="this.delete.error" slot="message" state="error"
                           v-if="this.delete.error && !this.delete.loading"/>
            {{'static.forms.forms.delete.content'|jmessage(selectedForm)}}
            <jinya-modal-button :closes-modal="true" :is-disabled="this.delete.loading" :is-secondary="true"
                                label="static.forms.forms.delete.no" slot="buttons-left"/>
            <jinya-modal-button :is-danger="true" :is-disabled="this.delete.loading" @click="remove"
                                label="static.forms.forms.delete.yes" slot="buttons-right"/>
        </jinya-modal>
    </div>
</template>

<script>
  import JinyaModalButton from '@/framework/Markup/Modal/ModalButton';
  import JinyaMessage from '@/framework/Markup/Validation/Message';
  import JinyaModal from '@/framework/Markup/Modal/Modal';
  import JinyaTable from '@/framework/Markup/Table/Table';
  import JinyaRequest from '@/framework/Ajax/JinyaRequest';
  import Translator from '@/framework/i18n/Translator';
  import JinyaToolbar from '@/framework/Markup/Toolbar/Toolbar';
  import JinyaToolbarButton from '@/framework/Markup/Toolbar/ToolbarButton';

  export default {
    name: 'Overview',
    components: {
      JinyaToolbarButton,
      JinyaToolbar,
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
            title: Translator.message('static.forms.forms.overview.table.header.title'),
          },
          {
            name: 'itemCount',
            title: Translator.message('static.forms.forms.overview.table.header.item_count'),
            template(row) {
              return row.items.length;
            },
          },
          {
            name: 'toAddress',
            title: Translator.message('static.forms.forms.overview.table.header.to_address'),
          },
          {
            name: 'description',
            title: Translator.message('static.forms.forms.overview.table.header.description'),
          },
        ];
      },
    },
    async mounted() {
      const offset = this.$route.query.offset || 0;
      const count = this.$route.query.count || Number.MAX_SAFE_INTEGER;
      const keyword = this.$route.query.keyword || '';
      await this.fetchForms(offset, count, keyword);
    },
    async beforeRouteUpdate(to, from, next) {
      await this.fetchForms(to.query.offset || 0, to.query.count || 10, to.query.keyword || '');
      next();
    },
    methods: {
      selectRow(row) {
        this.formSelected = true;
        this.selectedForm = row;
      },
      async fetchForms(offset = 0, count = Number.MAX_SAFE_INTEGER, keyword = '') {
        this.currentUrl = `/api/form?offset=${offset}&count=${count}&keyword=${keyword}`;

        const value = await JinyaRequest.get(this.currentUrl);
        this.forms = value.items;
        this.control = value.control;
        this.count = value.count;
        this.offset = value.offset;
      },
      selectForm(form) {
        this.selectedForm = form;
      },
      async remove() {
        this.delete.loading = true;
        try {
          await JinyaRequest.delete(`/api/form/${this.selectedForm.slug}`);
          this.delete.show = false;
          this.forms.splice(this.forms.findIndex((form) => form.id === this.selectedForm.id), 1);
          this.selectedForm = {};
          this.formSelected = false;
        } catch (reason) {
          this.delete.error = Translator.validator(reason.message);
        }

        this.delete.loading = false;
      },
      showDeleteModal(form) {
        this.selectForm(form);
        this.delete.show = true;
      },
      closeDeleteModal() {
        this.delete.show = false;
        this.delete.loading = false;
        this.delete.error = '';
      },
    },
    data() {
      return {
        forms: [],
        keyword: '',
        control: {
          next: false,
          previous: false,
        },
        formSelected: false,
        selectedForm: {},
        count: 0,
        offset: 0,
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
    .jinya-forms {
        padding-top: 3rem;
    }

    .jinya-form__description {
        padding-left: 1rem;
        padding-right: 1rem;
    }
</style>
