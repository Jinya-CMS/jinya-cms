<template>
  <li :class="{'is--selected': isSelected}" @click.prevent="$emit('selected')"
      class="jinya-menu-flyout__navbar__item">
    <a @click.prevent="$emit('selected')" class="jinya-menu-flyout__navbar__item__link" href="#"
       v-jinya-message="text"></a>
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

<style lang="scss" scoped>
  .jinya-menu-flyout__navbar__item {
    color: $white;
    font-size: 1.2rem;
    list-style: none;
    text-align: center;
    cursor: pointer;

    &.is--selected {
      background: $primary;
      color: $white;
      font-size: 1.3rem;

      .jinya-menu-flyout__navbar__item__link {
        text-decoration: none;
        color: $white;
        outline: none;
      }
    }

    .jinya-menu-flyout__navbar__item__link {
      text-decoration: none;
      padding: 0 1em;
      color: $primary;
    }
  }
</style>
