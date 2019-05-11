<template>
  <a :class="additionalClasses" :href="href" class="jinya-icon-button" v-if="href">
    <i :class="`mdi-${icon}`" class="mdi"></i>
  </a>
  <router-link :class="additionalClasses" :to="routeTarget" class="jinya-icon-button" v-else-if="to">
    <i :class="`mdi-${icon}`" class="mdi"></i>
  </router-link>
  <button :class="additionalClasses" :disabled="isDisabled" :type="type" @click="$event => $emit('click', $event)"
          class="jinya-icon-button" v-else>
    <i :class="`mdi-${icon}`" class="mdi"></i>
  </button>
</template>

<script>
  import Routes from '@/router/Routes';
  import ObjectUtils from '@/framework/Utils/ObjectUtils';

  export default {
    name: 'jinya-icon-button',
    props: {
      href: String,
      to: String,
      params: Object,
      query: Object,
      icon: {
        type: String,
        required: true,
      },
      isPrimary: Boolean,
      isSecondary: Boolean,
      isDanger: Boolean,
      isSuccess: Boolean,
      isDisabled: Boolean,
      type: {
        type: String,
        default() {
          return 'button';
        },
      },
    },
    computed: {
      additionalClasses() {
        return {
          'is--primary': !this.isDisabled && this.isPrimary,
          'is--secondary': !this.isDisabled && this.isSecondary,
          'is--danger': !this.isDisabled && this.isDanger,
          'is--success': !this.isDisabled && this.isSuccess,
          'is--default': !this.isDisabled && !(this.isSuccess || this.isDanger || this.isPrimary || this.isSecondary),
          'is--inverse': true,
          'is--disabled': this.isDisabled,
        };
      },
      routeTarget() {
        return this.to ? {
          name: ObjectUtils.valueByKeypath(Routes, this.to).name,
          params: this.params,
          query: this.query,
        } : undefined;
      },
    },
  };
</script>

<style lang="scss" scoped>
  .jinya-icon-button {
    border-width: 0;
    display: inline-block;
    padding: 0.5em 0.5em;
    vertical-align: middle;
    font-size: 100%;
    transition: background-color 0.3s, color 0.3s;
    text-align: center;
    text-decoration: none;
    cursor: pointer;

    &:focus {
      outline: none;
    }

    &:hover {
      text-decoration: none;
    }

    @include button-variations(transparent);
  }
</style>
