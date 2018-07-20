<template>
    <div class="jinya-menu-builder__settings">
        <jinya-input label="configuration.frontend.menus.builder.settings.title" v-model="item.title"/>
        <jinya-input :label="urlLabel" v-model="item.route.url" v-if="item.pageType !== 'empty'"/>
        <jinya-checkbox class="jinya-menu-builder__settings-checkbox"
                        label="configuration.frontend.menus.builder.settings.highlighted" v-model="item.highlighted"/>
        <jinya-button label="configuration.frontend.menus.builder.settings.save" :is-primary="true"
                      @click="$emit('done')"/>
    </div>
</template>

<script>
  import JinyaInput from '@/framework/Markup/Form/Input';
  import JinyaCheckbox from '@/framework/Markup/Form/Checkbox';
  import JinyaButton from '@/framework/Markup/Button';

  export default {
    name: 'jinya-menu-builder-settings-editor',
    components: {
      JinyaButton,
      JinyaCheckbox,
      JinyaInput,
    },
    computed: {
      urlLabel() {
        const pageType = this.item.pageType === 'external' ? 'target' : 'display';
        return `configuration.frontend.menus.builder.settings.${pageType}_url`;
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
    }

    .jinya-menu-builder__settings-checkbox {
        width: 100%;
        display: block;
        margin-bottom: 1rem;
    }
</style>
