<template>
    <section class="jinya-app">
        <nav class="jinya-app__navigation" v-if="$route.name !== loginRoute.name">
            <jinya-menu/>
        </nav>
        <img :src="background" class="jinya-app__background"/>
        <main class="jinya-app__content">
            <router-view/>
        </main>
    </section>
</template>

<script>
  import JinyaMenu from "@/components/Framework/Markup/Menu/JinyaMenu";
  import Routes from "./router/Routes";
  import EventBus from "./components/Framework/Events/EventBus";
  import Events from "./components/Framework/Events/Events";

  export default {
    components: {JinyaMenu},
    name: 'App',
    created() {
      EventBus.$on(Events.navigation.navigated, () => {
        this.background = this.$route.meta.background || window.options.genericBackground;
      });
    },
    data() {
      return {
        background: this.$route.meta.background || window.options.genericBackground,
        loginRoute: Routes.Account.Login
      };
    }
  }
</script>

<style lang="scss" scoped>
    .jinya-app {
        height: 100%;
        width: 100%;
        overflow: auto;

        .jinya-app__content {
            padding: 0 10%;
        }

        .jinya-app__background {
            z-index: -1;
            height: 100%;
            width: 100%;
            position: fixed;
            top: 0;
            left: 0;
        }
    }
</style>
<style lang="scss">
    * {
        box-sizing: border-box;
    }

    body {
        font-family: $font-family;
        margin: 0;
        overflow: auto;
    }

    a {
        color: $primary;

        &:hover {
            color: $primary-darker;
        }
    }

    h1, h2, h3, h4, h5, h6 {
        color: $primary;
        font-family: $font-family-heading;
    }

    h1 {
        font-size: 2em;
    }

    h2 {
        font-size: 1.8em;
    }

    h3 {
        font-size: 1.6em;
    }

    h4 {
        font-size: 1.4em;
    }

    h5 {
        font-size: 1.2em;
    }

    h6 {
        font-size: 1em;
        font-weight: bold;
    }

    .sr--only {
        display: none;

        @media speech {
            display: inline-block;
        }
    }
</style>