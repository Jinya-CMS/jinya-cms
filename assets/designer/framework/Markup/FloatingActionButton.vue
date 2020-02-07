<template>
    <router-link :class="additionalClasses" :to="routeTarget" class="jinya-floating-action-button"
                 v-if="routeTarget && !isDisabled">
        <i :class="`mdi-${icon}`" class="mdi"></i>
    </router-link>
    <button :class="additionalClasses" @click="$emit('click')" class="jinya-floating-action-button"
            v-else-if="!routeTarget && !isDisabled">
        <i :class="`mdi-${icon}`" class="mdi"></i>
    </button>
</template>

<script>
  import Routes from '@/router/Routes';
  import ObjectUtils from '@/framework/Utils/ObjectUtils';

  export default {
    name: 'jinya-floating-action-button',
    props: {
      icon: {
        type: String,
        required: true,
      },
      to: {},
      isPrimary: Boolean,
      isSecondary: Boolean,
      isDanger: Boolean,
      isSuccess: Boolean,
      isInverse: Boolean,
      isDisabled: {
        type: Boolean,
        default() {
          return false;
        },
      },
    },
    computed: {
      routeTarget() {
        if (this.to instanceof String) {
          return ObjectUtils.valueByKeypath(Routes, this.to);
        }
        if (this.to instanceof Object) {
          return this.to;
        }

        return undefined;
      },
      additionalClasses() {
        return {
          'is--primary': this.isPrimary,
          'is--secondary': this.isSecondary,
          'is--danger': this.isDanger,
          'is--success': this.isSuccess,
          'is--default': !(this.isSuccess || this.isDanger || this.isPrimary || this.isSecondary),
        };
      },
    },
  };
</script>

<style lang="scss" scoped>
    .jinya-floating-action-button {
        border: 2px solid;
        border-radius: 50%;
        width: 4rem;
        height: 4rem;
        position: fixed;
        display: flex;
        right: 3rem;
        bottom: 3rem;
        font-size: 2em;
        transition: background-color 0.3s, color 0.3s;
        cursor: pointer;
        align-items: center;
        justify-content: center;
        outline: none;

        &:focus {
            outline: none;
        }

        &:hover {
            text-decoration: none;
        }

        &.is--default {
            background: $gray-600;
            color: $white;
            border-color: $gray-600;

            &:hover {
                background: $white;
                color: $gray-600;
            }
        }

        &.is--primary {
            background: $primary;
            color: $white;
            border-color: $primary;

            &:hover {
                background: $white;
                color: $primary;
            }
        }

        &.is--secondary {
            background: $secondary;
            color: $white;
            border-color: $secondary;

            &:hover {
                background: $white;
                color: $secondary;
            }
        }

        &.is--danger {
            background: $negative;
            color: $white;
            border-color: $negative;

            &:hover {
                background: $white;
                color: $negative;
            }
        }

        &.is--positive {
            background: $positive;
            color: $white;
            border-color: $positive;

            &:hover {
                background: $white;
                color: $positive;
            }
        }
    }
</style>
