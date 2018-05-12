<template>
    <div class="jinya-theme">
        <jinya-loader :loading="loading" v-if="loading"/>
        <jinya-card-list nothing-found="impossible" v-else>
            <jinya-card :header="theme.displayName" v-for="theme in themes" class="jinya-card__item--theme"
                        :key="theme.name">
                <img :src="theme.previewImage" class="jinya-theme__preview-image"/>
                <jinya-card-button type="edit" icon="settings" :to="{name: settingsRoute, params: {name: theme.name}}"
                                   slot="footer" :title="'configuration.frontend.themes.overview.settings'|jmessage"/>
                <jinya-card-button type="edit" icon="menu" :to="{name: menusRoute, params: {name: theme.name}}"
                                   slot="footer" :title="'configuration.frontend.themes.overview.menus'|jmessage"/>
                <jinya-card-button type="edit" icon="check" @click="activate(theme)" slot="footer"
                                   :title="'configuration.frontend.themes.overview.activate'|jmessage"/>
                <jinya-card-button type="edit" icon="sass" slot="footer" class="jinya-card-button--variables"
                                   :to="{name: variablesRoute, params: {name: theme.name}}"
                                   :title="'configuration.frontend.themes.overview.variables'|jmessage"/>
            </jinya-card>
        </jinya-card-list>
    </div>
</template>

<script>
  import JinyaCardList from "@/framework/Markup/Listing/Card/CardList";
  import JinyaCard from "@/framework/Markup/Listing/Card/Card";
  import JinyaRequest from "@/framework/Ajax/JinyaRequest";
  import JinyaLoader from "@/framework/Markup/Loader";
  import JinyaCardButton from "@/framework/Markup/Listing/Card/CardButton";
  import Routes from "@/router/Routes";

  export default {
    name: "Overview",
    components: {
      JinyaCardButton,
      JinyaLoader,
      JinyaCard,
      JinyaCardList
    },
    computed: {
      settingsRoute() {
        return Routes.Configuration.Frontend.Theme.Settings.name;
      },
      variablesRoute() {
        return Routes.Configuration.Frontend.Theme.Variables.name;
      },
      menusRoute() {
        return Routes.Configuration.Frontend.Theme.Menus.name;
      }
    },
    data() {
      return {
        themes: [],
        loading: true
      };
    },
    methods: {
      async activate(theme) {
        await JinyaRequest.put(`/api/configuration/${theme.name}`);
      }
    },
    async mounted() {
      const themes = await JinyaRequest.get('/api/theme');
      this.themes = themes.items;
      this.loading = false;
    }
  }
</script>

<style scoped lang="scss">
    .jinya-card-button--variables {
        border-color: $pink;
        color: $pink;

        &:hover {
            color: color-yiq($pink);
            background: $pink;
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