<template>
    <jinya-artist-form @save="save" :enable="enable" :state="state" :message="message"/>
</template>

<script>
  import JinyaArtistForm from "@/components/Configuration/Artists/ArtistForm";
  import JinyaRequest from "@/components/Framework/Ajax/JinyaRequest";
  import Translator from "@/components/Framework/i18n/Translator";
  import Timing from "@/components/Framework/Utils/Timing";
  import Routes from "@/router/Routes";

  export default {
    name: "Add",
    components: {
      JinyaArtistForm
    },
    data() {
      return {
        enable: true,
        message: '',
        state: ''
      }
    },
    methods: {
      async save(artist) {
        const picture = artist.profilePicture;
        try {
          this.enable = false;
          this.state = 'loading';
          this.message = Translator.message('configuration.general.artists.add.saving', {
            firstname: artist.firstname,
            lastname: artist.lastname
          });

          const result = await JinyaRequest.post('/api/user', artist);

          this.message = Translator.message('configuration.general.artists.add.uploading', {
            firstname: artist.firstname,
            lastname: artist.lastname
          });
          await JinyaRequest.upload(`/api/user/${result.id}/profilepicture`, picture);

          this.state = 'success';
          this.message = Translator.message('configuration.general.artists.add.success', {name: artist.name});

          await Timing.wait();
          this.$router.push(Routes.Configuration.General.Artists.Overview);
        } catch (error) {
          this.message = error.message;
          this.state = 'error';
          this.enable = true;
        }
      }
    }
  }
</script>

<style scoped>

</style>