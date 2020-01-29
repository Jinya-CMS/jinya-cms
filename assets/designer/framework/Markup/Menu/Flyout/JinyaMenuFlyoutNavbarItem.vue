<template>
    <li class="jinya-menu-flyout__navbar__item">
        <a class="jinya-menu-flyout__navbar__item__link" href="#" v-jinya-message="text"></a>
        <slot/>
    </li>
</template>

<script>
  export default {
    name: 'jinya-menu-flyout-navbar-item',
    props: {
      isSelected: {
        type: Boolean,
        required: false,
        default() {
          return false;
        },
      },
      text: {
        type: String,
        required: true,
      },
    },
  };
</script>

<style lang="scss">
    .jinya-menu-flyout__navbar__item {
        font-size: 1.2rem;
        list-style: none;
        text-align: center;
        position: relative;
        transition: all 0.3s;
        vertical-align: center;
        display: flex;
        justify-content: center;
        align-items: center;
        padding-top: 1rem;
        padding-bottom: 1rem;

        &:hover {
            color: $primary-darker;

            .jinya-menu-flyout__menu {
                display: flex;
            }

            .jinya-menu-flyout__navbar__item__link {
                text-decoration: none;
                color: $primary-darker;
                outline: none;
            }
        }
    }

    .jinya-menu-flyout__navbar__item__link {
        text-decoration: none;
        padding: 0 1rem;
        color: $primary-light;
        transition: all 0.3s;
    }
</style>
