<template>
    <div class="jinya-menu-flyout__menu">
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

<style lang="scss">
    .jinya-menu-flyout__menu {
        background: $primary-lightest;
        display: none;
        width: auto;
        justify-content: center;
        box-shadow: 0 4px 4px #00000029;
        top: 3.5rem;
        padding: 0 2rem;
        position: absolute;
        left: 50%;
        transform: translateX(-50%);
        transition: all 0.3s;
        border-radius: 4px;

        &::after {
            content: "";
            border-left: 1rem solid transparent;
            border-right: 1rem solid transparent;
            border-bottom: 1rem solid $primary-lightest;
            transform: translateX(-50%);
            position: absolute;
            left: 50%;
            top: -1rem;
        }
    }
</style>
