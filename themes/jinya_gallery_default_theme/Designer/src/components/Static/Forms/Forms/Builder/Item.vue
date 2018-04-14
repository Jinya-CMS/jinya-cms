<template>
    <div class="jinya-form-builder__component">
        <jinya-form-builder-text-type :label="item.label" :required="item.options.required"
                                      class="jinya-form-builder__input" v-if="item.type.endsWith('TextType')"/>
        <jinya-form-builder-email-type :label="item.label" :required="item.options.required"
                                       class="jinya-form-builder__input" v-else-if="item.type.endsWith('EmailType')"/>
        <jinya-form-builder-checkbox-type :label="item.label" v-else-if="item.type.endsWith('CheckboxType')"
                                          class="jinya-form-builder__input" :required="item.options.required"/>
        <jinya-form-builder-choice-type :label="item.label" :required="item.options.required"
                                        class="jinya-form-builder__input" :choices="item.options.choices"
                                        v-else-if="item.type.endsWith('ChoiceType')"/>
        <jinya-form-builder-text-area-type :label="item.label" v-else-if="item.type.endsWith('TextareaType')"
                                           class="jinya-form-builder__input" :required="item.options.required"/>
        <jinya-form-builder-settings-editor :item="item" v-if="settingsAvailable"/>
    </div>
</template>

<script>
  import JinyaFormBuilderTextType from "@/components/Static/Forms/Forms/Builder/TextType";
  import JinyaFormBuilderEmailType from "@/components/Static/Forms/Forms/Builder/EmailType";
  import JinyaFormBuilderCheckboxType from "@/components/Static/Forms/Forms/Builder/CheckboxType";
  import JinyaFormBuilderChoiceType from "@/components/Static/Forms/Forms/Builder/ChoiceType";
  import JinyaFormBuilderTextAreaType from "@/components/Static/Forms/Forms/Builder/TextAreaType";
  import JinyaFormBuilderSettingsEditor from "@/components/Static/Forms/Forms/Builder/SettingsEditor";

  export default {
    name: "jinya-form-builder-item",
    components: {
      JinyaFormBuilderSettingsEditor,
      JinyaFormBuilderTextAreaType,
      JinyaFormBuilderChoiceType,
      JinyaFormBuilderCheckboxType,
      JinyaFormBuilderEmailType,
      JinyaFormBuilderTextType
    },
    props: {
      item: {
        type: Object,
        required: true
      },
      settingsAvailable: {
        type: Boolean,
        default() {
          return true;
        }
      }
    }
  }
</script>

<style scoped lang="scss">
    .jinya-form-builder__component {
        width: 100%;
        border: 1px solid $primary;
        border-radius: 2px;
        padding: 1rem 1rem 0;
        margin-bottom: 1rem;
        display: block;

        .jinya-form-builder__input {
            width: 100%;
            display: block;

            &.jinya-checkbox {
                margin-bottom: 1rem;
            }
        }
    }
</style>