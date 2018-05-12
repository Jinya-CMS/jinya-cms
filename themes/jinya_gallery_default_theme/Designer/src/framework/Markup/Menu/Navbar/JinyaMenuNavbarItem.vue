<template>
    <div class="jinya-menu-navbar__item"
         :class="{ 'is--selected': $route.name === to, 'is--end': align === 'end', 'is--start': align === 'start' }">
        <router-link class="jinya-menu-navbar__link" :to="routeTarget" v-jinya-message="text"/>
    </div>
</template>

<script>
  import ObjectUtils from "../../../Utils/ObjectUtils";
  import Routes from "@/router/Routes";

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
      },
      align: {
        type: String,
        default() {
          return 'start';
        },
        validate(input) {
          return ['end', 'start'].includes(input);
        }
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
    .jinya-menu-navbar__item {
        line-height: 60px;
        color: $white;
        list-style: none;
        margin: 0;
        background: $primary;
        padding: 0 5px;

        &:hover {
            background: $white;

            .jinya-menu-navbar__link {
                color: $primary;
            }
        }

        &.is--selected {
            background: $white;
            color: $primary;
        }

        &.is--end {
            align-self: flex-end;
        }

        &.is--start {
            align-self: flex-start;
        }

        .jinya-menu-navbar__link {
            margin-top: auto;
            margin-bottom: auto;
            color: $white;
            text-decoration: none;
            font-size: 1.2rem;
            font-variant: all-small-caps;
            display: block;
            cursor: pointer;

            &:hover {
                background: $white;
                color: $primary;
            }
        }
    }
</style>