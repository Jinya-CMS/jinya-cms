<template>
    <jinya-loader :loading="loading" v-if="loading"/>
    <div class="jinya-menus" v-else>
        <jinya-message :message="message" :state="state" v-if="!loading"/>
        <jinya-card-list nothing-found="configuration.frontend.menus.overview.nothing_found" v-if="!loading">
            <jinya-card :header="menu.name" :key="menu.id" class="jinya-card--menu" v-for="menu in menus">
                <img :src="menu.logo" class="jinya-menu__logo" v-if="menu.logo"/>
                <jinya-card-button :title="'configuration.frontend.menus.overview.edit'|jmessage"
                                   :to="{name: editRoute, params: {id: menu.id}}" icon="pencil" slot="footer"
                                   type="edit"/>
                <jinya-card-button :title="'configuration.frontend.menus.overview.editor'|jmessage"
                                   :to="{name: editorRoute, params: {id: menu.id}}" icon="menu" slot="footer"
                                   type="edit"/>
                <jinya-card-button :title="'configuration.frontend.menus.overview.delete'|jmessage"
                                   @click="showDelete(menu)" icon="delete" slot="footer" type="delete"/>
            </jinya-card>
        </jinya-card-list>
        <jinya-modal :loading="this.delete.loading"
                     :title="'configuration.frontend.menus.delete.title'|jmessage(selectedMenu)"
                     @close="closeDeleteModal()" v-if="this.delete.show">
            <jinya-message :message="this.delete.error" slot="message" state="error"
                           v-if="this.delete.error && !this.delete.loading"/>
            {{'configuration.frontend.menus.delete.message'|jmessage(selectedMenu)}}
            <jinya-modal-button :closes-modal="true" :is-disabled="this.delete.loading" :is-secondary="true"
                                label="configuration.frontend.menus.delete.no" slot="buttons-left"/>
            <jinya-modal-button :is-danger="true" :is-disabled="this.delete.loading" @click="remove"
                                label="configuration.frontend.menus.delete.yes" slot="buttons-right"/>
        </jinya-modal>
        <jinya-floating-action-button :is-primary="true" :to="addRoute" icon="plus"/>
    </div>
</template>

<script>
  import JinyaCardList from '@/framework/Markup/Listing/Card/CardList';
  import JinyaMessage from '@/framework/Markup/Validation/Message';
  import JinyaLoader from '@/framework/Markup/Waiting/Loader';
  import JinyaCard from '@/framework/Markup/Listing/Card/Card';
  import JinyaRequest from '@/framework/Ajax/JinyaRequest';
  import JinyaCardButton from '@/framework/Markup/Listing/Card/CardButton';
  import Routes from '@/router/Routes';
  import EventBus from '@/framework/Events/EventBus';
  import Events from '@/framework/Events/Events';
  import JinyaModal from '@/framework/Markup/Modal/Modal';
  import JinyaModalButton from '@/framework/Markup/Modal/ModalButton';
  import JinyaFloatingActionButton from '@/framework/Markup/FloatingActionButton';

  export default {
    name: 'Overview',
    components: {
      JinyaFloatingActionButton,
      JinyaModalButton,
      JinyaModal,
      JinyaCardButton,
      JinyaCard,
      JinyaLoader,
      JinyaMessage,
      JinyaCardList,
    },
    async mounted() {
      const keyword = this.$route.query.keyword || '';
      await this.fetchMenus(keyword);

      EventBus.$on(Events.search.triggered, (value) => {
        this.$router.push({
          name: Routes.Configuration.Frontend.Menu.Overview.name,
          query: {
            keyword: value.keyword,
          },
        });
      });
    },
    beforeDestroy() {
      EventBus.$off(Events.search.triggered);
    },
    async beforeRouteUpdate(to, from, next) {
      await this.fetchMenus(to.query.keyword || '');
      next();
    },
    methods: {
      load(target) {
        const url = new URL(target, window.location.href);

        this.$router.push({
          name: Routes.Configuration.Frontend.Menu.Overview.name,
          query: {
            keyword: url.searchParams.get('keyword'),
          },
        });
      },
      async fetchMenus(keyword = '') {
        this.loading = true;
        this.currentUrl = `/api/menu?keyword=${keyword}`;

        const value = await JinyaRequest.get(this.currentUrl);
        this.menus = value.items;
        this.loading = false;
      },
      showDelete(menu) {
        this.selectedMenu = menu;
        this.delete.show = true;
      },
      closeDeleteModal() {
        this.delete.show = false;
      },
      async remove() {
        this.delete.loading = true;
        try {
          await JinyaRequest.delete(`/api/menu/${this.selectedMenu.id}`);
          this.delete.show = false;
          this.menus.splice(this.menus.findIndex((menu) => menu.id === this.selectedMenu.id), 1);
        } catch (e) {
          this.delete.error = `configuration.general.artists.delete.${e.message}`;
        }

        this.delete.loading = false;
      },
    },
    computed: {
      addRoute() {
        return Routes.Configuration.Frontend.Menu.Add;
      },
      editRoute() {
        return Routes.Configuration.Frontend.Menu.Edit.name;
      },
      editorRoute() {
        return Routes.Configuration.Frontend.Menu.Builder.name;
      },
    },
    data() {
      return {
        message: '',
        state: '',
        loading: false,
        menus: [],
        selectedMenu: {
          name: '',
        },
        delete: {
          loading: false,
          show: false,
          error: '',
        },
      };
    },
  };
</script>

<style lang="scss" scoped>
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
