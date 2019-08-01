<template>
    <jinya-form @submit="$emit('done')" class="jinya-menu-builder__settings">
        <jinya-input :required="true"
                     :validation-message="'configuration.frontend.menus.builder.settings.title.empty'|jvalidator"
                     label="configuration.frontend.menus.builder.settings.title"
                     v-model="item.title"/>
        <jinya-input :label="urlLabel" :required="true" :validation-message="urlValidationMessage|jvalidator"
                     v-if="item.pageType !== 'empty'"
                     v-model="item.route.url"/>
        <jinya-checkbox class="jinya-menu-builder__settings-checkbox"
                        label="configuration.frontend.menus.builder.settings.highlighted"
                        v-model="item.highlighted"/>
        <jinya-button :is-primary="true" label="configuration.frontend.menus.builder.settings.save" slot="buttons"
                      type="submit"/>
    </jinya-form>
</template>

<script>
  import JinyaInput from '@/framework/Markup/Form/Input';
  import JinyaCheckbox from '@/framework/Markup/Form/Checkbox';
  import JinyaButton from '@/framework/Markup/Button';
  import JinyaForm from '@/framework/Markup/Form/Form';

  export default {
    name: 'jinya-menu-builder-settings-editor',
    components: {
      JinyaForm,
      JinyaButton,
      JinyaCheckbox,
      JinyaInput,
    },
    computed: {
      urlLabel() {
        const pageType = this.item.pageType === 'external' ? 'target' : 'display';
        return `configuration.frontend.menus.builder.settings.${pageType}_url`;
      },
      urlValidationMessage() {
        const pageType = this.item.pageType === 'external' ? 'target' : 'display';
        return `configuration.frontend.menus.builder.settings.${pageType}_url.empty`;
      },
    },
    props: {
      item: {
        type: Object,
        required: true,
      },
    },
  };
</script>

<style lang="scss" scoped>
    .jinya-menu-builder__settings {
        padding: 1rem;
        border: 2px solid $secondary-lighter;
        border-top-width: 0;
        margin-top: -0.1rem;
    }

    .jinya-menu-builder__settings-checkbox {
        width: 100%;
        display: block;
        margin-bottom: 1rem;
    }
</style>
