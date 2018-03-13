<template>
    <form class="jinya-form" @submit.prevent="$event => $emit('submit', $event)">
        <slot/>
    </form>
</template>

<script>
  export default {
    name: "jinya-form"
  }
</script>

<style scoped lang="scss">
    .jinya-form {
        margin: 0;
        padding: 0;
        font-size: 1rem;
        overflow: auto;
    }
</style>