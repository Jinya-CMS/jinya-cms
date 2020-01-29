<template>
    <div class="jinya-menu__navbar">
        <slot/>
    </div>
</template>

<script>
  export default {
    name: 'jinya-menu-navbar',
  };
</script>

<style lang="scss" scoped>
    .jinya-menu__navbar {
        width: 100%;
        margin: 0;
        background: $white;
        border-bottom: 0.25rem solid $primary;
        box-shadow: 0 4px 6px #00000029;
        display: flex;
        justify-content: flex-start;
        padding: 0;
    }
</style>
