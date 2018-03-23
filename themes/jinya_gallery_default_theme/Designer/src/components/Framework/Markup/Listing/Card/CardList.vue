<template>
    <div class="jinya-card-list">
        <slot/>
    </div>
</template>

<script>
  export default {
    name: "jinya-card-list"
  }
</script>

<style scoped lang="scss">
    .jinya-card-list {
        display: flex;
        flex-flow: row wrap;
        width: 100%;
        overflow: hidden;
        padding: 1% 0;
    }
</style>