<template>
  <jinya-form class="jinya-menu-builder__settings" @submit="$emit('done')">
    <jinya-input label="configuration.frontend.menus.builder.settings.title" v-model="item.title" :required="true"
                 :validation-message="'configuration.frontend.menus.builder.settings.title.empty'|jvalidator"/>
    <jinya-input :label="urlLabel" v-model="item.route.url" v-if="item.pageType !== 'empty'" :required="true"
                 :validation-message="urlValidationMessage|jvalidator"/>
    <jinya-checkbox class="jinya-menu-builder__settings-checkbox" v-model="item.highlighted"
                    label="configuration.frontend.menus.builder.settings.highlighted"/>
    <jinya-button slot="buttons" label="configuration.frontend.menus.builder.settings.save" :is-primary="true"
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
