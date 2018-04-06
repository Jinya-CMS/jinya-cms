<template>
    <jinya-page-form @save="save" :enable="enable" :message="message" :state="state" :page="page"/>
</template>

<script>
  import JinyaPageForm from "@/components/Static/Pages/SavedInJinya/PageForm";
  import JinyaRequest from "@/framework/Ajax/JinyaRequest";
  import Translator from "@/framework/i18n/Translator";
  import Routes from "@/router/Routes";
  import Timing from "@/framework/Utils/Timing";
  import DOMUtils from "@/framework/Utils/DOMUtils";

  export default {
    name: "edit",
    components: {
      JinyaPageForm
    },
    data() {
      return {
        message: '',
        state: '',
        loading: false,
        enable: true,
        page: {
          title: '',
          slug: '',
          content: ''
        }
      }
    },
    async mounted() {
      this.state = 'loading';
      this.enable = false;
      this.message = Translator.message('static.pages.details.loading');
      try {
        this.page = await JinyaRequest.get(`/api/page/${this.$route.params.slug}`);
        this.state = '';
        this.enable = true;
        DOMUtils.changeTitle(Translator.message('static.pages.edit.title', this.page));
      } catch (error) {
        this.state = 'error';
        this.message = Translator.validator(`static.pages.${error.message}`);
      }
    },
    methods: {
      async save(page) {
        try {
          this.enable = false;
          this.state = 'loading';
          this.message = Translator.message('static.pages.edit.saving', page);

          await JinyaRequest.put(`/api/page/${this.$route.params.slug}`, page);

          this.state = 'success';
          this.message = Translator.message('static.pages.edit.success', page);

          await Timing.wait();
          this.$router.push(Routes.Static.Pages.SavedInJinya.Overview);
        } catch (error) {
          this.message = error.message;
          this.state = 'error';
          this.enable = true;
        }
      }
    }
  }
</script>