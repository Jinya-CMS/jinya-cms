<template>
    <jinya-loader :loading="loading" v-if="loading"/>
    <div class="jinya-theme-configuration" v-else>
        <jinya-message :message="message" :state="state"/>
        <jinya-form :enable="!loading" @back="back" @submit="save"
                    cancel-label="configuration.frontend.themes.configuration.cancel"
                    save-label="configuration.frontend.themes.configuration.save" v-if="!initial">
            <jinya-tab-container :items="form.groups" @select="selected = $event">
                <template v-for="tab in form.groups">
                    <jinya-tab :is-selected="selected === tab.name" :key="tab.name">
                        <jinya-theme-configuration-field :enable="!loading" :key="`${tab.name}.${field.name}`"
                                                         :label="field.label" :name="`${tab.name}.${field.name}`"
                                                         :type="field.type"
                                                         :value="getValue(`${tab.name}.${field.name}`)"
                                                         @changed="changed"
                                                         v-for="field in tab.fields"/>

                        <jinya-fieldset :key="`${tab.name}.${group.name}`" :legend="group.title"
                                        v-for="group in tab.groups">
                            <jinya-theme-configuration-field
                                :enable="!loading"
                                :key="`${tab.name}.${group.name}.${field.name}`"
                                :label="field.label"
                                :name="`${tab.name}.${group.name}.${field.name}`"
                                :type="field.type"
                                :value="getValue(`${tab.name}.${group.name}.${field.name}`)"
                                @changed="changed"
                                v-for="field in group.fields"/>
                        </jinya-fieldset>
                    </jinya-tab>
                </template>
            </jinya-tab-container>
        </jinya-form>
    </div>
</template>

<script>
  import JinyaForm from '@/framework/Markup/Form/Form';
  import JinyaRequest from '@/framework/Ajax/JinyaRequest';
  import Events from '@/framework/Events/Events';
  import EventBus from '@/framework/Events/EventBus';
  import DOMUtils from '@/framework/Utils/DOMUtils';
  import JinyaFieldset from '@/framework/Markup/Form/Fieldset';
  import ObjectUtils from '@/framework/Utils/ObjectUtils';
  import JinyaMessage from '@/framework/Markup/Validation/Message';
  import Translator from '@/framework/i18n/Translator';
  import JinyaThemeConfigurationField from '@/components/Configuration/Frontend/Themes/Configuration/Field';
  import Timing from '@/framework/Utils/Timing';
  import Routes from '@/router/Routes';
  import ArrayUtils from '@/framework/Utils/ArrayUtils';
  import JinyaTab from '@/framework/Markup/Tab/Tab';
  import JinyaTabContainer from '@/framework/Markup/Tab/TabContainer';
  import JinyaLoader from '@/framework/Markup/Waiting/Loader';

  export default {
    name: 'Configuration',
    components: {
      JinyaLoader,
      JinyaTabContainer,
      JinyaTab,
      JinyaThemeConfigurationField,
      JinyaMessage,
      JinyaFieldset,
      JinyaForm,
    },
    data() {
      return {
        form: {
          title: '',
          groups: [],
        },
        theme: {},
        initial: true,
        message: '',
        state: '',
        files: {},
        selected: '',
        loading: true,
      };
    },
    methods: {
      async save() {
        this.loading = true;
        this.state = 'loading';
        this.message = Translator.message('configuration.frontend.themes.configuration.saving', this.theme);
        await JinyaRequest.put(`/api/theme/${this.theme.name}`, {
          config: this.theme.config,
        });

        this.state = 'success';
        this.message = Translator.message('configuration.frontend.themes.configuration.saved', this.theme);
        await Timing.wait();

        if (Object.keys(this.files).length > 0) {
          this.state = 'loading';
          await ArrayUtils.asyncForeach(Object.keys(this.files), async (key) => {
            const file = this.files[key];
            if (file.file) {
              this.message = Translator.message('configuration.frontend.themes.configuration.uploading', {
                setting: file.name,
              });
              await JinyaRequest.put(`/api/theme/${this.theme.name}/file/${key}`, file.file);
            } else {
              this.message = Translator.message('configuration.frontend.themes.configuration.deleting', {
                setting: file.name,
              });
              await JinyaRequest.delete(`/api/theme/${this.theme.name}/file/${key}`);
            }
          });

          this.state = 'success';
          this.message = Translator.message('configuration.frontend.themes.configuration.uploaded', this.theme);
          await Timing.wait();
        }
        this.loading = false;

        this.$router.push(Routes.Configuration.Frontend.Theme.Overview.name);
      },
      getValue(key) {
        return ObjectUtils.valueByKeypath(this.theme.config, key, '');
      },
      changed(data) {
        if (data.type === 'file') {
          if (data.file !== null) {
            this.files[data.name] = {
              file: data.value,
              name: data.label,
            };
          } else {
            this.files[data.name] = {
              file: null,
              name: data.label,
            };
          }
        } else {
          this.theme.config = ObjectUtils.setValueByKeyPath(this.theme.config, data.name, data.value);
        }
      },
      back() {
        this.$router.push(Routes.Configuration.Frontend.Theme.Overview);
      },
    },
    async mounted() {
      try {
        this.state = 'loading';
        this.message = Translator.message('configuration.frontend.themes.configuration.loading');
        this.theme = await JinyaRequest.get(`/api/theme/${this.$route.params.name}`);

        // eslint-disable-next-line max-len
        this.message = Translator.message('configuration.frontend.themes.configuration.loading_configuration', this.theme);
        const form = await JinyaRequest.get(`/api/theme/${this.$route.params.name}/form/config`);
        EventBus.$emit(Events.header.change, form.title);

        this.form = form;
        DOMUtils.changeTitle(form.title);
        this.state = '';
        this.message = '';
      } catch (e) {
        this.state = 'error';
        this.message = e.message;
      }
      this.initial = false;
      this.loading = false;
    },
  };
</script>

<style lang="scss" scoped>
    .jinya-theme-configuration {
        padding-top: 1em;
    }
</style>
