<template>
    <jinya-video-form :static="true" :video="video" @save="edit" :message="message" :state="state"
                      :hide-on-error="true" save-label="art.videos.details.edit"/>
</template>

<script>
  import JinyaVideoForm from "./VideoForm";
  import JinyaRequest from "@/framework/Ajax/JinyaRequest";
  import Routes from "../../../../router/Routes";
  import Translator from "@/framework/i18n/Translator";
  import DOMUtils from "@/framework/Utils/DOMUtils";

  // noinspection JSUnusedGlobalSymbols
  export default {
    components: {
      JinyaVideoForm
    },
    name: "art-details",
    data() {
      return {
        message: '',
        state: '',
        video: {
          background: '',
          name: '',
          slug: '',
          description: ''
        },
        overviewRoute: Routes.Art.Videos.SavedOnYoutube.Overview.name
      };
    },
    async mounted() {
      this.state = 'loading';
      this.message = Translator.message('art.videos.details.loading');
      try {
        const video = await JinyaRequest.get(`/api/video/${this.$route.params.slug}`);
        this.video = video.item;
        this.state = '';
        DOMUtils.changeTitle(this.video.name);
      } catch (error) {
        this.state = 'error';
        this.message = Translator.validator(`art.videos.${error.message}`);
      }
    },
    methods: {
      edit() {
        this.$router.push({
          name: Routes.Art.Videos.SavedOnYoutube.Edit.name,
          params: {
            slug: this.video.slug
          }
        });
      }
    }
  }
</script>
