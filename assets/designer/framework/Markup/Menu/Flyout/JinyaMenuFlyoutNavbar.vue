<template>
    <ul :class="{'is--open': isOpen}" class="jinya-menu-flyout__navbar">
        <slot/>
    </ul>
</template>

<script>
  export default {
    name: 'jinya-menu-flyout-navbar',
    props: {
      isOpen: {
        type: Boolean,
        required: true,
        default() {
          return false;
        },
      },
    },
  };
</script>

<style lang="scss" scoped>
    .jinya-menu-flyout__navbar {
        width: 100%;
        display: none;
        border-bottom: 3px solid $primary;
        background: $white;
        position: fixed;
        top: 0;
        left: 75px;
        margin: 0;
        padding: 0;
        justify-content: center;
        line-height: 57px;

        &.is--open {
            display: flex;
        }
    }
</style>
