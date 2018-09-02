<template>
  <div :style="{'margin-left':`${item.nestingLevel * 2}rem`, 'width':`calc(100% - ${item.nestingLevel * 2}rem)`}">
    <span class="jinya-menu-builder__item"
          :class="{'is--highlighted': item.highlighted, 'is--settings-open': showSettings}">
      <jinya-icon-button v-if="allowDecrease" :is-primary="item.highlighted" :is-secondary="!item.highlighted"
                         @click="$emit('decrease')" icon="chevron-left"
                         :title="'configuration.frontend.menus.builder.decrease_alignment'|jmessage"/>
      {{item.title}}
      <jinya-icon-button v-if="allowIncrease" :is-primary="item.highlighted" :is-secondary="!item.highlighted"
                         @click="$emit('increase')" icon="chevron-right"
                         :title="'configuration.frontend.menus.builder.increase_alignment'|jmessage"/>
      <jinya-icon-button :is-primary="item.highlighted" :is-secondary="!item.highlighted" class="is--right"
                         @click="toggleSettingsClick" icon="pencil"
                         :title="'configuration.frontend.menus.builder.edit_entry'|jmessage"/>
    </span>
    <transition enter-active-class="is--enter-active" leave-to-class="is--leave-to">
      <jinya-menu-builder-settings-editor :item="item" v-if="showSettings && enable" @done="editSettingsDone"/>
    </transition>
  </div>
</template>

<script>
  import JinyaIconButton from '@/framework/Markup/IconButton';
  import JinyaMenuBuilderSettingsEditor from '@/components/Configuration/Frontend/Menus/Builder/SettingsEditor';

  export default {
    name: 'jinya-menu-builder-group',
    components: {
      JinyaMenuBuilderSettingsEditor,
      JinyaIconButton,
    },
    computed: {
      draggableOptions() {
        return {
          group: 'menu',
        };
      },
    },
    props: {
      item: {
        type: Object,
        required: true,
      },
      allowIncrease: {
        type: Boolean,
        default() {
          return true;
        },
      },
      allowDecrease: {
        type: Boolean,
        default() {
          return false;
        },
      },
      enable: {
        type: Boolean,
        default() {
          return true;
        },
      },
    },
    methods: {
      editSettingsDone() {
        this.showSettings = false;
        this.$emit('edit-done', this.item);
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
  };
</script>

<style scoped lang="scss">
  .jinya-menu-builder__item {
    display: flex;
    padding: 0.5rem 0.5rem 0.5rem 1rem;
    background: $secondary-lighter;
    color: $gray-800;
    border-radius: 3px;
    align-items: center;
    cursor: move;
    margin-bottom: 0.1rem;

    .is--right {
      margin-left: auto;
    }

    &.is--highlighted {
      background: $primary-lighter;
    }

    &.is--settings-open {
      border-bottom-left-radius: 0;
      border-bottom-right-radius: 0;
    }
  }

  .jinya-menu-builder__settings {
    transition: opacity 0.3s;

    &.is--leave-to,
    &.is--enter-active {
      opacity: 0;
    }
  }
</style>
