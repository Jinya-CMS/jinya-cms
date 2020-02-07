<template>
    <ul class="jinya-menu-flyout__navbar">
        <slot/>
    </ul>
</template>

<script>
  export default {
    name: 'jinya-menu-flyout-navbar',
  };
</script>

<style lang="scss">
    .jinya-menu-flyout__navbar {
        padding: 0;
        display: flex;
        margin: 0 auto 0 1rem;
    }
</style>
