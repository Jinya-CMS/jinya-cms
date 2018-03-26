<template>
    <router-link v-if="to" class="jinya-card-button" :class="`jinya-card-button--${type}`" :to="routeTarget">
        <span v-if="icon" class="mdi" :class="`mdi-${icon}`"></span>
        <span v-if="text">{{text|jmessage}}</span>
    </router-link>
    <button v-else class="jinya-card-button" :class="`jinya-card-button--${type}`" @click="$emit('click')">
        <span v-if="icon" class="mdi" :class="`mdi-${icon}`"></span>
        <span v-if="text">{{text|jmessage}}</span>
    </button>
</template>

<script>
  import ObjectUtils from "../../../Utils/ObjectUtils";
  import Routes from "../../../../../router/Routes";

  export default {
    name: "jinya-card-button",
    props: {
      type: {
        type: String,
        validate(input) {
          return ['details', 'edit', 'delete'].includes(input.toString().toLowerCase());
        }
      },
      icon: {
        type: String,
        validate(input) {
          return this.text || input;
        }
      },
      text: {
        type: String,
        validate(input) {
          return this.icon || input;
        }
      },
      to: {}
    },
    data() {
      if (this.to instanceof String) {
        return {
          routeTarget: ObjectUtils.valueByKeypath(Routes, this.to)
        };
      } else if (this.to instanceof Object) {
        return {
          routeTarget: this.to
        };
      } else {
        return {};
      }
    }
  }
</script>

<style scoped lang="scss">
    @include card-footer-button('jinya-card-button');
</style>