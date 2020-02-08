<template>
    <jinya-artist-form :artist="artist" :enable="enable" :message="message" :show-password="false" :state="state"
                       @save="save"/>
</template>

<script>
  import JinyaArtistForm from '@/components/Configuration/General/Artists/ArtistForm';
  import JinyaRequest from '@/framework/Ajax/JinyaRequest';
  import Translator from '@/framework/i18n/Translator';
  import Timing from '@/framework/Utils/Timing';
  import Routes from '@/router/Routes';
  import DOMUtils from '@/framework/Utils/DOMUtils';

  export default {
    name: 'Edit',
    components: {
      JinyaArtistForm,
    },
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
      artist.roles = window.messages.authentication.roles.filter((role) => artist.roles.includes(role.value));
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
      DOMUtils.changeTitle(Translator.message('configuration.general.artists.edit.title', artist));
    },
    methods: {
      async save(artist) {
        const { profilePicture } = artist;
        try {
          this.enable = false;
          this.state = 'loading';
          this.message = Translator.message('configuration.general.artists.edit.saving', artist);

          await JinyaRequest.put(`/api/user/${this.$route.params.id}`, artist);

          if (profilePicture) {
            this.message = Translator.message('configuration.general.artists.edit.uploading', artist);
            await JinyaRequest.upload(`/api/user/${this.$route.params.id}/profilepicture`, profilePicture);
          }

          this.state = 'success';
          this.message = Translator.message('configuration.general.artists.edit.success', artist);

          await Timing.wait();
          this.$router.push(Routes.Configuration.General.Artists.Overview);
        } catch (error) {
          this.message = Translator.validator(`configuration.general.artists.${error.message}`);
          this.state = 'error';
          this.enable = true;
        }
      },
    },
  };
</script>

<style scoped>

</style>
