<template>
    <nav class="jinya-menu__flyout">
        <slot name="flyout-navbar"/>
        <slot name="flyout-menus"/>
    </nav>
</template>

<script>
  export default {
    name: "jinya-menu-flyout"
  }
</script>

<style scoped lang="scss">
    @import "designer";

    .jinya-menu__flyout {
        width: 100%;
    }
</style>