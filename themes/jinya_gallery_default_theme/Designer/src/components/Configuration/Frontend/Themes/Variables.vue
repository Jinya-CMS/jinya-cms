<template>
    <jinya-editor>
        <jinya-message :message="message" :state="state"/>
        <jinya-form @submit="save" save-label="configuration.frontend.themes.variables.save" :enable="!loading"
                    cancel-label="configuration.frontend.themes.variables.cancel" class="jinya-form--variables"
                    @back="back">
            <jinya-input v-for="field in filteredFields" :label="field.label" :placeholder="field.value"
                         :key="field.key" :value="getValue(field.key)" class="jinya-input--variables" :enable="!loading"
                         @change="value => changeVariable(field, value)"/>
        </jinya-form>
    </jinya-editor>
</template>

<script>
  import JinyaEditor from '@/framework/Markup/Form/Editor';
  import JinyaMessage from '@/framework/Markup/Validation/Message';
  import JinyaForm from '@/framework/Markup/Form/Form';
  import JinyaInput from '@/framework/Markup/Form/Input';
  import JinyaRequest from '@/framework/Ajax/JinyaRequest';
  import Translator from '@/framework/i18n/Translator';
  import DOMUtils from '@/framework/Utils/DOMUtils';
  import EventBus from '@/framework/Events/EventBus';
  import Events from '@/framework/Events/Events';
  import Routes from '@/router/Routes';

  export default {
    name: 'Variables',
    components: {
      JinyaInput,
      JinyaForm,
      JinyaMessage,
      JinyaEditor,
    },
    data() {
      return {
        fields: [],
        filteredFields: [],
        message: '',
        state: '',
        variables: [],
        loading: false,
      };
    },
    async mounted() {
      try {
        this.loading = true;
        this.state = 'loading';
        this.message = Translator.message('configuration.frontend.themes.variables.loading');
        const theme = await JinyaRequest.get(`/api/theme/${this.$route.params.name}`);
        this.theme = theme;
        this.variables = theme.scssVariables;
        DOMUtils.changeTitle(Translator.message('configuration.frontend.themes.variables.title', theme));
        EventBus.$emit(Events.header.change, Translator.message('configuration.frontend.themes.variables.title', theme));

        this.message = Translator.message('configuration.frontend.themes.variables.loading_variables', theme);
        const fields = await JinyaRequest.get(`/api/theme/${this.$route.params.name}/form/variables`);
        const mappedFields = this.mapFields(fields);
        this.fields = mappedFields;
        this.filteredFields = mappedFields;
        this.state = '';
        this.message = '';
        EventBus.$on(Events.search.triggered, value => this.search(value.keyword));
      } catch (e) {
        this.message = e.message;
        this.state = 'error';
      }

      this.loading = false;
    },
    methods: {
      changeVariable(field, value) {
        this.variables[field.key] = value;
      },
      back() {
        this.$router.push(Routes.Configuration.Frontend.Theme.Overview);
      },
      async save() {
        this.loading = true;
        try {
          this.state = 'loading';
          this.message = Translator.message('configuration.frontend.themes.variables.saving', this.theme);
          await JinyaRequest.put(`/api/theme/${this.$route.params.name}`, {
            scss: this.variables,
          });
          this.state = '';
          this.message = '';
        } catch (e) {
          this.state = 'error';
          this.message = e.message;
        }

        this.loading = false;
      },
      getValue(key) {
        return this.variables[key];
      },
      mapFields(fields) {
        return Object.keys(fields).map(key => ({
          key,
          value: fields[key],
          label: key.replace(/-/g, ' ').replace(/^\$/, '').replace(/(\b[a-z](?!\s))/g, x => x.toUpperCase()),
        }));
      },
      search(keyword) {
        this.filteredFields = [];
        this.$forceUpdate();
        this.filteredFields = this.fields.filter(field => field.key.indexOf(keyword) > -1);
      },
    },
  };
</script>

<style scoped lang="scss">
    .jinya-form--variables {
        display: flex;
        margin-bottom: 1em;
    }

    .jinya-input--variables {
        flex: 0 0 100% / 3 - 1%;
        margin-right: 0.5%;
        margin-left: 0.5%;

        &:first-child,
        &:nth-child(n) {
            margin-left: 0;
            flex-basis: 100% / 3 - 0.5%;
        }

        &:nth-child(3n) {
            margin-right: 0;
            flex-basis: 100% / 3 - 0.5%;
        }
    }
</style>
