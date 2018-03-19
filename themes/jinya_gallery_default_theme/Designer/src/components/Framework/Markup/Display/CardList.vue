<template>
    <section class="jinya-card__list">
        <slot/>
    </section>
</template>

<script>
  export default {
    name: "jinya-card-list"
  }
</script>

<style scoped lang="scss">
    .jinya-card__list {
        display: flex;
        flex-flow: row wrap;
        width: 100%;
        overflow: hidden;
        padding: 1% 0;
    }
</style>