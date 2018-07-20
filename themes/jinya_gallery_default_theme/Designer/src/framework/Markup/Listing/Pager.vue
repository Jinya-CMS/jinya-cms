<template>
    <nav class="jinya-navigation-pager" v-if="!(disable.previous && disable.next)">
        <jinya-button :is-disabled="disable.previous" label="framework.markup.navigation.pager.previous"
                      :is-primary="true" :is-inverse="true"
                      class="jinya-navigation-pager__button jinya-navigation-pager__button--previous"
                      @click="$emit('previous')"/>
        {{'framework.markup.navigation.pager.page_indicator'|jmessage({page: currentPage, count: pages})}}
        <jinya-button class="jinya-navigation-pager__button jinya-navigation-pager__button--next" :is-primary="true"
                      :is-inverse="true" label="framework.markup.navigation.pager.next" @click="$emit('next')"
                      :is-disabled="disable.next"/>
    </nav>
</template>

<script>
  import JinyaButton from '@/framework/Markup/Button';

  export default {
    name: 'jinya-pager',
    components: { JinyaButton },
    props: {
      offset: {
        type: Number,
        required: true,
      },
      count: {
        type: Number,
        required: true,
      },
    },
    data() {
      const pages = Math.ceil((this.count / 10.0));

      return {
        pages,
        currentPage: this.offset / 10 + 1,
        disable: {
          next: this.offset + 10 >= this.count,
          previous: this.offset === 0,
        },
      };
    },
  };
</script>

<style scoped lang="scss">
    .jinya-navigation-pager {
        width: 100%;
        display: flex;
        padding: 1em;
        justify-content: space-between;
        align-items: center;
        transition: all 0.3s;
        color: $primary;

        .jinya-navigation-pager__button {
            min-width: 8em;
            padding: 0.6em;

            &.jinya-navigation-pager__button--previous {
                align-self: flex-start;
            }

            &.jinya-navigation-pager__button--next {
                align-self: flex-end;
            }
        }
    }
</style>
