<template>
    <li class="jinya-menu-flyout__menu__item">
        <router-link :to="routeTarget" v-jinya-message="text"/>
    </li>
</template>
<script>
  import ObjectUtils from "../../../Utils/ObjectUtils";
  import Routes from "@/router/Routes";

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
    },
    data() {
      return {
        routeTarget: ObjectUtils.valueByKeypath(Routes, this.to)
      }
    }
  }
</script>

<style scoped lang="scss">
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