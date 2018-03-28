<template>
    <a v-if="href" :href="href" v-jinya-message="label" class="jinya-button" :class="getAdditionalClasses()"></a>
    <router-link v-else-if="to" :to="routeTarget" v-jinya-message="label" class="jinya-button"
                 :class="getAdditionalClasses()"/>
    <button :type="type" v-else @click="$event => $emit('click', $event)" v-jinya-message="label" class="jinya-button"
            :disabled="isDisabled" :class="getAdditionalClasses()"></button>
</template>

<script>
  import Routes from "../../../router/Routes";
  import ObjectUtils from "../Utils/ObjectUtils";

  export default {
    name: "jinya-button",
    props: {
      href: String,
      to: String,
      params: Object,
      query: Object,
      label: {
        type: String,
        required: true
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
          return 'button'
        }
      }
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
          'is--disabled': this.isDisabled
        };
      }
    },
    computed: {
      routeTarget() {
        return this.to ? {
          name: ObjectUtils.valueByKeypath(Routes, this.to).name,
          params: this.params,
          query: this.query
        } : undefined;
      }
    }
  }
</script>

<style scoped lang="scss">
    .jinya-button {
        border: 2px solid;
        background: $white;
        min-width: 10em;
        display: inline-block;
        padding: 0.5em 1em;
        vertical-align: middle;
        position: relative;
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

        &.is--default {
            background: $gray-600;
            color: $white;
            border-color: $gray-600;

            &:hover {
                background: $white;
                color: $gray-600;
            }

            &.is--inverse {
                background: $white;
                color: $gray-600;
                border-color: $gray-600;

                &:hover {
                    background: $gray-600;
                    color: $white;
                }
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

            &.is--inverse {
                background: $white;
                color: $primary;
                border-color: $primary;

                &:hover {
                    background: $primary;
                    color: $white;
                }
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

            &.is--inverse {
                background: $white;
                color: $secondary;
                border-color: $secondary;

                &:hover {
                    background: $secondary;
                    color: $white;
                }
            }
        }
        &.is--danger {
            background: $danger;
            color: $white;
            border-color: $danger;

            &:hover {
                background: $white;
                color: $danger;
            }

            &.is--inverse {
                background: $white;
                color: $danger;
                border-color: $danger;

                &:hover {
                    background: $danger;
                    color: $white;
                }
            }
        }
        &.is--success {
            background: $success;
            color: $white;
            border-color: $success;

            &:hover {
                background: $white;
                color: $success;
            }

            &.is--inverse {
                background: $white;
                color: $success;
                border-color: $success;

                &:hover {
                    background: $success;
                    color: $white;
                }
            }
        }

        &[disabled],
        &.is--disabled {
            background: $gray-600;
            border-color: $gray-600;
            color: color-yiq($gray-600);
            cursor: not-allowed;

            &.is--inverse {
                background: $white;
                border-color: $gray-600;
                color: $gray-600;
            }

            &:hover {
                background: $gray-600;
                color: $white;

                &.is--inverse {
                    background: $white;
                    border-color: $gray-600;
                    color: $gray-600;
                }
            }
        }
    }
</style>