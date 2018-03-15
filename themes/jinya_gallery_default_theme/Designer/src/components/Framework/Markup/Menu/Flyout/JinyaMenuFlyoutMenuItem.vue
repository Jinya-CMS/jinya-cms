<template>
    <li class="jinya-menu-flyout__menu__item">
        <router-link :to="to" v-jinya-message="text"/>
    </li>
</template>
<script>
  export default {
    name: "jinya-menu-flyout-menu-item",
    props: {
      to: {
        type: String,
        required: true
      },
      text: {
        type: String,
        required: true
      }
    }
  }
</script>

<style scoped lang="scss">
    @import "designer";

    .jinya-menu-flyout__menu__item {
        color: $white;
        margin: 0 0 1em;

        &:last-of-type {
            margin-bottom: 0;
        }

        a {
            color: $white;
            text-decoration: none;
        }
    }
</style>