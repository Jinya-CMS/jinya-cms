<template>
  <div class="jinya-theme">
    <jinya-message :message="message" :state="state" v-if="state"></jinya-message>
    <jinya-loader :loading="loading" v-if="loading"/>
    <jinya-card-list nothing-found="impossible" v-else>
      <jinya-card :header="theme.displayName" :key="theme.name" class="jinya-card__item--theme" v-for="theme in themes">
        <img :src="theme.previewImage" class="jinya-theme__preview-image"/>
        <jinya-card-button :title="'configuration.frontend.themes.overview.settings'|jmessage"
                           :to="{name: settingsRoute, params: {name: theme.name}}" icon="settings"
                           slot="footer" type="edit"/>
        <jinya-card-button :title="'configuration.frontend.themes.overview.links'|jmessage"
                           :to="{name: linksRoute, params: {name: theme.name}}" icon="link"
                           slot="footer" type="edit"/>
        <jinya-card-button :title="'configuration.frontend.themes.overview.activate'|jmessage" @click="activate(theme)"
                           icon="check" slot="footer"
                           type="edit"/>
        <jinya-card-button :title="'configuration.frontend.themes.overview.variables'|jmessage"
                           :to="{name: variablesRoute, params: {name: theme.name}}" class="jinya-card-button--variables"
                           icon="sass"
                           slot="footer"
                           type="edit"/>
      </jinya-card>
    </jinya-card-list>
  </div>
</template>

<script>
  import JinyaCardList from '@/framework/Markup/Listing/Card/CardList';
  import JinyaCard from '@/framework/Markup/Listing/Card/Card';
  import JinyaRequest from '@/framework/Ajax/JinyaRequest';
  import JinyaLoader from '@/framework/Markup/Waiting/Loader';
  import JinyaCardButton from '@/framework/Markup/Listing/Card/CardButton';
  import Routes from '@/router/Routes';
  import JinyaMessage from '@/framework/Markup/Validation/Message';
  import Translator from '@/framework/i18n/Translator';

  export default {
    name: 'Overview',
    components: {
      JinyaMessage,
      JinyaCardButton,
      JinyaLoader,
      JinyaCard,
      JinyaCardList,
    },
    computed: {
      settingsRoute() {
        return Routes.Configuration.Frontend.Theme.Settings.name;
      },
      variablesRoute() {
        return Routes.Configuration.Frontend.Theme.Variables.name;
      },
      linksRoute() {
        return Routes.Configuration.Frontend.Theme.Links.name;
      },
    },
    data() {
      return {
        themes: [],
        loading: true,
        message: '',
        state: '',
      };
    },
    methods: {
      async activate(theme) {
        try {
          this.state = 'loading';
          this.message = Translator.message('configuration.frontend.themes.overview.activating', theme);

          await JinyaRequest.put(`/api/configuration/${theme.name}`);

          this.state = 'success';
          this.message = Translator.message('configuration.frontend.themes.overview.activated', theme);
        } catch (e) {
          this.state = 'error';
          this.message = Translator.message('configuration.frontend.themes.overview.activation_failed', theme);
        }
      },
    },
    async mounted() {
      const themes = await JinyaRequest.get('/api/theme');
      this.themes = themes.items;
      this.loading = false;
    },
  };
</script>

<style lang="scss" scoped>
  .jinya-theme {
    padding-top: 1rem;
  }

  .jinya-card-button--variables {
    border-color: $pink !important;
    color: $pink !important;

    &:hover {
      color: color-yiq($pink) !important;
      background: $pink !important;
    }
  }

  .jinya-card__item--theme {
    max-width: 15em / 9 * 16;

    &:only-child {
      max-width: 30em / 9 * 16;

      .jinya-theme__preview-image {
        max-width: 30em / 9 * 16;
        width: 30em / 9 * 16;
      }
    }

    .jinya-theme__preview-image {
      max-width: 15em / 9 * 16;
      width: 15em / 9 * 16;
      object-fit: cover;
    }
  }
</style>
