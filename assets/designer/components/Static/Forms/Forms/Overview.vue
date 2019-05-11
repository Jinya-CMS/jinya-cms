<template>
  <div class="jinya-form-overview">
    <jinya-loader :loading="loading"/>
    <jinya-card-list :nothing-found="nothingFound" v-if="!loading">
      <jinya-card :header="form.title" :key="form.slug" v-for="form in forms">
        <p class="jinya-form__description">{{form.description}}</p>
        <jinya-card-button :to="{name: editRoute, params: {slug: form.slug}}" icon="pencil" slot="footer"
                           type="edit"/>
        <jinya-card-button :to="{name: itemsRoute, params: {slug: form.slug}}" icon="television-guide" slot="footer"
                           type="edit"/>
        <jinya-card-button @click="showDeleteModal(form)" icon="delete" slot="footer" type="delete"/>
      </jinya-card>
    </jinya-card-list>
    <jinya-pager :count="count" :offset="offset" @next="load(control.next)" @previous="load(control.previous)"
                 v-if="!loading"/>
    <jinya-modal :loading="this.delete.loading" @close="closeDeleteModal()" title="static.forms.forms.delete.title"
                 v-if="this.delete.show">
      <jinya-message :message="this.delete.error" slot="message" state="error"
                     v-if="this.delete.error && !this.delete.loading"/>
      {{'static.forms.forms.delete.content'|jmessage({artwork: selectedArtwork.name})}}
      <jinya-modal-button :closes-modal="true" :is-disabled="this.delete.loading" :is-secondary="true"
                          label="static.forms.forms.delete.no" slot="buttons-left"/>
      <jinya-modal-button :is-danger="true" :is-disabled="this.delete.loading" @click="remove"
                          label="static.forms.forms.delete.yes" slot="buttons-right"/>
    </jinya-modal>
    <jinya-floating-action-button :is-primary="true" :to="addRoute" icon="plus" v-if="!loading"/>
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

<style lang="scss" scoped>
  .jinya-form__description {
    padding-left: 1rem;
    padding-right: 1rem;
  }
</style>
