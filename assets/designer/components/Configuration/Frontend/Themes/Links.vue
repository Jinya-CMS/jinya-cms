<template>
    <jinya-loader :loading="loading" v-if="loading"/>
    <div v-else>
        <jinya-message :message="message" :state="state"/>
        <jinya-form :enable="enable" @back="cancel" @submit="save"
                    cancel-label="configuration.frontend.themes.links.cancel"
                    save-label="configuration.frontend.themes.links.save"
                    v-if="state !== 'info'">
            <jinya-fieldset :legend="'configuration.frontend.themes.links.menus'|jmessage" v-if="structure.menus">
                <jinya-choice :choices="menus" :enforce-select="true" :key="menu" :label="structure.menus[menu]"
                              :selected="createSelectValue(links.menus[menu])"
                              @selected="item => selected(item, links.menus, menu)"
                              v-for="menu in Object.keys(structure.menus)"/>
            </jinya-fieldset>
            <jinya-fieldset :legend="'configuration.frontend.themes.links.pages'|jmessage" v-if="structure.pages">
                <jinya-choice :choices="pages" :enforce-select="true" :key="page" :label="structure.pages[page]"
                              :selected="createSelectValue(links.pages[page])"
                              @selected="item => selected(item, links.pages, page)"
                              v-for="page in Object.keys(structure.pages)"/>
            </jinya-fieldset>
            <jinya-fieldset :legend="'configuration.frontend.themes.links.forms'|jmessage" v-if="structure.forms">
                <jinya-choice :choices="forms" :enforce-select="true" :key="form" :label="structure.forms[form]"
                              :selected="createSelectValue(links.forms[form])"
                              @selected="item => selected(item, links.forms, form)"
                              v-for="form in Object.keys(structure.forms)"/>
            </jinya-fieldset>
            <jinya-fieldset :legend="'configuration.frontend.themes.links.segment_pages'|jmessage"
                            v-if="structure.segment_pages">
                <jinya-choice :choices="segmentPages" :enforce-select="true" :key="segmentPage"
                              :label="structure.segment_pages[segmentPage]"
                              :selected="createSelectValue(links.segmentPages[segmentPage])"
                              @selected="item => selected(item, links.segmentPages, segmentPage)"
                              v-for="segmentPage in Object.keys(structure.segment_pages)"/>
            </jinya-fieldset>
            <jinya-fieldset :legend="'configuration.frontend.themes.links.galleries'|jmessage"
                            v-if="structure.galleries">
                <jinya-choice :choices="galleries" :enforce-select="true" :key="gallery"
                              :label="structure.galleries[gallery]"
                              :selected="createSelectValue(links.galleries[gallery])"
                              @selected="item => selected(item, links.galleries, gallery)"
                              v-for="gallery in Object.keys(structure.galleries)"/>
            </jinya-fieldset>
            <jinya-fieldset :legend="'configuration.frontend.themes.links.files'|jmessage"
                            v-if="structure.files">
                <jinya-choice :choices="files" :enforce-select="true" :key="file"
                              :label="structure.files[file]" :selected="createSelectValue(links.files[file])"
                              @selected="item => selected(item, links.files, file)"
                              v-for="file in Object.keys(structure.files)"/>
            </jinya-fieldset>
        </jinya-form>
    </div>
</template>

<script>
  import isObject from 'lodash/isObject';
  import isArray from 'lodash/isArray';
  import JinyaForm from '@/framework/Markup/Form/Form';
  import JinyaMessage from '@/framework/Markup/Validation/Message';
  import JinyaRequest from '@/framework/Ajax/JinyaRequest';
  import JinyaLoader from '@/framework/Markup/Waiting/Loader';
  import Routes from '@/router/Routes';
  import Translator from '@/framework/i18n/Translator';
  import Timing from '@/framework/Utils/Timing';
  import JinyaFieldset from '@/framework/Markup/Form/Fieldset';
  import JinyaChoice from '@/framework/Markup/Form/Choice';
  import DOMUtils from '@/framework/Utils/DOMUtils';

  export default {
    name: 'Links',
    components: {
      JinyaChoice,
      JinyaFieldset,
      JinyaLoader,
      JinyaMessage,
      JinyaForm,
    },
    data() {
      return {
        state: '',
        message: '',
        enable: true,
        loading: false,
        structure: {},
        links: {},
        artworks: [],
        pages: [],
        files: [],
        galleries: [],
        segmentPages: [],
        forms: [],
        menus: [],
      };
    },
    async mounted() {
      this.loading = true;
      this.structure = await JinyaRequest.get(`/api/theme/${this.$route.params.name}/links/structure`);

      if (this.structure.length === 0) {
        this.loading = false;
        this.state = 'info';
        this.message = 'configuration.frontend.themes.links.no_structure_found';
      } else {
        const linksPromise = JinyaRequest
          .get(`/api/theme/${this.$route.params.name}/links`)
          .then((links) => {
            const keys = Object.keys(links);
            // eslint-disable-next-line no-restricted-syntax,guard-for-in
            for (const idx in Object.keys(links)) {
              const key = keys[idx];
              if (isArray(links[key])) {
                this.links[key] = {};
              } else {
                this.links[key] = links[key];
              }
            }
          });
        const pagePromise = JinyaRequest
          .get('/api/page')
          .then((pages) => {
            this.pages = pages.items.map((item) => ({ text: item.title, value: item.slug }));
          });
        const segmentPagePromise = JinyaRequest
          .get('/api/segment_page')
          .then((pages) => {
            this.segmentPages = pages.items.map((item) => ({ text: item.name, value: item.slug }));
          });
        const formsPromise = JinyaRequest
          .get('/api/form')
          .then((forms) => {
            this.forms = forms.items.map((item) => ({ text: item.title, value: item.slug }));
          });
        const menusPromise = JinyaRequest
          .get('/api/menu')
          .then((menus) => {
            this.menus = menus.items.map((item) => ({ text: item.name, value: item.id }));
          });
        const galleriesPromise = JinyaRequest
          .get('/api/media/gallery')
          .then((galleries) => {
            this.galleries = galleries.items.map((item) => ({ text: item.name, value: item.slug }));
          });
        const filesPromise = JinyaRequest
          .get('/api/media/file')
          .then((files) => {
            this.files = files.items.map((item) => ({ text: item.name, value: item.id }));
          });
        const themePromise = JinyaRequest
          .get(`/api/theme/${this.$route.params.name}`)
          .then((theme) => {
            this.theme = theme;
            DOMUtils.changeTitle(theme.displayName);
          });

        await Promise.all([
          linksPromise,
          themePromise,
          pagePromise,
          segmentPagePromise,
          formsPromise,
          menusPromise,
          galleriesPromise,
          filesPromise,
        ]);
        this.loading = false;
      }
    },
    methods: {
      createSelectValue(item) {
        if (isArray(item)) {
          return {
            value: item[0].slug || item[0].id,
            text: item[0].name,
          };
        }

        if (isObject(item)) {
          return {
            value: item.slug || item.id,
            text: item.name,
          };
        }

        return { value: '', text: '' };
      },
      createLinkValue(item) {
        return {
          slug: item.value,
        };
      },
      selected(item, links, key) {
        if (links === undefined || links === null) {
          links = {};
        }
        links[key] = this.createLinkValue(item);
      },
      cancel() {
        this.$router.push(Routes.Configuration.Frontend.Theme.Overview);
      },
      async save() {
        this.enable = false;
        try {
          this.state = 'loading';
          this.message = Translator.message('configuration.frontend.themes.links.saving', this.theme);

          await JinyaRequest.put(`/api/theme/${this.$route.params.name}/links`, this.links);

          this.message = Translator.message('configuration.frontend.themes.links.saved', this.theme);
          this.state = 'success';

          await Timing.wait();
          this.$router.push(Routes.Configuration.Frontend.Theme.Overview);
        } catch (e) {
          this.enable = true;
          this.message = Translator.validator(`configuration.frontend.themes.links.${e.message}`);
          this.state = 'error';
        }
      },
    },
  };
</script>

<style lang="scss" scoped>
    .jinya-fieldset {
        min-width: 25%;
        flex: 0 0 25%;
    }
</style>
