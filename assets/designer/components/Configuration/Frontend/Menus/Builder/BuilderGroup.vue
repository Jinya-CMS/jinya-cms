<template>
  <div :style="{'margin-left':`${item.nestingLevel * 2}rem`, 'width':`calc(100% - ${item.nestingLevel * 2}rem)`}">
    <span :class="{'is--highlighted': item.highlighted, 'is--settings-open': showSettings}"
          class="jinya-menu-builder__item">
      <jinya-icon-button :is-primary="item.highlighted" :is-secondary="!item.highlighted"
                         :title="'configuration.frontend.menus.builder.decrease_alignment'|jmessage"
                         @click="$emit('decrease')" icon="chevron-left"
                         v-if="allowDecrease"/>
      {{item.title}}
      <jinya-icon-button :is-primary="item.highlighted" :is-secondary="!item.highlighted"
                         :title="'configuration.frontend.menus.builder.increase_alignment'|jmessage"
                         @click="$emit('increase')" icon="chevron-right"
                         v-if="allowIncrease"/>
      <jinya-icon-button :is-primary="item.highlighted" :is-secondary="!item.highlighted"
                         :title="'configuration.frontend.menus.builder.edit_entry'|jmessage"
                         @click="toggleSettingsClick" class="is--right"
                         icon="pencil"/>
    </span>
    <transition enter-active-class="is--enter-active" leave-to-class="is--leave-to">
      <jinya-menu-builder-settings-editor :item="item" @done="editSettingsDone" v-if="showSettings && enable"/>
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

<style lang="scss" scoped>
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
