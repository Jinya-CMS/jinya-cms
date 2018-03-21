<template>
    <nav class="jinya-navigation-pager">
        <button :disabled="offset === 0" :class="{'is--disabled': offset === 0}"
                class="jinya-navigation-pager__button jinya-navigation-pager__button--previous"
                @click="$emit('previous')" v-jinya-message="'framework.markup.navigation.pager.previous'"></button>
        <span class="jinya-navigation-pager__indicator">{{'framework.markup.navigation.pager.page'|jmessage}} {{currentPage}} {{'framework.markup.navigation.pager.of'|jmessage}} {{pages}}</span>
        <button :disabled="offset + 10 >= count" :class="{'is--disabled': offset + 10 >= count}"
                class="jinya-navigation-pager__button jinya-navigation-pager__button--next"
                @click="$emit('next')" v-jinya-message="'framework.markup.navigation.pager.next'"></button>
    </nav>
</template>

<script>
  export default {
    name: "jinya-pager",
    props: {
      offset: {
        type: Number,
        required: true
      },
      count: {
        type: Number,
        required: true
      }
    },
    data() {
      const pages = (this.count / 10).toFixed(0);

      return {
        pages: pages,
        currentPage: this.offset / 10 + 1
      }
    }
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
            cursor: pointer;
            background: $white;
            color: $primary;
            border: 1px solid $primary;
            min-width: 8em;
            padding: 0.6em;
            outline: none;

            &.is--disabled {
                background: $gray-100;
                border-color: $gray-700;
                color: $gray-700;
                cursor: not-allowed;

                &:hover {
                    background: $gray-100;
                    border-color: $gray-700;
                    color: $gray-700;
                }
            }

            &:hover {
                background: $primary;
                color: $white;
            }

            &:focus {
                outline: none;
            }

            &.jinya-navigation-pager__button--previous {
                align-self: flex-start;
            }

            &.jinya-navigation-pager__button--next {
                align-self: flex-end;
            }
        }

        .jinya-navigation-pager__indicator {
            padding: calc(1em + 1px);
            color: $primary;
        }
    }
</style>