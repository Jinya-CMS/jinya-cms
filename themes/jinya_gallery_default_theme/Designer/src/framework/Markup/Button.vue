<template>
    <a v-if="href" :href="href" v-jinya-message="label" class="jinya-button" :class="getAdditionalClasses()"></a>
    <router-link v-else-if="to" :to="routeTarget" v-jinya-message="label" class="jinya-button"
                 :class="getAdditionalClasses()"/>
    <button :type="type" v-else @click="$event => $emit('click', $event)" v-jinya-message="label" class="jinya-button"
            :disabled="isDisabled" :class="getAdditionalClasses()"></button>
</template>

<script>
  import Routes from '@/router/Routes';
  import ObjectUtils from '../Utils/ObjectUtils';

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
    methods: {
      getAdditionalClasses() {
        return {
          'is--primary': this.isDisabled ? false : this.isPrimary,
          'is--secondary': this.isDisabled ? false : this.isSecondary,
          'is--danger': this.isDisabled ? false : this.isDanger,
          'is--success': this.isDisabled ? false : this.isSuccess,
          'is--default': this.isDisabled ? false : !(this.isSuccess || this.isDanger || this.isPrimary || this.isSecondary),
          'is--inverse': this.isInverse,
          'is--disabled': this.isDisabled,
        };
      },
    },
    computed: {
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

<style scoped lang="scss">
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

        &:focus {
            outline: none;
        }

        &:hover {
            text-decoration: none;
        }

        @include button-variations;
    }
</style>
