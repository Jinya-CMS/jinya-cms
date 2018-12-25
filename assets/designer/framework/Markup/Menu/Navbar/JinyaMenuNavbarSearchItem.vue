<template>
  <div class="jinya-menu-navbar__item--search">
    <input class="jinya-menu-navbar__item--search__input" type="search" @keyup.enter="sendSearch"
           :aria-labelledby="id" v-model="keyword"
           :placeholder="'framework.markup.menu.navbar.search_item.placeholder' | jmessage"/>
    <button class="jinya-menu-navbar__item--search__button" @click.prevent="sendSearch">
      <span class="mdi mdi-magnify"></span>
      <span class="sr--only" :id="id">{{'generic.search'|jmessage}}</span>
    </button>
  </div>
</template>

<script>
  import EventBus from '@/framework/Events/EventBus';

  export default {
    name: 'jinya-menu-navbar-search-item',
    data() {
      return {
        keyword: this.$route.query.keyword,
        id: Math.random(),
      };
    },
    methods: {
      sendSearch() {
        EventBus.$emit('search-triggered', {
          route: this.$route.name,
          keyword: this.keyword,
        });
      },
    },
  };
</script>

<style scoped lang="scss">
  .jinya-menu-navbar__item--search {
    margin-left: auto;
    display: flex;
    position: relative;
    background: $primary-lighter;

    .jinya-menu-navbar__item--search__input {
      width: 25rem;
      background: $primary-lighter;
      border: none;
      padding: 10px 10px 10px 20px;
      font-size: 1.25em;
      font-family: $font-family;
      color: $primary-darker;
      box-sizing: border-box;

      @mixin placeholder {
        color: $primary-darker;
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
      color: $primary-darker;
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
