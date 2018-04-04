<template>
    <div class="jinya-theme-configuration">
        <jinya-message :message="message" :state="state"/>
        <jinya-form save-label="configuration.frontend.themes.configuration.save" @submit="save" v-if="!initial"
                    cancel-label="configuration.frontend.themes.configuration.cancel" :enable="!loading">
            <jinya-tab-container :items="form.groups" @select="selected = $event">
                <template v-for="tab in form.groups">
                    <jinya-tab :is-selected="selected === tab.name">
                        <jinya-theme-configuration-field v-for="field in tab.fields" :key="`${tab.name}.${field.name}`"
                                                         :label="field.label" :name="`${tab.name}.${field.name}`"
                                                         :type="field.type" :enable="!loading" @changed="changed"
                                                         :value="getValue(`${tab.name}.${field.name}`)"/>

                        <jinya-fieldset v-for="group in tab.groups" :legend="group.title"
                                        :key="`${tab.name}.${group.name}`">
                            <jinya-theme-configuration-field v-for="field in group.fields" :enable="!loading"
                                                             :type="field.type" :label="field.label" @changed="changed"
                                                             :key="`${tab.name}.${group.name}.${field.name}`"
                                                             :name="`${tab.name}.${group.name}.${field.name}`"
                                                             :value="getValue(`${tab.name}.${group.name}.${field.name}`)"/>
                        </jinya-fieldset>
                    </jinya-tab>
                </template>
            </jinya-tab-container>
        </jinya-form>
    </div>
</template>

<script>
  import JinyaForm from "@/components/Framework/Markup/Form/Form";
  import JinyaRequest from "@/components/Framework/Ajax/JinyaRequest";
  import Events from "@/components/Framework/Events/Events";
  import EventBus from "@/components/Framework/Events/EventBus";
  import DOMUtils from "@/components/Framework/Utils/DOMUtils";
  import JinyaInput from "@/components/Framework/Markup/Form/Input";
  import JinyaChoice from "@/components/Framework/Markup/Form/Choice";
  import JinyaFileInput from "@/components/Framework/Markup/Form/FileInput";
  import JinyaCheckbox from "@/components/Framework/Markup/Form/Checkbox";
  import JinyaFieldset from "@/components/Framework/Markup/Form/Fieldset";
  import ObjectUtils from "@/components/Framework/Utils/ObjectUtils";
  import JinyaMessage from "@/components/Framework/Markup/Validation/Message";
  import Translator from "@/components/Framework/i18n/Translator";
  import JinyaThemeConfigurationField from "@/components/Configuration/Frontend/Themes/Configuration/Field";
  import Timing from "@/components/Framework/Utils/Timing";
  import Routes from "@/router/Routes";
  import ArrayUtils from "@/components/Framework/Utils/ArrayUtils";
  import JinyaTab from "@/components/Framework/Markup/Tab/Tab";
  import JinyaTabContainer from "@/components/Framework/Markup/Tab/TabContainer";

  export default {
    name: "Configuration",
    components: {
      JinyaTabContainer,
      JinyaTab,
      JinyaThemeConfigurationField,
      JinyaMessage, JinyaFieldset, JinyaCheckbox, JinyaFileInput, JinyaChoice, JinyaInput, JinyaForm
    },
    data() {
      return {
        form: {
          title: '',
          groups: []
        },
        theme: {},
        initial: true,
        message: '',
        state: '',
        files: {},
        selected: ''
      };
    },
    methods: {
      async save() {
        this.loading = true;
        this.state = 'loading';
        this.message = Translator.message('configuration.frontend.themes.configuration.saving', this.theme);
        await JinyaRequest.put(`/api/theme/${this.theme.name}`, {
          config: this.theme.config
        });

        this.state = 'success';
        this.message = Translator.message('configuration.frontend.themes.configuration.saved', this.theme);
        await Timing.wait();

        if (Object.keys(this.files).length > 0) {
          this.state = 'loading';
          await ArrayUtils.asyncForeach(Object.keys(this.files), async key => {
            const file = this.files[key];
            if (file.file) {
              this.message = Translator.message('configuration.frontend.themes.configuration.uploading', {
                setting: file.label
              });
              await JinyaRequest.put(`/api/theme/${this.theme.name}/file/${key}`, file.file);
            } else {
              this.message = Translator.message('configuration.frontend.themes.configuration.deleting', {
                setting: file.label
              });
              await JinyaRequest.delete(`/api/theme/${this.theme.name}/file/${key}`);
            }
          });

          this.state = 'success';
          this.message = Translator.message('configuration.frontend.themes.configuration.uploaded', this.theme);
          await Timing.wait();
        }
        this.loading = false;

        this.$router.push(Routes.Configuration.Frontend.Theme.Overview);
      },
      getValue(key) {
        return ObjectUtils.valueByKeypath(this.theme.config, key, '');
      },
      changed(data) {
        if (data.type === 'file') {
          if (data.file === null) {
            this.files[data.name] = {
              file: data.file,
              name: data.label
            };
          } else {
            this.files[data.name] = {
              file: null,
              name: data.label
            };
          }
        } else {
          this.theme.config = ObjectUtils.setValueByKeyPath(this.theme.config, data.name, data.value);
        }
      }
    },
    async mounted() {
      try {
        this.state = 'loading';
        this.message = Translator.message('configuration.frontend.themes.configuration.loading');
        this.theme = await JinyaRequest.get(`/api/theme/${this.$route.params.name}`);

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
    }
  }
</script>

<style scoped lang="scss">
    .jinya-theme-configuration {
        padding-top: 1em;
    }
</style>