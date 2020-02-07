<template>
    <li :class="{'is--active': active}" class="jinya-menu-flyout__menu__item">
        <a :href="href" @click.prevent="navigated">{{text|jmessage}}</a>
    </li>
</template>

<script>
  import ObjectUtils from '../../../Utils/ObjectUtils';
  import Routes from '@/router/Routes';
  import EventBus from '@/framework/Events/EventBus';
  import Events from '@/framework/Events/Events';

  export default {
    name: 'jinya-menu-flyout-menu-item',
    props: {
      to: {
        type: String,
      },
      text: {
        type: String,
        required: true,
      },
      navigate: {
        type: Boolean,
        default() {
          return true;
        },
      },
      directLink: {
        type: Boolean,
        default() {
          return false;
        },
      },
    },
    computed: {
      href() {
        if (this.directLink) {
          return this.to;
        }
        return this.route.route;
      },
      route() {
        if (ObjectUtils.valueByKeypath(Routes, this.to, false)) {
          return ObjectUtils.valueByKeypath(Routes, this.to);
        }
        return { route: '' };
      },
      active() {
        return this.route.route === window.location.pathname;
      },
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
      },
    },
  };
</script>

<style lang="scss">
    .jinya-menu-flyout__menu__item {
        padding-top: 0.5rem;
        padding-bottom: 0.5rem;

        &.is--active {
            color: $primary-darkest;
        }

        a {
            color: $primary;
            text-decoration: none;
            white-space: nowrap;
        }
    }
</style>
