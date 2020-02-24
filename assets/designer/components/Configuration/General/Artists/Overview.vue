<template>
    <div class="jinya-artist-overview">
        <jinya-loader :loading="loading" v-if="loading"/>
        <jinya-card-list nothing-found="configuration.general.artists.overview.nothing_found" v-else>
            <jinya-card :header="`${artist.artistName}`" :key="artist.email" class="jinya-artist"
                        v-for="artist in artists">
                <img :src="artist.profilePicture" class="jinya-artist__profile-picture"/>
                <jinya-card-button :title="'configuration.general.artists.overview.details'|jmessage"
                                   :to="{name: artist.me ? profileRoute : detailsRoute, params: {id: artist.id}}"
                                   icon="account-card-details"
                                   slot="footer"
                                   type="details"/>
                <jinya-card-button :title="'configuration.general.artists.overview.edit'|jmessage"
                                   :to="{name: editRoute, params: {id: artist.id}}"
                                   icon="account-edit" slot="footer"
                                   type="edit" v-if="!artist.me"/>
                <jinya-card-button :title="'configuration.general.artists.overview.enable'|jmessage"
                                   @click="showEnable(artist)"
                                   icon="account-check" slot="footer" type="edit"
                                   v-if="!artist.me" v-show="!artist.enabled"/>
                <jinya-card-button :title="'configuration.general.artists.overview.disable'|jmessage"
                                   @click="showDisable(artist)"
                                   icon="account-off" slot="footer" type="delete"
                                   v-if="artist.deletable" v-show="artist.enabled"/>
                <jinya-card-button :title="'configuration.general.artists.overview.delete'|jmessage"
                                   @click="showDelete(artist)"
                                   icon="account-remove" slot="footer"
                                   type="delete" v-if="artist.deletable"/>
            </jinya-card>
        </jinya-card-list>
        <jinya-modal :loading="this.delete.loading"
                     :title="'configuration.general.artists.delete.title'|jmessage(selectedArtist)"
                     @close="closeDeleteModal()"
                     v-if="this.delete.show">
            <jinya-message :message="this.delete.error" slot="message" state="error"
                           v-if="this.delete.error && !this.delete.loading">
                <jinya-message-action-bar v-if="this.delete.creator">
                    <jinya-button :is-danger="true" @click="deactivate"
                                  label="configuration.general.artists.delete.disable"/>
                </jinya-message-action-bar>
            </jinya-message>
            {{'configuration.general.artists.delete.message'|jmessage(selectedArtist)}}
            <jinya-modal-button :closes-modal="true" :is-disabled="this.delete.loading" :is-secondary="true"
                                label="configuration.general.artists.delete.no" slot="buttons-left"/>
            <jinya-modal-button :is-danger="true" :is-disabled="this.delete.loading" @click="remove"
                                label="configuration.general.artists.delete.yes" slot="buttons-right"/>
        </jinya-modal>
        <jinya-modal :loading="this.enable.loading"
                     :title="'configuration.general.artists.enable.title'|jmessage(selectedArtist)"
                     @close="closeEnableModal()" v-if="this.enable.show">
            <jinya-message :message="this.enable.error" slot="message" state="error"
                           v-if="this.enable.error && !this.enable.loading"/>
            {{'configuration.general.artists.enable.message'|jmessage(selectedArtist )}}
            <jinya-modal-button :closes-modal="true" :is-disabled="this.enable.loading" :is-secondary="true"
                                label="configuration.general.artists.enable.no" slot="buttons-left"/>
            <jinya-modal-button :is-disabled="this.enable.loading" :is-success="true" @click="activate"
                                label="configuration.general.artists.enable.yes" slot="buttons-right"/>
        </jinya-modal>
        <jinya-modal :loading="this.disable.loading"
                     :title="'configuration.general.artists.disable.title'|jmessage(selectedArtist)"
                     @close="closeDisableModal()" v-if="this.disable.show">
            <jinya-message :message="this.disable.error" slot="message"
                           state="error"
                           v-if="this.disable.error && !this.disable.loading"/>
            {{'configuration.general.artists.disable.message'|jmessage(selectedArtist )}}
            <jinya-modal-button :closes-modal="true" :is-disabled="this.disable.loading"
                                :is-secondary="true"
                                label="configuration.general.artists.disable.no" slot="buttons-left"/>
            <jinya-modal-button :is-danger="true" :is-disabled="this.disable.loading"
                                @click="deactivate"
                                label="configuration.general.artists.disable.yes" slot="buttons-right"/>
        </jinya-modal>
        <jinya-floating-action-button :is-primary="true" :to="addRoute" icon="account-plus"/>
    </div>
</template>

<script>
  import JinyaCardList from '@/framework/Markup/Listing/Card/CardList';
  import JinyaCard from '@/framework/Markup/Listing/Card/Card';
  import JinyaRequest from '@/framework/Ajax/JinyaRequest';
  import JinyaCardButton from '@/framework/Markup/Listing/Card/CardButton';
  import JinyaLoader from '@/framework/Markup/Waiting/Loader';
  import JinyaFloatingActionButton from '@/framework/Markup/FloatingActionButton';
  import Routes from '@/router/Routes';
  import JinyaModalButton from '@/framework/Markup/Modal/ModalButton';
  import JinyaMessage from '@/framework/Markup/Validation/Message';
  import JinyaModal from '@/framework/Markup/Modal/Modal';
  import JinyaMessageActionBar from '@/framework/Markup/Validation/MessageActionBar';
  import JinyaButton from '@/framework/Markup/Button';
  import EventBus from '@/framework/Events/EventBus';
  import Events from '@/framework/Events/Events';
  import { getCurrentUser } from '@/framework/Storage/AuthStorage';

  export default {
    name: 'Overview',
    components: {
      JinyaFloatingActionButton,
      JinyaModalButton,
      JinyaButton,
      JinyaMessageActionBar,
      JinyaMessage,
      JinyaModal,
      JinyaCardButton,
      JinyaCard,
      JinyaCardList,
      JinyaLoader,
    },
    computed: {
      editRoute() {
        return Routes.Configuration.General.Artists.Edit.name;
      },
      detailsRoute() {
        return Routes.Configuration.General.Artists.Details.name;
      },
      profileRoute() {
        return Routes.MyJinya.Account.Profile.name;
      },
      addRoute() {
        return Routes.Configuration.General.Artists.Add;
      },
    },
    data() {
      return {
        artists: [],
        loading: true,
        selectedArtist: {
          artistName: '',
          id: '',
          email: '',
        },
        delete: {
          loading: false,
          show: false,
          error: '',
          creator: false,
        },
        enable: {
          loading: false,
          show: false,
          error: '',
        },
        disable: {
          loading: false,
          show: false,
          error: '',
        },
      };
    },
    beforeDestroy() {
      EventBus.$off(Events.search.triggered);
    },
    async beforeRouteUpdate(to, from, next) {
      await this.fetchUsers();
      next();
    },
    methods: {
      mapArtists(artist) {
        const item = artist;
        item.me = artist.email === this.me.email;
        item.deletable = !item.roles?.includes('ROLE_SUPER_ADMIN')
          || !item.me
          || this.artists.filter((a) => a.roles.includes('ROLE_SUPER_ADMIN')).length === 1;

        return item;
      },
      showDelete(artist) {
        this.selectedArtist = artist;
        this.delete.show = true;
      },
      closeDeleteModal() {
        this.delete.show = false;
      },
      showDisable(artist) {
        this.selectedArtist = artist;
        this.disable.show = true;
      },
      closeDisableModal() {
        this.disable.show = false;
      },
      showEnable(artist) {
        this.selectedArtist = artist;
        this.enable.show = true;
      },
      closeEnableModal() {
        this.enable.show = false;
      },
      async remove() {
        this.delete.loading = true;
        try {
          await JinyaRequest.delete(`/api/user/${this.selectedArtist.id}`);
          this.delete.show = false;
          this.artists.splice(this.artists.findIndex((artist) => artist.email === this.selectedArtist.email), 1);
          this.artists = this.mapArtists(this.artists);
        } catch (e) {
          this.delete.creator = e.message === 'api.state.409.foreign_key_failed';
          this.delete.error = `configuration.general.artists.delete.${e.message}`;
        }
        this.delete.loading = false;
      },
      async activate() {
        this.enable.loading = true;
        try {
          await JinyaRequest.put(`/api/user/${this.selectedArtist.id}/activation`);
          this.selectedArtist.enabled = true;
          this.artists.splice(this.artists.findIndex(
            (artist) => artist.email === this.selectedArtist.email,
          ), 1, this.selectedArtist);
          this.artists = this.mapArtists(this.artists);
          this.enable.show = false;
        } catch (e) {
          this.enable.error = `configuration.general.artists.enable.${e.message}`;
        }
        this.enable.loading = false;
      },
      async deactivate() {
        this.disable.loading = true;
        try {
          await JinyaRequest.delete(`/api/user/${this.selectedArtist.id}/activation`);
          this.selectedArtist.enabled = false;
          this.artists.splice(
            this.artists.findIndex((artist) => artist.email === this.selectedArtist.email),
            1,
            this.selectedArtist,
          );
          this.artists = this.mapArtists(this.artists);
          this.disable.show = false;
        } catch (e) {
          this.disable.error = `configuration.general.artists.disable.${e.message}`;
        }
        this.disable.loading = false;
      },
      load(target) {
        const url = new URL(target, window.location.href);

        this.$router.push({
          name: Routes.Configuration.Frontend.Menu.Overview.name,
          query: {
            keyword: url.searchParams.get('keyword'),
          },
        });
      },
      async fetchUsers(keyword = '') {
        this.loading = true;
        this.currentUrl = `/api/user?keyword=${keyword}`;

        const artists = await JinyaRequest.get(this.currentUrl);
        this.artists = artists.items.map(this.mapArtists);
        this.loading = false;
      },
    },
    async mounted() {
      const keyword = this.$route.query.keyword || '';

      this.me = getCurrentUser();
      await this.fetchUsers(keyword);

      EventBus.$on(Events.search.triggered, (value) => {
        this.$router.push({
          name: Routes.Configuration.General.Artists.Overview.name,
          query: {
            keyword: value.keyword,
          },
        });
      });
    },
  };
</script>

<style lang="scss" scoped>
    .jinya-artist__profile-picture {
        height: 100%;
        width: 100%;
        object-fit: cover;
    }
</style>
