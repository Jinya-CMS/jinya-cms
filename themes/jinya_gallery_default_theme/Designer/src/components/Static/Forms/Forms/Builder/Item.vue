<template>
    <div class="jinya-form-builder__component">
        <div class="jinya-icon-bar">
            <jinya-icon-button @click="toggleSettingsClick" icon="settings" :is-primary="true" :is-disabled="!enable"
                               v-if="settingsAvailable"/>
        </div>
        <jinya-form-builder-text-type :label="item.label" :required="item.options.required" :enable="enable"
                                      class="jinya-form-builder__input" v-if="item.type.endsWith('TextType')"/>
        <jinya-form-builder-email-type :label="item.label" :required="item.options.required" :enable="enable"
                                       class="jinya-form-builder__input" v-else-if="item.type.endsWith('EmailType')"/>
        <jinya-form-builder-checkbox-type :label="item.label" v-else-if="item.type.endsWith('CheckboxType')"
                                          :enable="enable" class="jinya-form-builder__input"
                                          :required="item.options.required"/>
        <jinya-form-builder-choice-type :label="item.label" :required="item.options.required" :enable="enable"
                                        class="jinya-form-builder__input" :choices="item.options.choices"
                                        v-else-if="item.type.endsWith('ChoiceType')"/>
        <jinya-form-builder-text-area-type :label="item.label" v-else-if="item.type.endsWith('TextareaType')"
                                           :enable="enable" class="jinya-form-builder__input"
                                           :required="item.options.required"/>
        <transition enter-active-class="is--enter-active" leave-to-class="is--leave-to">
            <jinya-form-builder-settings-editor :item="item" v-if="settingsAvailable && showSettings && enable"
                                                @done="editSettingsDone"/>
        </transition>
    </div>
</template>

<script>
  import JinyaFormBuilderTextType from '@/components/Static/Forms/Forms/Builder/TextType';
  import JinyaFormBuilderEmailType from '@/components/Static/Forms/Forms/Builder/EmailType';
  import JinyaFormBuilderCheckboxType from '@/components/Static/Forms/Forms/Builder/CheckboxType';
  import JinyaFormBuilderChoiceType from '@/components/Static/Forms/Forms/Builder/ChoiceType';
  import JinyaFormBuilderTextAreaType from '@/components/Static/Forms/Forms/Builder/TextAreaType';
  import JinyaFormBuilderSettingsEditor from '@/components/Static/Forms/Forms/Builder/SettingsEditor';
  import JinyaIconButton from '@/framework/Markup/IconButton';

  export default {
    name: 'jinya-form-builder-item',
    components: {
      JinyaIconButton,
      JinyaFormBuilderSettingsEditor,
      JinyaFormBuilderTextAreaType,
      JinyaFormBuilderChoiceType,
      JinyaFormBuilderCheckboxType,
      JinyaFormBuilderEmailType,
      JinyaFormBuilderTextType,
    },
    methods: {
      editSettingsDone() {
        this.showSettings = false;
        this.$emit('edit-done', {
          data: this.item,
          position: this.position,
        });
      },
      toggleSettingsClick() {
        this.showSettings = !this.showSettings;
        this.$emit('toggle-settings', this.item);
      },
    },
    data() {
      return {
        showSettings: false,
      };
    },
    props: {
      item: {
        type: Object,
        required: true,
      },
      position: {
        type: Number,
      },
      settingsAvailable: {
        type: Boolean,
        default() {
          return true;
        },
      },
      enable: {
        type: Boolean,
        default() {
          return true;
        },
      },
    },
  };
</script>

<style scoped lang="scss">
    .jinya-form-builder__component {
        width: 100%;
        border: 1px solid $primary;
        border-radius: 2px;
        padding: 1rem 1rem 0;
        margin-bottom: 1rem;
        display: block;
        position: relative;

        .jinya-form-builder__input {
            width: 100%;
            display: block;

            &.jinya-checkbox {
                margin-bottom: 1rem;
            }
        }
    }

    .jinya-icon-bar {
        position: absolute;
        right: 0;
        top: 0;
    }

    .jinya-form-builder__settings {
        transition: opacity 0.3s;

        &.is--leave-to,
        &.is--enter-active {
            opacity: 0;
        }
    }
</style>
