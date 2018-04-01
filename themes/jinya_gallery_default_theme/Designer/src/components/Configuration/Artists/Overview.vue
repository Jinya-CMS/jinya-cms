<template>
    <div class="jinya-artist-overview">
        <jinya-loader :loading="loading" v-if="loading"/>
        <jinya-card-list nothing-found="configuration.artists.overview.nothing_found" v-else>
            <template v-for="artist in artists">
                <jinya-card class="jinya-artist" :header="`${artist.firstname} ${artist.lastname}`">
                    <img class="jinya-artist__profile-picture" :src="artist.profilePicture"/>
                    <jinya-card-button :title="'configuration.general.artists.overview.details'|jmessage" type="details"
                                       icon="account-card-details" slot="footer"/>
                    <jinya-card-button :title="'configuration.general.artists.overview.edit'|jmessage" type="edit"
                                       :to="{name: editRoute, params: {id: artist.id}}" icon="account-edit"
                                       v-if="artist.editable" slot="footer"/>
                    <jinya-card-button :title="'configuration.general.artists.overview.enable'|jmessage" type="edit"
                                       icon="account-check" v-if="artist.editable && !artist.enabled" slot="footer"/>
                    <jinya-card-button :title="'configuration.general.artists.overview.disable'|jmessage" type="delete"
                                       icon="account-off" v-if="artist.deletable && artist.enabled" slot="footer"/>
                    <jinya-card-button :title="'configuration.general.artists.overview.delete'|jmessage" type="delete"
                                       icon="account-remove" v-if="artist.deletable" slot="footer"/>
                </jinya-card>
            </template>
        </jinya-card-list>
        <jinya-floating-action-button icon="account-plus" :is-primary="true" to="Configuration.General.Artists.Add"/>
    </div>
</template>

<script>
  import JinyaCardList from "@/components/Framework/Markup/Listing/Card/CardList";
  import JinyaCard from "@/components/Framework/Markup/Listing/Card/Card";
  import JinyaRequest from "@/components/Framework/Ajax/JinyaRequest";
  import JinyaCardButton from "@/components/Framework/Markup/Listing/Card/CardButton";
  import JinyaLoader from "@/components/Framework/Markup/Loader";
  import JinyaFloatingActionButton from "@/components/Framework/Markup/FloatingActionButton";
  import Routes from "@/router/Routes";

  export default {
    name: "Overview",
    components: {
      JinyaFloatingActionButton,
      JinyaLoader,
      JinyaCardButton,
      JinyaCard,
      JinyaCardList
    },
    computed: {
      editRoute() {
        return Routes.Configuration.General.Artists.Edit.name;
      }
    },
    data() {
      return {
        artists: [],
        loading: true
      }
    },
    methods: {
      mapArtists(artist) {
        const item = artist;
        item.editable = artist.email !== this.me.email;
        item.deletable = item.editable && this.artists.filter(a => a.roles.includes('ROLE_SUPER_ADMIN')).length > 1;

        return item;
      }
    },
    async mounted() {
      const artists = await JinyaRequest.get(`/api/user?count=${Number.MAX_SAFE_INTEGER}`);
      const me = await JinyaRequest.get('/api/account');
      this.artists = artists.items;
      this.me = me;
      this.artists = this.artists.map(this.mapArtists);
      this.loading = false;
    }
  }
</script>

<style scoped lang="scss">
    .jinya-artist__profile-picture {
        height: 100%;
        width: 100%;
        object-fit: cover;
    }

    .jinya-artist {
        .jinya-card__body {
            height: 25em;
        }
    }
</style>