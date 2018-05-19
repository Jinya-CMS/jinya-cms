<template>
    <section class="jinya-message" :class="`is--${state}`">
        <span class="jinya-message__progress jinya-message__progress--increase" v-if="state === 'loading'"></span>
        <span class="jinya-message__progress" v-if="state === 'loading'"></span>
        <span class="jinya-message__progress jinya-message__progress--decrease" v-if="state === 'loading'"></span>
        <span v-html="message"></span>
        <slot/>
    </section>
</template>

<script>
  export default {
    name: "jinya-message",
    props: {
      message: {
        type: String,
        required: true
      },
      params: {
        type: Object,
        default() {
          return {};
        }
      },
      state: {
        type: String,
        default() {
          return 'info';
        }
      }
    }
  }
</script>

<style scoped lang="scss">
    .jinya-message {
        padding: 1rem;
        width: 100%;

        &.is--error {
            color: $red;
            background: scale_color(pastelize($red), $alpha: 80%);
        }

        &.is--warning {
            color: $yellow;
            background: scale_color(pastelize($yellow), $alpha: 80%);
        }

        &.is--success {
            color: $green;
            background: scale_color(pastelize($green), $alpha: 80%);
        }

        &.is--info {
            color: $cyan;
            background: scale_color(pastelize($cyan), $alpha: 80%);
        }

        &.is--primary {
            color: $primary;
            background: scale_color(pastelize($primary), $alpha: 80%);
        }

        &.is--loading {
            background: scale_color(pastelize($primary), $alpha: 80%);
            overflow-x: hidden;
            position: relative;
            padding-top: 1.6em;
            color: $primary;

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