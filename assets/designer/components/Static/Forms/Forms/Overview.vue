<template>
  <div class="jinya-form-overview">
    <jinya-loader :loading="loading"/>
    <jinya-card-list :nothing-found="nothingFound" v-if="!loading">
      <jinya-card v-for="form in forms" :header="form.title" :key="form.slug">
        <p class="jinya-form__description">{{form.description}}</p>
        <jinya-card-button icon="pencil" type="edit" :to="{name: editRoute, params: {slug: form.slug}}"
                           slot="footer"/>
        <jinya-card-button icon="television-guide" type="edit" slot="footer"
                           :to="{name: itemsRoute, params: {slug: form.slug}}"/>
        <jinya-card-button icon="delete" type="delete" @click="showDeleteModal(form)" slot="footer"/>
      </jinya-card>
    </jinya-card-list>
    <jinya-pager @previous="load(control.previous)" @next="load(control.next)" v-if="!loading" :offset="offset"
                 :count="count"/>
    <jinya-modal @close="closeDeleteModal()" title="static.forms.forms.delete.title" v-if="this.delete.show"
                 :loading="this.delete.loading">
      <jinya-message :message="this.delete.error" state="error" v-if="this.delete.error && !this.delete.loading"
                     slot="message"/>
      {{'static.forms.forms.delete.content'|jmessage({artwork: selectedArtwork.name})}}
      <jinya-modal-button :is-secondary="true" slot="buttons-left" label="static.forms.forms.delete.no"
                          :closes-modal="true" :is-disabled="this.delete.loading"/>
      <jinya-modal-button :is-danger="true" slot="buttons-right" label="static.forms.forms.delete.yes"
                          @click="remove" :is-disabled="this.delete.loading"/>
    </jinya-modal>
    <jinya-floating-action-button v-if="!loading" :is-primary="true" icon="plus" :to="addRoute"/>
  </div>
</template>

<script>
  import JinyaCardList from '@/framework/Markup/Listing/Card/CardList';
  import JinyaCard from '@/framework/Markup/Listing/Card/Card';
  import Translator from '@/framework/i18n/Translator';
  import JinyaRequest from '@/framework/Ajax/JinyaRequest';
  import JinyaCardButton from '@/framework/Markup/Listing/Card/CardButton';
  import Routes from '@/router/Routes';
  import JinyaLoader from '@/framework/Markup/Waiting/Loader';
  import JinyaPager from '@/framework/Markup/Listing/Pager';
  import JinyaModal from '@/framework/Markup/Modal/Modal';
  import JinyaMessage from '@/framework/Markup/Validation/Message';
  import JinyaModalButton from '@/framework/Markup/Modal/ModalButton';
  import JinyaFloatingActionButton from '@/framework/Markup/FloatingActionButton';

  export default {
    name: 'Overview',
    components: {
      JinyaFloatingActionButton,
      JinyaModalButton,
      JinyaMessage,
      JinyaModal,
      JinyaPager,
      JinyaLoader,
      JinyaCardButton,
      JinyaCard,
      JinyaCardList,
    },
    computed: {
      nothingFound() {
        return this.keyword
          ? Translator.validator('static.forms.forms.overview.nothing_found')
          : Translator.validator('static.forms.forms.overview.no_forms');
      },
      editRoute() {
        return Routes.Static.Forms.Forms.Edit.name;
      },
      itemsRoute() {
        return Routes.Static.Forms.Forms.Items.name;
      },
      addRoute() {
        return Routes.Static.Forms.Forms.Add;
      },
    },
    async mounted() {
      const offset = this.$route.query.offset || 0;
      const count = this.$route.query.count || 10;
      const keyword = this.$route.query.keyword || '';
      await this.fetchForms(offset, count, keyword);
    },
    async beforeRouteUpdate(to, from, next) {
      await this.fetchForms(to.query.offset || 0, to.query.count || 10, to.query.keyword || '');
      next();
    },
    methods: {
      async fetchForms(offset = 0, count = 10, keyword = '') {
        this.loading = true;
        this.currentUrl = `/api/form?offset=${offset}&count=${count}&keyword=${keyword}`;

        const value = await JinyaRequest.get(this.currentUrl);
        this.forms = value.items;
        this.control = value.control;
        this.count = value.count;
        this.offset = value.offset;
        this.loading = false;
      },
      selectForm(form) {
        this.selectedForm = form;
      },
      async remove() {
        this.delete.loading = true;
        try {
          await JinyaRequest.delete(`/api/form/${this.selectedForm.slug}`);
          this.delete.show = false;
          const url = new URL(window.location.href);
          await this.fetchForms(0, 10, url.searchParams.get('keyword'));
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
        loading: true,
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

<style scoped lang="scss">
  .jinya-form__description {
    padding-left: 1rem;
    padding-right: 1rem;
  }
</style>
