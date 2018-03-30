<template>
    <router-link :disabled="isDisabled" v-if="to" class="jinya-card-button" :class="additionalClasses"
                 :to="routeTarget">
        <span v-if="icon" class="mdi" :class="`mdi-${icon}`"></span>
        <span v-if="text">{{text|jmessage}}</span>
    </router-link>
    <button :disabled="isDisabled" v-else class="jinya-card-button" :class="additionalClasses" @click="$emit('click')">
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
      to: {},
      isDisabled: Boolean
    },
    data() {
      const additionalClasses = {};
      additionalClasses[`jinya-card-button--${this.type}`] = this.type;
      additionalClasses['is--disabled'] = this.isDisabled;

      if (this.to instanceof String) {
        return {
          routeTarget: ObjectUtils.valueByKeypath(Routes, this.to),
          additionalClasses: additionalClasses
        };
      } else if (this.to instanceof Object) {
        return {
          routeTarget: this.to,
          additionalClasses: additionalClasses
        };
      } else {
        return {
          additionalClasses: additionalClasses
        };
      }
    }
  }
</script>

<style scoped lang="scss">
    @include card-footer-button('jinya-card-button');
</style>