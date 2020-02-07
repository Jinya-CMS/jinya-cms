<template>
    <router-link :class="additionalClasses" :disabled="isDisabled" :title="tooltip|jmessage" :to="routeTarget"
                 class="jinya-card-button" v-if="to">
        <span :class="`mdi-${icon}`" class="mdi" v-if="icon"></span>
        <span v-if="text">{{text|jmessage}}</span>
    </router-link>
    <button :class="additionalClasses" :disabled="isDisabled" :title="tooltip|jmessage" @click="$emit('click')"
            class="jinya-card-button" v-else>
        <span :class="`mdi-${icon}`" class="mdi" v-if="icon"></span>
        <span v-if="text">{{text|jmessage}}</span>
    </button>
</template>

<script>
  import ObjectUtils from '@/framework/Utils/ObjectUtils';
  import Routes from '@/router/Routes';

  export default {
    name: 'jinya-card-button',
    props: {
      type: {
        type: String,
        validate(input) {
          return ['details', 'edit', 'delete'].includes(input.toString().toLowerCase());
        },
      },
      icon: {
        type: String,
        validate(input) {
          return this.text || input;
        },
      },
      text: {
        type: String,
        validate(input) {
          return this.icon || input;
        },
      },
      tooltip: {
        type: String,
      },
      to: {},
      isDisabled: Boolean,
    },
    data() {
      const additionalClasses = {};
      additionalClasses[`jinya-card-button--${this.type}`] = this.type;
      additionalClasses['is--disabled'] = this.isDisabled;

      if (this.to instanceof String) {
        return {
          routeTarget: ObjectUtils.valueByKeypath(Routes, this.to),
          additionalClasses,
        };
      }
      if (this.to instanceof Object) {
        return {
          routeTarget: this.to,
          additionalClasses,
        };
      }
      return {
        additionalClasses,
      };
    },
  };
</script>

<style lang="scss" scoped>
    @include card-footer-button('jinya-card-button');
</style>
