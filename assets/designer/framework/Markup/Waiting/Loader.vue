<template>
  <div :class="{'is--mini': mini}" class="jinya-loader" v-if="loading">
    <div class="jinya-loader__spinner"></div>
  </div>
</template>

<script>
  export default {
    name: 'jinya-loader',
    props: {
      loading: {
        type: Boolean,
        default() {
          return false;
        },
      },
      mini: {
        type: Boolean,
        default() {
          return false;
        },
      },
    },
  };
</script>

<style lang="scss" scoped>
  .jinya-loader {
    display: flex;
    justify-content: center;
    align-items: center;
    height: 100%;
    width: 100%;

    .jinya-loader__spinner {
      position: absolute;
      border: 16px solid $gray-300;
      border-top-color: $primary;
      border-radius: 50%;
      width: 10em;
      height: 10em;
      animation: spin 0.8s ease infinite;
      margin: auto;

      &.is--mini {
        width: 3em;
        height: 3em;
      }

      @keyframes spin {
        to {
          transform: rotate(360deg);
        }
      }
    }
  }
</style>
