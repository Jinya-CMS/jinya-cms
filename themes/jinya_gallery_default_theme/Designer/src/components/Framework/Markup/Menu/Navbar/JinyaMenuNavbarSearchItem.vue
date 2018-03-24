<template>
    <div class="jinya-menu-navbar__item--search">
        <!--suppress HtmlFormInputWithoutLabel -->
        <input class="jinya-menu-navbar__item--search__input" type="search" @keyup.enter="sendSearch"
               :aria-labelledby="id" v-model="keyword" :placeholder="'generic.search' | jmessage"/>
        <button class="jinya-menu-navbar__item--search__button" @click.prevent="sendSearch">
            <span class="mdi mdi-magnify"></span>
            <span class="sr--only" :id="id" v-jinya-message="'generic.search'"></span>
        </button>
    </div>
</template>

<script>
  import EventBus from "@/components/Framework/Events/EventBus";

  export default {
    name: "jinya-menu-navbar-search-item",
    data() {
      return {
        keyword: this.$route.query.keyword,
        id: Math.random()
      };
    },
    methods: {
      sendSearch() {
        EventBus.$emit('search-triggered', {
          route: this.$route.name,
          keyword: this.keyword
        });
      }
    }
  }
</script>

<style scoped lang="scss">
    .jinya-menu-navbar__item--search {
        margin-left: auto;
        display: flex;

        .jinya-menu-navbar__item--search__input {
            width: 25em;
            background: $primary;
            border: none;
            padding: 10px;
            font-size: 1em;
            font-family: $font-family;
            color: $white;

            @mixin placeholder {
                color: $primary-lighter;
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
            //noinspection CssInvalidPseudoSelector
            &::-ms-input-placeholder {
                @include placeholder;
            }
            &::placeholder {
                @include placeholder;
            }

            &:focus {
                color: $white;
                outline: none;
            }
        }

        .jinya-menu-navbar__item--search__button {
            width: 60px;
            height: 60px;
            background: transparent;
            color: $primary-lighter;
            border: none;
            font-size: 2em;

            &:hover {
                color: $white;
            }
        }
    }
</style>