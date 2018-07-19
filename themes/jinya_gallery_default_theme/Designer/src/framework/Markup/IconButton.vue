<template>
    <a v-if="href" :href="href" class="jinya-icon-button" :class="getAdditionalClasses()">
        <i class="mdi" :class="`mdi-${icon}`"></i>
    </a>
    <router-link v-else-if="to" :to="routeTarget" class="jinya-icon-button" :class="getAdditionalClasses()">
        <i class="mdi" :class="`mdi-${icon}`"></i>
    </router-link>
    <button :type="type" v-else @click="$event => $emit('click', $event)" class="jinya-icon-button"
            :disabled="isDisabled" :class="getAdditionalClasses()">
        <i class="mdi" :class="`mdi-${icon}`"></i>
    </button>
</template>

<script>
  import Routes from '@/router/Routes';
  import ObjectUtils from '../Utils/ObjectUtils';

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
    methods: {
      getAdditionalClasses() {
        return {
          'is--primary': this.isDisabled ? false : this.isPrimary,
          'is--secondary': this.isDisabled ? false : this.isSecondary,
          'is--danger': this.isDisabled ? false : this.isDanger,
          'is--success': this.isDisabled ? false : this.isSuccess,
          'is--default': this.isDisabled ? false : !(this.isSuccess || this.isDanger || this.isPrimary || this.isSecondary),
          'is--inverse': true,
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
