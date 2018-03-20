<template>
    <nav class="jinya-navigation-pager">
        <button class="jinya-navigation-pager__button jinya-navigation-pager__button--previous"
                @click="$emit('previous')" v-jinya-message="'framework.markup.navigation.pager.previous'"></button>
        <button class="jinya-navigation-pager__button jinya-navigation-pager__button--next"
                @click="$emit('next')" v-jinya-message="'framework.markup.navigation.pager.next'"></button>
    </nav>
</template>

<script>
  export default {
    name: "jinya-pager"
  }
</script>

<style scoped lang="scss">
    .jinya-navigation-pager {
        width: 100%;
        display: flex;
        padding: 1em;
        justify-content: space-between;
        transition: all 0.3s;

        .jinya-navigation-pager__button {
            background: $white;
            color: $primary;
            border: 1px solid $primary;
            min-width: 8em;
            padding: 0.6em;

            &:hover {
                background: $primary;
                color: $white;
            }

            &.jinya-navigation-pager__button--previous {
                align-self: flex-start;
            }

            &.jinya-navigation-pager__button--next {
                align-self: flex-end;
            }
        }
    }
</style>