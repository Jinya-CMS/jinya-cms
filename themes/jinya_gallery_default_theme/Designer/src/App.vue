<template>
  <section class="jinya-app">
    <img :src="background" class="jinya-app__background"/>
    <main class="jinya-app__content" @click="hideMenu">
      <div class="jinya-app__router-view">
        <router-view/>
      </div>
    </main>
    <jinya-bug-dialog @close="showBugDialog = false" :show="showBugDialog" v-if="showBugDialog"/>
    <jinya-feature-dialog @close="showFeatureDialog = false" :show="showFeatureDialog" v-if="showFeatureDialog"/>
    <jinya-like-dialog @close="showLikeDialog = false" :show="showLikeDialog" v-if="showLikeDialog"/>
    <jinya-video-uploader/>
    <!-- Dirty hack, otherwise the stacking context breaks, z-index doesn't help... -->
    <nav class="jinya-app__navigation" v-if="$route.name !== loginRoute.name">
      <jinya-menu @show-bug="showBugDialog = true" @show-feature="showFeatureDialog = true"
                  @show-like="showLikeDialog = true"/>
    </nav>
  </section>
</template>

<script>
  import JinyaMenu from '@/components/Navigation/JinyaMenu';
  import Routes from '@/router/Routes';
  import EventBus from '@/framework/Events/EventBus';
  import Events from '@/framework/Events/Events';
  import JinyaBugDialog from '@/components/Support/BugDialog';
  import JinyaFeatureDialog from '@/components/Support/FeatureDialog';
  import JinyaLikeDialog from '@/components/Support/LikeDialog';
  import JinyaVideoUploader from '@/components/Background/VideoUploader';

  export default {
    components: {
      JinyaVideoUploader,
      JinyaLikeDialog,
      JinyaFeatureDialog,
      JinyaBugDialog,
      JinyaMenu,
    },
    name: 'App',
    created() {
      EventBus.$on(Events.navigation.navigated, () => {
        this.background = this.$route.meta.background || window.options.genericBackground;
      });
    },
    methods: {
      hideMenu() {
        EventBus.$emit(Events.navigation.navigated);
      },
    },
    data() {
      return {
        background: this.$route.meta.background || window.options.genericBackground,
        loginRoute: Routes.Account.Login,
        showBugDialog: false,
        showFeatureDialog: false,
        showLikeDialog: false,
      };
    },
  };
</script>

<style lang="scss" scoped>
  .jinya-app {
    height: 100%;
    width: 100%;
    overflow: auto;
    position: relative;

    .jinya-app__navigation {
      position: fixed;
      top: 0;
      left: 0;
      right: 0;
    }

    .jinya-app__background {
      z-index: -1;
      width: 100%;
      position: fixed;
      object-fit: scale-down;
      top: 0;
      left: 0;
    }

    .jinya-app__content {
      height: 100%;
      width: 100%;
    }

    .jinya-app__router-view {
      padding: 0 10% 0;
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
    padding-top: 60px;
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
