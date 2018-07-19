<template>
    <li class="jinya-menu-flyout__menu__item" :class="{'is--active': active}">
        <a :href="href" @click.prevent="navigated">{{text|jmessage}}</a>
    </li>
</template>

<script>
  import ObjectUtils from "../../../Utils/ObjectUtils";
  import Routes from "@/router/Routes";
  import EventBus from "@/framework/Events/EventBus";
  import Events from "@/framework/Events/Events";

  export default {
    name: "jinya-menu-flyout-menu-item",
    props: {
      to: {
        type: String
      },
      text: {
        type: String,
        required: true
      },
      navigate: {
        type: Boolean,
        default() {
          return true;
        }
      },
      directLink: {
        type: Boolean,
        default() {
          return false;
        }
      }
    },
    computed: {
      href() {
        if (this.directLink) {
          return this.to;
        } else {
          return this.route.route;
        }
      },
      route() {
        if (ObjectUtils.valueByKeypath(Routes, this.to, false)) {
          return ObjectUtils.valueByKeypath(Routes, this.to);
        } else {
          return {route: ''};
        }
      },
      active() {
        return this.route.route === window.location.pathname;
      }
    },
    methods: {
      navigated() {
        if (this.navigate && !this.directLink) {
          EventBus.$emit(Events.navigation.navigating);
          this.$router.push(this.route);
          EventBus.$emit(Events.navigation.navigated);
        } else if (this.navigate && this.directLink) {
          window.open(this.href);
        }

        this.$emit('selected');
      }
    }
  }
</script>

<style scoped lang="scss">
    .jinya-menu-flyout__menu__item {
        color: $white;
        margin: 0 0 1em;

        &.is--active {
            &:before {
                content: '\f1c6';
                //noinspection CssNoGenericFontName
                font-family: 'Material Design Icons';
                color: $white;
                transform: rotate(-90deg);
                display: inline-block;
            }
        }

        &:last-of-type {
            margin-bottom: 0;
        }

        a {
            color: $white;
            text-decoration: none;
        }
    }
</style>