<template>
    <div class="jinya-page">
        <jinya-message :message="message" :state="state" v-if="state"/>
        <jinya-loader :loading="loading" v-if="loading"/>
        <iframe :srcdoc="pageHtml" class="jinya-page__preview" v-if="!loading && !state"></iframe>
    </div>
</template>

<script>
  import JinyaRequest from '@/framework/Ajax/JinyaRequest';
  import Translator from '@/framework/i18n/Translator';
  import DOMUtils from '@/framework/Utils/DOMUtils';
  import JinyaLoader from '@/framework/Markup/Waiting/Loader';
  import JinyaMessage from '@/framework/Markup/Validation/Message';
  import EventBus from '@/framework/Events/EventBus';
  import Events from '@/framework/Events/Events';

  export default {
    name: 'PageDetails',
    components: {
      JinyaMessage,
      JinyaLoader,
    },
    data() {
      return {
        pageHtml: '',
        loading: true,
        state: '',
        message: '',
      };
    },
    async mounted() {
      this.loading = true;
      try {
        this.page = await JinyaRequest.get(`/api/segment_page/${this.$route.params.slug}`);
        this.pageHtml = await JinyaRequest.getPlain(`/api/segment_page/${this.$route.params.slug}/preview`);
        DOMUtils.changeTitle(this.page.name);
        EventBus.$emit(Events.header.change, this.page.name);
      } catch (error) {
        this.state = 'error';
        this.message = Translator.validator(error.message);
      }
      this.loading = false;
    },
  };
</script>

<style lang="scss" scoped>
    .jinya-page {
        height: calc(100vh - 4rem);
        padding-top: 1rem;
    }

    .jinya-page__preview {
        width: 100%;
        height: 100%;
        border: 1px solid $primary;
    }
</style>
