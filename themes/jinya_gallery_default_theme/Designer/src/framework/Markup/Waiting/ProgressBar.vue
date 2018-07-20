<template>
    <div class="jinya-progress-bar">
        <span class="jinya-progress-bar__progress jinya-progress-bar__progress--increase"></span>
        <span class="jinya-progress-bar__progress"></span>
        <span class="jinya-progress-bar__progress jinya-progress-bar__progress--decrease"></span>
    </div>
</template>

<script>
  export default {
    name: 'jinya-progress-bar',
    props: {
      title: {
        type: String,
        required: false,
      },
    },
  };
</script>

<style scoped lang="scss">
    .jinya-progress-bar {
        height: 1rem;
    }

    .jinya-progress-bar__progress {
        background: $primary;
        color: $primary-lighter;
        width: 150%;
        height: 100%;
        position: absolute;
        opacity: 0.4;
        top: 0;
        left: 0;
        right: 0;

        &.jinya-progress-bar__progress--increase {
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

        &.jinya-progress-bar__progress--decrease {
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
</style>
