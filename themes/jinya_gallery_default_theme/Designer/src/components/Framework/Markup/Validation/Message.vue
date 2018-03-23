<template>
    <section class="jinya-message" :class="`is--${state}`">
        {{message|jvalidator(params)}}
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
    }
</style>