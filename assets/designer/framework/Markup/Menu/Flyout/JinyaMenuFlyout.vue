<template>
    <nav class="jinya-menu__flyout">
        <slot name="flyout-navbar"/>
        <slot name="flyout-menus"/>
    </nav>
</template>

<script>
  export default {
    name: 'jinya-menu-flyout',
  };
</script>

<style lang="scss" scoped>
    .jinya-menu__flyout {
        width: 100%;
    }
</style>
