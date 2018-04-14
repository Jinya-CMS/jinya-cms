<template>
    <div class="jinya-form-builder">
        <jinya-loader :loading="loading"/>
        <jinya-editor v-if="!loading">
            <jinya-form save-label="static.forms.forms.builder.save" cancel-label="static.forms.forms.builder.cancel">
                <jinya-editor-pane>
                    <draggable class="jinya-form-builder__draggable" :options="originOptions">
                        <jinya-form-builder-item :settings-available="false" :item="{type: 'TextType', options: {}}"/>
                        <jinya-form-builder-item :settings-available="false" :item="{type: 'EmailType', options: {}}"/>
                        <jinya-form-builder-item :settings-available="false"
                                                 :item="{type: 'CheckboxType', options: {}}"/>
                        <jinya-form-builder-item :settings-available="false"
                                                 :item="{type: 'ChoiceType', options: {choices:[]}}"/>
                        <jinya-form-builder-item :settings-available="false"
                                                 :item="{type: 'TextareaType', options: {}}"/>
                    </draggable>
                </jinya-editor-pane>
                <jinya-editor-pane>
                    <draggable @change="itemsChange" class="jinya-form-builder__draggable" :options="destinationOptions"
                               :list="items">
                        <jinya-form-builder-item :item="item" v-for="item in items"/>
                    </draggable>
                </jinya-editor-pane>
            </jinya-form>
        </jinya-editor>
    </div>
</template>

<script>
  import JinyaRequest from "@/framework/Ajax/JinyaRequest";
  import DOMUtils from "@/framework/Utils/DOMUtils";
  import Translator from "@/framework/i18n/Translator";
  import EventBus from "@/framework/Events/EventBus";
  import Events from "@/framework/Events/Events";
  import JinyaEditor from "@/framework/Markup/Form/Editor";
  import JinyaEditorPane from "@/framework/Markup/Form/EditorPane";
  import JinyaForm from "@/framework/Markup/Form/Form";
  import JinyaFormBuilderTextType from "@/components/Static/Forms/Forms/Builder/TextType";
  import JinyaFormBuilderEmailType from "@/components/Static/Forms/Forms/Builder/EmailType";
  import JinyaFormBuilderCheckboxType from "@/components/Static/Forms/Forms/Builder/CheckboxType";
  import JinyaFormBuilderChoiceType from "@/components/Static/Forms/Forms/Builder/ChoiceType";
  import JinyaFormBuilderTextAreaType from "@/components/Static/Forms/Forms/Builder/TextAreaType";
  import draggable from 'vuedraggable';
  import JinyaFormBuilderItem from "@/components/Static/Forms/Forms/Builder/Item";
  import JinyaLoader from "@/framework/Markup/Loader";

  export default {
    name: "Builder",
    components: {
      JinyaLoader,
      JinyaFormBuilderItem,
      JinyaFormBuilderTextAreaType,
      JinyaFormBuilderChoiceType,
      JinyaFormBuilderCheckboxType,
      JinyaFormBuilderEmailType,
      JinyaFormBuilderTextType,
      JinyaForm,
      JinyaEditorPane,
      JinyaEditor,
      draggable
    },
    methods: {
      async itemsChange(data) {
        const added = data.added;
        const moved = data.moved;
        const slug = this.$route.params.slug;

        if (added) {
        }

        if (moved) {
          const newPosition = moved.newIndex;
          const oldPosition = moved.oldIndex;

          await JinyaRequest.put(`/api/form/${slug}/move/${oldPosition}/to/${newPosition}`);
        }
      }
    },
    computed: {
      originOptions() {
        return {
          sort: false,
          group: {
            name: 'edit',
            pull: 'clone',
            put: false
          }
        }
      },
      destinationOptions() {
        return {
          handle: '.jinya-form-builder__component',
          group: {
            name: 'edit',
            pull: false,
            put: true
          }
        }
      }
    },
    async mounted() {
      this.loading = true;
      const form = await JinyaRequest.get(`/api/form/${this.form.slug}`);
      this.form = form;

      DOMUtils.changeTitle(Translator.message('static.forms.forms.builder.title', form));
      EventBus.$emit(Events.header.change, Translator.message('static.forms.forms.builder.title', form));

      this.items = await JinyaRequest.get(`/api/form/${this.form.slug}/items`);
      this.loading = false;
    },
    data() {
      return {
        form: {
          title: '',
          slug: this.$route.params.slug
        },
        message: '',
        state: '',
        enable: true,
        items: [],
        loading: false
      };
    }
  }
</script>

<style scoped lang="scss">
    .jinya-form-builder__draggable {
        width: 100%;
    }
</style>