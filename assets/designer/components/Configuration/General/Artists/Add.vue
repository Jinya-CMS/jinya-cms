<template>
    <jinya-artist-form :enable="enable" :message="message" :show-password="true" :state="state" @save="save"/>
</template>

<script>
  import JinyaArtistForm from '@/components/Configuration/General/Artists/ArtistForm';
  import JinyaRequest from '@/framework/Ajax/JinyaRequest';
  import Translator from '@/framework/i18n/Translator';
  import Timing from '@/framework/Utils/Timing';
  import Routes from '@/router/Routes';

  export default {
    name: 'Add',
    components: {
      JinyaArtistForm,
    },
    data() {
      return {
        enable: true,
        message: '',
        state: '',
      };
    },
    methods: {
      async save(artist) {
        const { profilePicture } = artist;
        try {
          this.enable = false;
          this.state = 'loading';
          this.message = Translator.message('configuration.general.artists.add.saving', artist);

          const result = await JinyaRequest.post('/api/user', artist);

          if (profilePicture) {
            this.message = Translator.message('configuration.general.artists.add.uploading', artist);
            await JinyaRequest.upload(`/api/user/${result.id}/profilepicture`, profilePicture);
          }

          this.state = 'success';
          this.message = Translator.message('configuration.general.artists.add.success', artist);

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
