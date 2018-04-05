<template>
    <div class="jinya-menus">
        <jinya-loader :loading="loading"/>
        <jinya-message :message="message" :state="state" v-if="!loading"/>
        <jinya-card-list nothing-found="configuration.frontend.menus.overview.nothing_found" v-if="!loading">
            <jinya-card v-for="menu in menus" :header="menu.name" class="jinya-card--menu">
                <img class="jinya-menu__logo" :src="menu.logo"/>
                <jinya-card-button slot="footer" type="edit" icon="pencil"
                                   :to="{name: editRoute, params: {id: menu.id}}"
                                   :title="'configuration.frontend.menus.overview.edit'|jmessage"/>
                <jinya-card-button slot="footer" type="edit" icon="menu"
                                   :to="{name: editorRoute, params: {id: menu.id}}"
                                   :title="'configuration.frontend.menus.overview.editor'|jmessage"/>
                <jinya-card-button slot="footer" type="delete" icon="delete"
                                   :title="'configuration.frontend.menus.overview.delete'|jmessage"/>
            </jinya-card>
        </jinya-card-list>
        <jinya-pager :count="count" :offset="offset" @next="load(control.next)" @previous="load(control.previous)"/>
    </div>
</template>

<script>
  import JinyaCardList from "@/framework/Markup/Listing/Card/CardList";
  import JinyaMessage from "@/framework/Markup/Validation/Message";
  import JinyaLoader from "@/framework/Markup/Loader";
  import JinyaCard from "@/framework/Markup/Listing/Card/Card";
  import JinyaRequest from "@/framework/Ajax/JinyaRequest";
  import JinyaCardButton from "@/framework/Markup/Listing/Card/CardButton";
  import JinyaPager from "@/framework/Markup/Listing/Pager";
  import Routes from "@/router/Routes";
  import EventBus from "@/framework/Events/EventBus";
  import Events from "@/framework/Events/Events";

  export default {
    name: "Overview",
    components: {
      JinyaPager,
      JinyaCardButton,
      JinyaCard,
      JinyaLoader,
      JinyaMessage,
      JinyaCardList
    },
    async mounted() {
      const offset = this.$route.query.offset || 0;
      const count = this.$route.query.count || 10;
      const keyword = this.$route.query.keyword || '';
      await this.fetchMenus(offset, count, keyword);

      EventBus.$on(Events.search.triggered, value => {
        this.$router.push({
          name: Routes.Configuration.Frontend.Menu.Overview.name,
          query: {
            offset: 0,
            count: this.$route.query.count,
            keyword: value.keyword
          }
        });
      });
    },
    beforeDestroy() {
      EventBus.$off(Events.search.triggered);
    },
    async beforeRouteUpdate(to, from, next) {
      await this.fetchMenus(to.query.offset || 0, to.query.count || 10, to.query.keyword || '');
      next();
    },
    methods: {
      load(target) {
        const url = new URL(target, location.href);

        this.$router.push({
          name: Routes.Configuration.Frontend.Menu.Overview.name,
          query: {
            offset: url.searchParams.get('offset'),
            count: url.searchParams.get('count'),
            keyword: url.searchParams.get('keyword')
          }
        });
      },
      async fetchMenus(offset = 0, count = 10, keyword = '') {
        this.loading = true;
        this.currentUrl = `/api/menu?offset=${offset}&count=${count}&keyword=${keyword}`;

        const value = await JinyaRequest.get(this.currentUrl);
        this.menus = value.items;
        this.control = value.control;
        this.count = value.count;
        this.offset = value.offset;
        this.loading = false;
      }
    },
    computed: {
      editRoute() {
        return Routes.Configuration.Frontend.Menu.Edit.name;
      },
      editorRoute() {
        return Routes.Configuration.Frontend.Menu.Editor.name;
      }
    },
    data() {
      return {
        message: '',
        state: '',
        loading: false,
        menus: [],
        offset: 0,
        count: 0,
        control: {
          next: '',
          previous: ''
        }
      };
    }
  }
</script>

<style scoped lang="scss">
    .jinya-card--menu {
        .jinya-card__body {
            display: flex;
            justify-content: center;
        }
    }

    .jinya-menu__logo {
        height: 100%;
        width: auto;
        object-fit: cover;
    }
</style>