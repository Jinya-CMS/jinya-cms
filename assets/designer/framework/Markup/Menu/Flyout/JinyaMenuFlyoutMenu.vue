<template>
  <div :class="{'is--open': isOpen}" class="jinya-menu-flyout__menu" v-if="isOpen">
    <slot/>
  </div>
</template>

<script>
  export default {
    name: 'jinya-menu-flyout-menu',
    props: {
      isOpen: {
        type: Boolean,
        default() {
          return false;
        },
      },
    },
  };
</script>

<style lang="scss" scoped>
  .jinya-menu-flyout__menu {
    background: $primary;
    display: none;
    position: fixed;
    width: 100%;
    justify-content: center;

    &.is--open {
      display: flex;
    }
  }
</style>
