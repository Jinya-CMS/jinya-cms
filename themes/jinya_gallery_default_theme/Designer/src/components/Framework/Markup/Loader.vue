<template>
    <div v-if="loading" class="jinya-loader">
        <div class="jinya-loader__spinner"></div>
    </div>
</template>

<script>
  export default {
    name: "jinya-loader",
    props: {
      loading: {
        type: Boolean,
        default() {
          return false;
        }
      }
    }
  }
</script>

<style scoped lang="scss">
    .jinya-loader {
        display: flex;
        justify-content: center;
        align-items: center;
        height: 100%;
        width: 100%;

        .jinya-loader__spinner {
            position: absolute;
            border: 16px solid transparent;
            border-top-color: $primary-lighter;
            border-right-color: $primary;
            border-bottom-color: $primary-darker;
            border-radius: 50%;
            width: 120px;
            height: 120px;
            animation: spin 0.5s steps(6) infinite;
            margin: auto;

            @keyframes spin {
                from {
                    transform: rotate(0deg);
                }
                to {
                    transform: rotate(360deg);
                }
            }
        }
    }
</style>