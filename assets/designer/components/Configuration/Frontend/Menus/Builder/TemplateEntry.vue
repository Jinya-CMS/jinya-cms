<template>
    <span class="jinya-menu-builder__template-item">{{item.title}}</span>
</template>

<script>
  export default {
    name: 'jinya-menu-builder-template-entry',
    props: {
      item: {
        type: Object,
        required: true,
      },
    },
  };
</script>

<style lang="scss" scoped>
    .jinya-menu-builder__template-item {
        display: block;
        padding: 0.5rem 0.5rem 0.5rem 1rem;
        border: 0.5px solid $white;
        background: $secondary-lighter;
        color: $gray-800;
        border-radius: 3px;
        cursor: move;
    }
</style>
