<template>
    <section :class="`is--${state}`" class="jinya-message">
        <span class="jinya-message__progress jinya-message__progress--increase" v-if="state === 'loading'"></span>
        <span class="jinya-message__progress" v-if="state === 'loading'"></span>
        <span class="jinya-message__progress jinya-message__progress--decrease" v-if="state === 'loading'"></span>
        <span v-html="realMessage"></span>
        <slot/>
    </section>
</template>

<script>
  import Translator from '@/framework/i18n/Translator';

  export default {
    name: 'jinya-message',
    props: {
      message: {
        type: String,
        required: true,
      },
      params: {
        type: Object,
        default() {
          return {};
        },
      },
      state: {
        type: String,
        default() {
          return 'info';
        },
      },
    },
    computed: {
      realMessage() {
        if (Translator.hasValidator(this.message)) {
          return Translator.validator(this.message, this.params);
        }
        if (Translator.hasMessage(this.message)) {
          return Translator.message(this.message, this.params);
        }
        return this.message;
      },
    },
  };
</script>

<style lang="scss" scoped>
    @mixin message-type($color) {
        background: color-yiq($color);
        color: $color;
        border: 1px solid $color;
        border-top-width: 0.6em;
    }

    .jinya-message {
        padding: 1rem;
        width: 100%;
        box-sizing: border-box;
        border-radius: 4px;
        margin-bottom: 0.5rem;

        &.is--error {
            @include message-type($negative);
        }

        &.is--success {
            @include message-type($positive);
        }

        &.is--info {
            @include message-type($info);
        }

        &.is--primary {
            @include message-type($primary);
        }

        &.is--secondary {
            @include message-type($secondary);
        }

        &.is--loading {
            overflow-x: hidden;
            position: relative;
            padding-top: 1.6em;
            @include message-type($primary);
            border-top-width: 0;

            .jinya-message__progress {
                background: $primary;
                width: 150%;
                height: 0.6em;
                position: absolute;
                opacity: 0.4;
                top: 0;
                left: 0;
                right: 0;
            }

            .jinya-message__progress--increase {
                width: unset;
                animation: increase 2s infinite;
                opacity: 1;

                @keyframes increase {
                    from {
                        left: -5%;
                        width: 5%;
                    }
                    to {
                        left: 130%;
                        width: 100%;
                    }
                }
            }

            .jinya-message__progress--decrease {
                width: unset;
                animation: decrease 2s 0.5s infinite;
                opacity: 1;

                @keyframes decrease {
                    from {
                        left: -80%;
                        width: 80%;
                    }
                    to {
                        left: 110%;
                        width: 10%;
                    }
                }
            }
        }
    }
</style>
