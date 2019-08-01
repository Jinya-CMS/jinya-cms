<template>
    <a :class="additionalClasses" :href="href" class="jinya-button" v-if="href" v-jinya-message="label"></a>
    <router-link :class="additionalClasses" :to="routeTarget" class="jinya-button" v-else-if="to"
                 v-jinya-message="label"/>
    <button :class="additionalClasses" :disabled="isDisabled" :type="type" @click="$event => $emit('click', $event)"
            class="jinya-button"
            v-else v-jinya-message="label"></button>
</template>

<script>
  import Routes from '@/router/Routes';
  import ObjectUtils from '@/framework/Utils/ObjectUtils';

  export default {
    name: 'jinya-button',
    props: {
      href: String,
      to: String,
      params: Object,
      query: Object,
      label: {
        type: String,
        required: true,
      },
      isPrimary: Boolean,
      isSecondary: Boolean,
      isDanger: Boolean,
      isSuccess: Boolean,
      isInverse: Boolean,
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
          'is--inverse': this.isInverse,
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
    .jinya-button {
        border: 2px solid;
        background: $white;
        min-width: 10em;
        display: inline-block;
        padding: 0.5em 1em;
        vertical-align: middle;
        font-size: 100%;
        transition: background-color 0.3s, color 0.3s;
        text-align: center;
        text-decoration: none;
        cursor: pointer;
        font-family: $font-family;
        font-weight: normal;

        &:focus {
            outline: none;
        }

        &:hover {
            text-decoration: none;
        }

        @include button-variations;
    }
</style>
