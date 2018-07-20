<template>
    <transition v-if="isSelected" enter-class="is--entering" leave-to-class="is--leaving">
        <div class="jinya-tab__item">
            <slot/>
        </div>
    </transition>
</template>

<script>
  export default {
    name: 'jinya-tab',
    props: {
      isSelected: {
        type: Boolean,
        default() {
          return false;
        },
      },
    },
  };
</script>

<style scoped lang="scss">
    .jinya-tab__item {
        width: 100%;
        max-height: 100%;
        overflow-y: auto;
        transition: height 0.3s;
        height: auto;

        &.is--entering {
            transition-delay: 0.3s;
        }

        &.is--entering,
        &.is--leaving {
            height: 0;
        }
    }
</style>
