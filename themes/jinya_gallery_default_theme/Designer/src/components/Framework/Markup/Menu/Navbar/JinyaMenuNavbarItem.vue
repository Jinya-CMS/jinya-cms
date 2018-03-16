<template>
    <div class="jinya-menu-navbar__item" :class="{ 'is--selected': $route.name === to }">
        <router-link class="jinya-menu-navbar__item__link" :to="{name: to}" v-jinya-message="text"/>
    </div>
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
        line-height: 60px;
        color: $white;
        list-style: none;
        margin: 0;
        background: $primary;
        display: flex;
        flex-direction: column;
        justify-content: center;
        padding: 0 5px;

        a {
            display: block;
            font-variant: all-small-caps;
            cursor: pointer;
        }

        &:hover {
            background: $white;

            a {
                color: $primary;
            }
        }

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