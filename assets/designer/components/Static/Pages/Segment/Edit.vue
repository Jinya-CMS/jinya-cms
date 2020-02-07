<template>
    <jinya-page-form :enable="enable" :message="message" :page="page" :state="state" @save="save"/>
</template>

<script>
  import JinyaPageForm from '@/components/Static/Pages/Segment/PageForm';
  import JinyaRequest from '@/framework/Ajax/JinyaRequest';
  import Translator from '@/framework/i18n/Translator';
  import Routes from '@/router/Routes';
  import Timing from '@/framework/Utils/Timing';
  import DOMUtils from '@/framework/Utils/DOMUtils';

  export default {
    name: 'edit',
    components: {
      JinyaPageForm,
    },
    data() {
      return {
        message: '',
        state: '',
        loading: false,
        enable: true,
        page: {
          name: '',
        },
      };
    },
    async mounted() {
      this.state = 'loading';
      this.enable = false;
      this.message = Translator.message('static.pages.segment.edit.loading');
      try {
        this.page = await JinyaRequest.get(`/api/segment_page/${this.$route.params.slug}`);
        this.state = '';
        this.enable = true;
        DOMUtils.changeTitle(Translator.message('static.pages.segment.edit.title', this.page));
      } catch (error) {
        this.state = 'error';
        this.message = Translator.validator(`static.pages.segment.${error.message}`);
      }
    },
    methods: {
      async save(page) {
        try {
          this.enable = false;
          this.state = 'loading';
          this.message = Translator.message('static.pages.segment.edit.saving', page);

          await JinyaRequest.put(`/api/segment_page/${this.page.slug}`, page);

          this.state = 'success';
          this.message = Translator.message('static.pages.segment.edit.success', page);

          await Timing.wait();
          this.$router.push(Routes.Static.Pages.Segment.Overview);
        } catch (error) {
          this.message = Translator.validator(`static.pages.segment.${error.message}`);
          this.state = 'error';
          this.enable = true;
        }
      },
    },
  };
</script>
