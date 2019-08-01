<template>
    <jinya-artwork-form :artwork="artwork" :enable="enable" :message="message" :state="state" @save="save"/>
</template>

<script>
  import JinyaArtworkForm from './ArtworkForm';
  import JinyaRequest from '@/framework/Ajax/JinyaRequest';
  import Translator from '@/framework/i18n/Translator';
  import Routes from '@/router/Routes';
  import Timing from '@/framework/Utils/Timing';
  import DOMUtils from '@/framework/Utils/DOMUtils';

  export default {
    components: {
      JinyaArtworkForm,
    },
    data() {
      return {
        message: '',
        state: '',
        loading: false,
        enable: false,
        artwork: {
          picture: '',
          name: '',
          description: '',
        },
      };
    },
    name: 'edit',
    async mounted() {
      this.state = 'loading';
      this.enable = false;
      this.message = Translator.message('art.artworks.details.loading');
      try {
        const artwork = await JinyaRequest.get(`/api/artwork/${this.$route.params.slug}`);
        this.artwork = artwork.item;
        this.state = '';
        this.enable = true;
        DOMUtils.changeTitle(Translator.message('art.artworks.edit.title', this.artwork));
      } catch (error) {
        this.state = 'error';
        this.message = Translator.validator(`art.artworks.${error.message}`);
      }
    },
    methods: {
      async save(artwork) {
        const { picture } = artwork;
        try {
          this.enable = false;
          this.state = 'loading';
          this.message = Translator.message('art.artworks.edit.saving', artwork);

          const savedData = await JinyaRequest.put(`/api/artwork/${this.$route.params.slug}`, {
            name: artwork.name,
            description: artwork.description,
          });

          if (picture) {
            this.message = Translator.message('art.artworks.edit.uploading', artwork);
            await JinyaRequest.upload(`/api/artwork/${savedData.slug}/picture?conversionType=${artwork.type}`, picture);
          }

          this.state = 'success';
          this.message = Translator.message('art.artworks.edit.success', artwork);

          await Timing.wait();
          this.$router.push({
            name: Routes.Art.Artworks.SavedInJinya.Details.name,
            params: {
              slug: savedData.slug,
            },
          });
        } catch (error) {
          this.message = `art.artworks.${error.message}`;
          this.state = 'error';
          this.enable = true;
        }
      },
    },
  };
</script>
