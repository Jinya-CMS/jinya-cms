<template>
    <jinya-artist-form :artist="artist" :message="message" :is-static="true" :show-password="false"
                       save-label="configuration.general.artists.details.edit" @save="edit"/>
</template>

<script>
  import JinyaArtistForm from '@/components/Configuration/General/Artists/ArtistForm';
  import DOMUtils from '@/framework/Utils/DOMUtils';
  import JinyaRequest from '@/framework/Ajax/JinyaRequest';
  import Translator from '@/framework/i18n/Translator';
  import Routes from '@/router/Routes';

  export default {
    name: 'Details',
    components: { JinyaArtistForm },
    data() {
      return {
        enable: false,
        state: '',
        message: '',
        artist: {
          firstname: '',
          lastname: '',
          enabled: {
            text: '',
            value: false,
          },
          roles: [],
          email: '',
        },
      };
    },
    async mounted() {
      this.state = 'loading';
      this.message = Translator.message('configuration.general.artists.edit.loading');

      const artist = await JinyaRequest.get(`/api/user/${this.$route.params.id}`);
      artist.roles = window.messages.authentication.roles.filter(role => artist.roles.includes(role.value));
      artist.enabled = {
        value: artist.enabled,
        text: artist.enabled
          ? Translator.message('configuration.general.artists.artist_form.enabled')
          : Translator.message('configuration.general.artists.artist_form.disabled'),
      };

      this.artist = artist;
      this.state = '';
      this.message = '';
      this.enable = true;
      DOMUtils.changeTitle(`${artist.firstname} ${artist.lastname}`);
    },
    methods: {
      edit() {
        this.$router.push({
          name: Routes.Configuration.General.Artists.Edit.name,
          params: {
            id: this.$route.params.id,
          },
        });
      },
    },
  };
</script>
