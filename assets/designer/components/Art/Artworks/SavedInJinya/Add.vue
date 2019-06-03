<template>
  <jinya-artwork-form :enable="enable" :message="message" :state="state" @save="save"/>
</template>

<script>
  import JinyaArtworkForm from '@/components/Art/Artworks/SavedInJinya/ArtworkForm';
  import JinyaRequest from '@/framework/Ajax/JinyaRequest';
  import Translator from '@/framework/i18n/Translator';
  import Routes from '@/router/Routes';
  import Timing from '@/framework/Utils/Timing';

  // noinspection JSUnusedGlobalSymbols
  export default {
    components: {
      JinyaArtworkForm,
    },
    data() {
      return {
        message: '',
        state: '',
        loading: false,
        enable: true,
      };
    },
    name: 'add',
    methods: {
      async save(artwork) {
        const { picture } = artwork;
        try {
          this.enable = false;
          this.state = 'loading';
          this.message = Translator.message('art.artworks.add.saving', artwork);

          const savedData = await JinyaRequest.post('/api/artwork', {
            name: artwork.name,
            description: artwork.description,
          });

          this.message = Translator.message('art.artworks.add.uploading', artwork);
          await JinyaRequest.upload(`/api/artwork/${savedData.slug}/picture?conversionType=${artwork.type}`, picture);

          this.state = 'success';
          this.message = Translator.message('art.artworks.add.success', artwork);

          await Timing.wait();
          this.$router.push(Routes.Art.Artworks.SavedInJinya.Overview);
        } catch (error) {
          this.message = `art.artworks.${error.message}`;
          this.state = 'error';
          this.enable = true;
        }
      },
    },
  };
</script>
