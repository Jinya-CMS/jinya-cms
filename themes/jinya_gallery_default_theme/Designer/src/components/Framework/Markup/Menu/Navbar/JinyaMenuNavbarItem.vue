<template>
    <li class="jinya-menu-navbar__item" :class="{ 'is--selected': $route.name === to }">
        <router-link class="jinya-menu-navbar__item__link" :to="{name: to}" v-jinya-message="text"/>
    </li>
</template>

<script>
  export default {
    name: "jinya-menu-navbar-item",
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

    .jinya-menu-navbar__item {
        padding-left: 5px;
        padding-right: 5px;
        line-height: 60px;
        color: $white;

        &.is--selected {
            background: $white;
            color: $primary;
        }

        .jinya-menu-navbar__item__link {
            margin-top: auto;
            margin-bottom: auto;
            color: $white;
            text-decoration: none;
            font-size: 1.2rem;
            font-variant: all-small-caps;

            &:hover {
                background: $white;
                color: $primary;
            }
        }
    }
</style>