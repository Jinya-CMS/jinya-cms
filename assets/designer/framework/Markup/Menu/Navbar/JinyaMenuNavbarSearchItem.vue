<template>
    <div class="jinya-menu-navbar__item--search">
        <input :aria-labelledby="id" :placeholder="'framework.markup.menu.navbar.search_item.placeholder' | jmessage"
               @keyup.enter="sendSearch"
               class="jinya-menu-navbar__item--search__input" type="search"
               v-model="keyword"/>
        <button @click.prevent="sendSearch" class="jinya-menu-navbar__item--search__button">
            <span class="mdi mdi-magnify"></span>
            <span :id="id" class="sr--only">{{'generic.search'|jmessage}}</span>
        </button>
    </div>
</template>

<script>
  import EventBus from '@/framework/Events/EventBus';
  import Events from '@/framework/Events/Events';
  import uniqueId from 'lodash/uniqueId';

  export default {
    name: 'jinya-menu-navbar-search-item',
    data() {
      return {
        keyword: this.$route.query.keyword,
        id: uniqueId(),
      };
    },
    mounted() {
      EventBus.$on(Events.search.reset, () => {
        this.keyword = '';
      });
    },
    methods: {
      sendSearch() {
        EventBus.$emit(Events.search.triggered, {
          route: this.$route.name,
          keyword: this.keyword,
        });
      },
    },
  };
</script>

<style lang="scss">
    .jinya-menu-navbar__item--search {
        margin-left: auto;
        display: flex;
        position: relative;
        background: $white;

        .jinya-menu-navbar__item--search__input {
            width: 25rem;
            background: $white;
            border: none;
            padding: 10px 10px 10px 20px;
            font-size: 1.25em;
            font-family: $font-family;
            color: $primary-darkest;
            box-sizing: border-box;

            @mixin placeholder {
                color: $primary-darkest;
                opacity: 1;
            }

            &::-webkit-input-placeholder {
                @include placeholder;
            }

            &::-moz-placeholder {
                @include placeholder;
            }

            &:-moz-placeholder {
                @include placeholder;
            }

            &:-ms-input-placeholder {
                @include placeholder;
            }

            &::-ms-input-placeholder {
                @include placeholder;
            }

            &::placeholder {
                @include placeholder;
            }

            &:focus {
                outline: none;
            }
        }

        .jinya-menu-navbar__item--search__button {
            width: 60px;
            height: 60px;
            background: transparent;
            color: $primary-darkest;
            border: none;
            font-size: 2em;
            cursor: pointer;

            &:hover {
                color: $black;
            }

            &:focus {
                outline: none;
            }
        }
    }
</style>
