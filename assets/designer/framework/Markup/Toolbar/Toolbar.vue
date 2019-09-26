<template>
    <div class="jinya__toolbar">
        <slot/>
    </div>
</template>

<script>
  export default {
    name: 'JinyaToolbar',
  };
</script>

<style lang="scss" scoped>
    .jinya__toolbar {
        width: 100%;
        display: flex;
    }
</style>
