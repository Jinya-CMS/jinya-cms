<template>
    <div class="jinya-artist-overview">
        <jinya-loader :loading="loading" v-if="loading"/>
        <jinya-card-list nothing-found="configuration.general.artists.overview.nothing_found" v-else>
            <jinya-card v-for="artist in artists" :key="artist.email" class="jinya-artist"
                        :header="`${artist.firstname} ${artist.lastname}`">
                <img class="jinya-artist__profile-picture" :src="artist.profilePicture"/>
                <jinya-card-button :title="'configuration.general.artists.overview.details'|jmessage" type="details"
                                   :to="{name: artist.me ? profileRoute : detailsRoute, params: {id: artist.id}}"
                                   icon="account-card-details"
                                   slot="footer"/>
                <jinya-card-button :title="'configuration.general.artists.overview.edit'|jmessage" type="edit"
                                   :to="{name: editRoute, params: {id: artist.id}}" icon="account-edit"
                                   v-if="!artist.me" slot="footer"/>
                <jinya-card-button :title="'configuration.general.artists.overview.enable'|jmessage" type="edit"
                                   icon="account-check" @click="showEnable(artist)" v-if="!artist.me"
                                   v-show="!artist.enabled" slot="footer"/>
                <jinya-card-button :title="'configuration.general.artists.overview.disable'|jmessage" type="delete"
                                   icon="account-off" @click="showDisable(artist)" v-if="artist.deletable"
                                   v-show="artist.enabled" slot="footer"/>
                <jinya-card-button :title="'configuration.general.artists.overview.delete'|jmessage" type="delete"
                                   icon="account-remove" @click="showDelete(artist)"
                                   v-if="artist.deletable" slot="footer"/>
            </jinya-card>
        </jinya-card-list>
        <jinya-pager :count="count" :offset="offset" @next="load(control.next)" @previous="load(control.previous)"/>
        <jinya-modal @close="closeDeleteModal()" v-if="this.delete.show" :loading="this.delete.loading"
                     :title="'configuration.general.artists.delete.title'|jmessage(selectedArtist)">
            <jinya-message :message="this.delete.error" state="error" v-if="this.delete.error && !this.delete.loading"
                           slot="message">
                <jinya-message-action-bar v-if="this.delete.creator">
                    <jinya-button label="configuration.general.artists.delete.disable" :is-danger="true"
                                  @click="deactivate"/>
                </jinya-message-action-bar>
            </jinya-message>
            {{'configuration.general.artists.delete.message'|jmessage(selectedArtist )}}
            <jinya-modal-button :is-secondary="true" slot="buttons-left" label="configuration.general.artists.delete.no"
                                :closes-modal="true" :is-disabled="this.delete.loading"/>
            <jinya-modal-button :is-danger="true" slot="buttons-right" label="configuration.general.artists.delete.yes"
                                @click="remove" :is-disabled="this.delete.loading"/>
        </jinya-modal>
        <jinya-modal @close="closeEnableModal()"
                     :title="'configuration.general.artists.enable.title'|jmessage(selectedArtist)"
                     v-if="this.enable.show" :loading="this.enable.loading">
            <jinya-message :message="this.enable.error" state="error" v-if="this.enable.error && !this.enable.loading"
                           slot="message"/>
            {{'configuration.general.artists.enable.message'|jmessage(selectedArtist )}}
            <jinya-modal-button :is-secondary="true" slot="buttons-left" label="configuration.general.artists.enable.no"
                                :closes-modal="true" :is-disabled="this.enable.loading"/>
            <jinya-modal-button :is-success="true" slot="buttons-right" label="configuration.general.artists.enable.yes"
                                @click="activate" :is-disabled="this.enable.loading"/>
        </jinya-modal>
        <jinya-modal @close="closeDisableModal()"
                     :title="'configuration.general.artists.disable.title'|jmessage(selectedArtist)"
                     v-if="this.disable.show" :loading="this.disable.loading">
            <jinya-message :message="this.disable.error" state="error"
                           v-if="this.disable.error && !this.disable.loading"
                           slot="message"/>
            {{'configuration.general.artists.disable.message'|jmessage(selectedArtist )}}
            <jinya-modal-button :is-secondary="true" slot="buttons-left"
                                label="configuration.general.artists.disable.no"
                                :closes-modal="true" :is-disabled="this.disable.loading"/>
            <jinya-modal-button :is-danger="true" slot="buttons-right"
                                label="configuration.general.artists.disable.yes"
                                @click="deactivate" :is-disabled="this.disable.loading"/>
        </jinya-modal>
        <jinya-floating-action-button icon="account-plus" :is-primary="true" :to="addRoute"/>
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
  import JinyaPager from '@/framework/Markup/Listing/Pager';
  import EventBus from '@/framework/Events/EventBus';
  import Events from '@/framework/Events/Events';
  import { getCurrentUser } from '@/framework/Storage/AuthStorage';

  export default {
    name: 'Overview',
    components: {
      JinyaPager,
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
        offset: 0,
        count: 0,
        control: {
          next: '',
          previous: '',
        },
        artists: [],
        loading: true,
        selectedArtist: {
          firstname: '',
          lastname: '',
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
      await this.fetchUsers(to.query.offset || 0, to.query.count || 10, to.query.keyword || '');
      next();
    },
    methods: {
      mapArtists(artist) {
        const item = artist;
        item.me = artist.email === this.me.email;
        item.deletable = !item.roles.includes('ROLE_SUPER_ADMIN')
          || !item.me
          || this.artists.filter(a => a.roles.includes('ROLE_SUPER_ADMIN')).length === 1;

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
          this.artists.splice(this.artists.findIndex(artist => artist.email === this.selectedArtist.email), 1);
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
            artist => artist.email === this.selectedArtist.email,
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
          this.artists.splice(this.artists.findIndex(
            artist => artist.email === this.selectedArtist.email,
          ), 1, this.selectedArtist);
          this.artists = this.mapArtists(this.artists);
          this.disable.show = false;
          this.delete.show = false;
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
            offset: url.searchParams.get('offset'),
            count: url.searchParams.get('count'),
            keyword: url.searchParams.get('keyword'),
          },
        });
      },
      async fetchUsers(offset = 0, count = 10, keyword = '') {
        this.loading = true;
        this.currentUrl = `/api/user?offset=${offset}&count=${count}&keyword=${keyword}`;

        const artists = await JinyaRequest.get(this.currentUrl);
        this.artists = artists.items.map(this.mapArtists);
        this.control = artists.control;
        this.count = artists.count;
        this.offset = artists.offset;
        this.loading = false;
      },
    },
    async mounted() {
      const offset = this.$route.query.offset || 0;
      const count = this.$route.query.count || 10;
      const keyword = this.$route.query.keyword || '';

      this.me = getCurrentUser();
      await this.fetchUsers(offset, count, keyword);

      EventBus.$on(Events.search.triggered, (value) => {
        this.$router.push({
          name: Routes.Configuration.General.Artists.Overview.name,
          query: {
            offset: 0,
            count: this.$route.query.count,
            keyword: value.keyword,
          },
        });
      });
    },
  };
</script>

<style scoped lang="scss">
    .jinya-artist__profile-picture {
        height: 100%;
        width: 100%;
        object-fit: cover;
    }
</style>
